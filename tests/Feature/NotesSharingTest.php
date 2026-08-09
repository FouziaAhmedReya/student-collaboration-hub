<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Tests\TestCase;

class NotesSharingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_notes_can_be_searched_and_filtered(): void
    {
        $networkNote = $this->makeNote([
            'title' => 'Computer Networks TCP Notes',
            'department' => 'CSE',
            'course' => 'CSE421',
        ]);
        $calculusNote = $this->makeNote([
            'title' => 'Differential Calculus',
            'department' => 'Mathematics',
            'course' => 'MAT110',
            'public_id' => 'notes/calculus',
        ]);

        $response = $this->get(route('notes.index', [
            'search' => 'Networks',
            'department' => 'CSE',
        ]));

        $response->assertOk()
            ->assertSee($networkNote->title)
            ->assertDontSee($calculusNote->title);
    }

    public function test_a_note_upload_is_saved_with_cloudinary_metadata(): void
    {
        $cloudinary = $this->mock(CloudinaryService::class, function (MockInterface $mock) {
            $mock->shouldReceive('upload')->once()->andReturn([
                'public_id' => 'student-collaboration-hub/notes/network-notes',
                'secure_url' => 'https://res.cloudinary.com/demo/raw/upload/network-notes.pdf',
                'resource_type' => 'raw',
                'format' => 'pdf',
                'bytes' => 204800,
            ]);
        });
        $this->app->instance(CloudinaryService::class, $cloudinary);

        $response = $this->post(route('notes.store'), [
            'title' => 'TCP and IP Lecture Notes',
            'description' => 'Transport and network layer concepts.',
            'department' => 'Computer Science and Engineering',
            'course' => 'CSE421 Computer Networks',
            'semester' => 'Spring 2026',
            'file' => UploadedFile::fake()->create('network-notes.pdf', 200, 'application/pdf'),
        ]);

        $response->assertRedirect(route('notes.index'));
        $this->assertDatabaseHas('notes', [
            'title' => 'TCP and IP Lecture Notes',
            'public_id' => 'student-collaboration-hub/notes/network-notes',
            'bytes' => 204800,
        ]);
    }

    public function test_note_metadata_can_be_edited_without_replacing_the_file(): void
    {
        $note = $this->makeNote();
        $cloudinary = $this->mock(CloudinaryService::class);
        $cloudinary->shouldNotReceive('upload');
        $cloudinary->shouldNotReceive('destroy');
        $this->app->instance(CloudinaryService::class, $cloudinary);

        $response = $this->put(route('notes.update', $note), [
            'title' => 'Updated Network Notes',
            'description' => 'Updated description.',
            'department' => 'CSE',
            'course' => 'CSE421',
            'semester' => 'Summer 2026',
        ]);

        $response->assertRedirect(route('notes.index'));
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Updated Network Notes',
            'public_id' => 'notes/original',
        ]);
    }

    public function test_replacing_a_file_uploads_the_new_file_and_removes_the_old_one(): void
    {
        $note = $this->makeNote();
        $cloudinary = $this->mock(CloudinaryService::class, function (MockInterface $mock) {
            $mock->shouldReceive('upload')->once()->andReturn([
                'public_id' => 'notes/replacement',
                'secure_url' => 'https://res.cloudinary.com/demo/raw/upload/replacement.pdf',
                'resource_type' => 'raw',
                'format' => 'pdf',
                'bytes' => 102400,
            ]);
            $mock->shouldReceive('destroy')->once()->with('notes/original', 'raw');
        });
        $this->app->instance(CloudinaryService::class, $cloudinary);

        $response = $this->put(route('notes.update', $note), [
            'title' => $note->title,
            'description' => $note->description,
            'department' => $note->department,
            'course' => $note->course,
            'semester' => $note->semester,
            'file' => UploadedFile::fake()->create('replacement.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('notes.index'));
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'public_id' => 'notes/replacement',
            'original_name' => 'replacement.pdf',
        ]);
    }

    public function test_deleting_a_note_also_deletes_its_cloudinary_file(): void
    {
        $note = $this->makeNote();
        $cloudinary = $this->mock(CloudinaryService::class, function (MockInterface $mock) {
            $mock->shouldReceive('destroy')->once()->with('notes/original', 'raw');
        });
        $this->app->instance(CloudinaryService::class, $cloudinary);

        $response = $this->delete(route('notes.destroy', $note));

        $response->assertRedirect(route('notes.index'));
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_downloading_returns_an_attachment_and_increments_the_counter(): void
    {
        $note = $this->makeNote();
        Http::fake([
            $note->secure_url => Http::response('pdf-content', 200, ['Content-Type' => 'application/pdf']),
        ]);

        $response = $this->get(route('notes.download', $note));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename=network-notes.pdf');
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'downloads_count' => 1]);
    }

    public function test_unsupported_files_are_rejected_before_upload(): void
    {
        $cloudinary = $this->mock(CloudinaryService::class);
        $cloudinary->shouldNotReceive('upload');
        $this->app->instance(CloudinaryService::class, $cloudinary);

        $response = $this->from(route('notes.create'))->post(route('notes.store'), [
            'title' => 'Unsafe file',
            'department' => 'CSE',
            'course' => 'CSE421',
            'semester' => 'Spring 2026',
            'file' => UploadedFile::fake()->create('program.exe', 10, 'application/x-msdownload'),
        ]);

        $response->assertRedirect(route('notes.create'))->assertSessionHasErrors('file');
        $this->assertDatabaseCount('notes', 0);
    }

    private function makeNote(array $overrides = []): Note
    {
        return Note::create(array_merge([
            'title' => 'Computer Networks Notes',
            'description' => 'TCP, IP, and routing lecture notes.',
            'department' => 'CSE',
            'course' => 'CSE421',
            'semester' => 'Spring 2026',
            'original_name' => 'network-notes.pdf',
            'public_id' => 'notes/original',
            'secure_url' => 'https://res.cloudinary.com/demo/raw/upload/network-notes.pdf',
            'resource_type' => 'raw',
            'format' => 'pdf',
            'mime_type' => 'application/pdf',
            'bytes' => 204800,
        ], $overrides));
    }
}
