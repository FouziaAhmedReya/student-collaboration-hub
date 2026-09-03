<?php

namespace App\Http\Controllers\Modules\Fouzia;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookOrder;
use App\Services\CloudinaryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BookMarketplaceController extends Controller
{
    public function __construct(
        private readonly CloudinaryService $cloudinary
    ) {
    }

    /**
     * Display all marketplace books.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')
            ->trim()
            ->toString();

        $category = $request->string('category')
            ->trim()
            ->toString();

        $course = $request->string('course')
            ->trim()
            ->toString();

        $condition = $request->string('condition')
            ->trim()
            ->toString();

        $status = $request->string('status', 'active')
            ->trim()
            ->toString();

        $sort = $request->string('sort', 'latest')
            ->trim()
            ->toString();

        $books = Book::query()
            ->with('seller:id,name,email')

            ->when(
                $search !== '',
                function (Builder $query) use ($search) {
                    $query->where(
                        function (Builder $innerQuery) use ($search) {
                            $innerQuery
                                ->where(
                                    'title',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'author',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'course',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
                }
            )

            ->when(
                $category !== '',
                fn (Builder $query) =>
                    $query->where('category', $category)
            )

            ->when(
                $course !== '',
                fn (Builder $query) =>
                    $query->where('course', $course)
            )

            ->when(
                $condition !== '',
                fn (Builder $query) =>
                    $query->where('condition', $condition)
            )

            ->when(
                $status !== 'all',
                fn (Builder $query) =>
                    $query->where('status', $status)
            );

        match ($sort) {
            'oldest' => $books->oldest(),

            'title' => $books->orderBy('title'),

            'price_low' => $books->orderBy('price'),

            'price_high' => $books->orderByDesc('price'),

            default => $books->latest(),
        };

        return view('marketplace.index', [
            'books' => $books
                ->paginate(12)
                ->withQueryString(),

            'categories' => Book::CATEGORIES,

            'conditions' => Book::CONDITIONS,

            'courses' => Book::query()
                ->whereNotNull('course')
                ->where('course', '!=', '')
                ->distinct()
                ->orderBy('course')
                ->pluck('course'),
        ]);
    }

    /**
     * Display the book-listing form.
     */
    public function create(): View
    {
        return view('marketplace.create', [
            'categories' => Book::CATEGORIES,
            'conditions' => Book::CONDITIONS,
        ]);
    }

    /**
     * Store a new marketplace book.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->bookRules(requireImage: true)
        );

        try {
            $uploaded = $this->cloudinary->upload(
                $request->file('image'),
                config('services.cloudinary.book_folder')
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'image' =>
                        'The book image could not be uploaded: ' .
                        $exception->getMessage(),
                ]);
        }

        try {
            $book = DB::transaction(
                function () use (
                    $request,
                    $validated,
                    $uploaded
                ): Book {
                    return Book::create([
                        ...$this->bookMetadata($validated),

                        ...$this->imageMetadata(
                            $request,
                            $uploaded
                        ),

                        'user_id' => auth()->id(),

                        /*
                         * owner_token was used before authentication.
                         * Ownership is now controlled by user_id.
                         */
                        'owner_token' => null,

                        'status' => 'active',
                    ]);
                }
            );
        } catch (Throwable $exception) {
            $this->removeUploadedImage($uploaded);

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'book' =>
                        'The book listing could not be saved.',
                ]);
        }

        return redirect()
            ->route('marketplace.show', $book)
            ->with(
                'success',
                '“' . $book->title .
                '” is now listed in the marketplace.'
            );
    }

    /**
     * Display one marketplace book.
     */
    public function show(Book $book): View
    {
        $book->loadMissing('seller:id,name,email');

        return view('marketplace.show', [
            'book' => $book,

            'isOwner' => $this->isBookOwner($book),
        ]);
    }

    /**
     * Display the edit form.
     */
    public function edit(Book $book): View
    {
        $this->ensureBookOwner($book);

        return view('marketplace.edit', [
            'book' => $book,
            'categories' => Book::CATEGORIES,
            'conditions' => Book::CONDITIONS,
        ]);
    }

    /**
     * Update a book listing.
     */
    public function update(
        Request $request,
        Book $book
    ): RedirectResponse {
        $this->ensureBookOwner($book);

        $validated = $request->validate(
            $this->bookRules(requireImage: false)
        );

        $uploaded = null;

        if ($request->hasFile('image')) {
            try {
                $uploaded = $this->cloudinary->upload(
                    $request->file('image'),
                    config('services.cloudinary.book_folder')
                );
            } catch (Throwable $exception) {
                report($exception);

                return back()
                    ->withInput()
                    ->withErrors([
                        'image' =>
                            'The new image could not be uploaded: ' .
                            $exception->getMessage(),
                    ]);
            }
        }

        $oldImage = [
            'public_id' => $book->image_public_id,

            'resource_type' =>
                $book->image_resource_type,
        ];

        try {
            DB::transaction(
                function () use (
                    $book,
                    $validated,
                    $request,
                    $uploaded
                ): void {
                    $attributes =
                        $this->bookMetadata($validated);

                    if ($uploaded !== null) {
                        $attributes = [
                            ...$attributes,

                            ...$this->imageMetadata(
                                $request,
                                $uploaded
                            ),
                        ];
                    }

                    $book->update($attributes);
                }
            );
        } catch (Throwable $exception) {
            if ($uploaded !== null) {
                $this->removeUploadedImage($uploaded);
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'book' =>
                        'The book listing could not be updated.',
                ]);
        }

        /*
         * Remove the old image only after the database
         * has been updated successfully.
         */
        if (
            $uploaded !== null &&
            ! empty($oldImage['public_id'])
        ) {
            try {
                $this->cloudinary->destroy(
                    $oldImage['public_id'],
                    $oldImage['resource_type']
                );
            } catch (Throwable $exception) {
                Log::warning(
                    'Old marketplace image could not be removed.',
                    [
                        'book_id' => $book->id,

                        'error' =>
                            $exception->getMessage(),
                    ]
                );
            }
        }

        return redirect()
            ->route('marketplace.show', $book)
            ->with(
                'success',
                'The book listing was updated.'
            );
    }

    /**
     * Delete a book owned by the logged-in student.
     */
    public function destroy(Book $book): RedirectResponse
    {
        $this->ensureBookOwner($book);

        $hasPendingOrder = $book->orders()
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingOrder) {
            return back()->withErrors([
                'delete' =>
                    'Handle the pending purchase request before deleting this listing.',
            ]);
        }

        try {
            if (! empty($book->image_public_id)) {
                $this->cloudinary->destroy(
                    $book->image_public_id,
                    $book->image_resource_type
                );
            }

            $book->delete();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'delete' =>
                    'The book could not be deleted: ' .
                    $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('marketplace.manage')
            ->with(
                'success',
                'The book listing was deleted.'
            );
    }

    /**
     * Display the logged-in student's selling
     * and buying activity.
     */
    public function manage(): View
    {
        $userId = auth()->id();

        $sellingBooks = Book::query()
            ->with([
                'orders' => function ($query) {
                    $query
                        ->with('buyer:id,name,email')
                        ->latest();
                },
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $buyingOrders = BookOrder::query()
            ->with([
                'book',
                'book.seller:id,name,email',
            ])
            ->where('buyer_id', $userId)
            ->latest()
            ->get();

        return view('marketplace.manage', [
            'sellingBooks' => $sellingBooks,
            'buyingOrders' => $buyingOrders,
        ]);
    }

    /**
     * Send a purchase request for a book.
     */
    public function purchase(
        Request $request,
        Book $book
    ): RedirectResponse {
        if ($this->isBookOwner($book)) {
            return back()->withErrors([
                'purchase' =>
                    'You cannot buy your own book.',
            ]);
        }

        $validated = $request->validate([
            'buyer_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'message' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(
            function () use (
                $request,
                $book,
                $validated
            ): void {
                $lockedBook = Book::query()
                    ->lockForUpdate()
                    ->findOrFail($book->id);

                if (
                    (int) $lockedBook->user_id ===
                    (int) auth()->id()
                ) {
                    throw ValidationException::withMessages([
                        'purchase' =>
                            'You cannot buy your own book.',
                    ]);
                }

                if ($lockedBook->status !== 'active') {
                    throw ValidationException::withMessages([
                        'purchase' =>
                            'This book is no longer available.',
                    ]);
                }

                $lockedBook->orders()->create([
                    'buyer_id' => auth()->id(),

                    /*
                     * buyer_token was used before authentication.
                     * The buyer is now identified by buyer_id.
                     */
                    'buyer_token' => null,

                    'buyer_name' =>
                        auth()->user()->name,

                    'buyer_email' =>
                        auth()->user()->email,

                    'buyer_phone' =>
                        $validated['buyer_phone']
                        ?? auth()->user()->phone,

                    'message' =>
                        $validated['message']
                        ?? null,

                    'status' => 'pending',
                ]);

                $lockedBook->update([
                    'status' => 'reserved',
                ]);
            }
        );

        return redirect()
            ->route('marketplace.manage')
            ->with(
                'success',
                'Your purchase request was sent to the seller.'
            );
    }

    /**
     * Seller accepts a purchase request.
     */
    public function acceptOrder(
        BookOrder $order
    ): RedirectResponse {
        $order->loadMissing('book');

        abort_if(
            $order->book === null,
            404,
            'The related book listing no longer exists.'
        );

        $this->ensureBookOwner($order->book);

        DB::transaction(
            function () use ($order): void {
                $lockedOrder = BookOrder::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                $lockedBook = Book::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedOrder->book_id);

                abort_unless(
                    (int) $lockedBook->user_id ===
                    (int) auth()->id(),
                    403,
                    'You cannot manage another student’s purchase request.'
                );

                if ($lockedOrder->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'order' =>
                            'This purchase request has already been handled.',
                    ]);
                }

                $lockedOrder->update([
                    'status' => 'accepted',
                    'responded_at' => now(),
                ]);

                $lockedBook->update([
                    'status' => 'sold',
                ]);
            }
        );

        return back()->with(
            'success',
            'Purchase request accepted. The book is now marked as sold.'
        );
    }

    /**
     * Seller rejects a purchase request.
     */
    public function rejectOrder(
        BookOrder $order
    ): RedirectResponse {
        $order->loadMissing('book');

        abort_if(
            $order->book === null,
            404,
            'The related book listing no longer exists.'
        );

        $this->ensureBookOwner($order->book);

        DB::transaction(
            function () use ($order): void {
                $lockedOrder = BookOrder::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                $lockedBook = Book::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedOrder->book_id);

                abort_unless(
                    (int) $lockedBook->user_id ===
                    (int) auth()->id(),
                    403,
                    'You cannot manage another student’s purchase request.'
                );

                if ($lockedOrder->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'order' =>
                            'This purchase request has already been handled.',
                    ]);
                }

                $lockedOrder->update([
                    'status' => 'rejected',
                    'responded_at' => now(),
                ]);

                $lockedBook->update([
                    'status' => 'active',
                ]);
            }
        );

        return back()->with(
            'success',
            'Purchase request rejected. The book is available again.'
        );
    }

    /**
     * Buyer cancels their own pending purchase request.
     */
    public function cancelOrder(
        BookOrder $order
    ): RedirectResponse {
        $this->ensureOrderBuyer($order);

        DB::transaction(
            function () use ($order): void {
                $lockedOrder = BookOrder::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                abort_unless(
                    (int) $lockedOrder->buyer_id ===
                    (int) auth()->id(),
                    403,
                    'You cannot cancel another student’s purchase request.'
                );

                $lockedBook = Book::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedOrder->book_id);

                if ($lockedOrder->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'order' =>
                            'Only pending purchase requests can be cancelled.',
                    ]);
                }

                $lockedOrder->update([
                    'status' => 'cancelled',
                    'responded_at' => now(),
                ]);

                $lockedBook->update([
                    'status' => 'active',
                ]);
            }
        );

        return back()->with(
            'success',
            'Your purchase request was cancelled.'
        );
    }

    /**
     * Make a sold or unavailable book active again.
     */
    public function relist(Book $book): RedirectResponse
    {
        $this->ensureBookOwner($book);

        $hasPendingOrder = $book->orders()
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingOrder) {
            return back()->withErrors([
                'relist' =>
                    'You cannot relist this book while a purchase request is pending.',
            ]);
        }

        $book->update([
            'status' => 'active',
        ]);

        return back()->with(
            'success',
            'The book is available again.'
        );
    }

    /**
     * Validation rules for creating and editing books.
     */
    private function bookRules(
        bool $requireImage
    ): array {
        return [
            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'author' => [
                'required',
                'string',
                'max:120',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:1000000',
            ],

            'course' => [
                'required',
                'string',
                'max:120',
            ],

            'category' => [
                'required',
                Rule::in(Book::CATEGORIES),
            ],

            'condition' => [
                'required',
                Rule::in(
                    array_keys(Book::CONDITIONS)
                ),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'seller_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'image' => [
                $requireImage
                    ? 'required'
                    : 'nullable',

                File::types([
                    'jpg',
                    'jpeg',
                    'png',
                    'webp',
                ])->max('5mb'),
            ],
        ];
    }

    /**
     * Prepare book information for database saving.
     */
    private function bookMetadata(
        array $validated
    ): array {
        return [
            'title' => $validated['title'],

            'author' => $validated['author'],

            'price' => $validated['price'],

            'course' => $validated['course'],

            'category' => $validated['category'],

            'condition' => $validated['condition'],

            'description' =>
                $validated['description'] ?? null,

            /*
             * Seller name and email always come from the
             * logged-in student account.
             */
            'seller_name' =>
                auth()->user()->name,

            'seller_email' =>
                auth()->user()->email,

            'seller_phone' =>
                $validated['seller_phone']
                ?? auth()->user()->phone,
        ];
    }

    /**
     * Prepare Cloudinary image information.
     */
    private function imageMetadata(
        Request $request,
        array $uploaded
    ): array {
        $file = $request->file('image');

        return [
            'original_image_name' =>
                $file->getClientOriginalName(),

            'image_public_id' =>
                $uploaded['public_id'],

            'image_url' =>
                $uploaded['secure_url'],

            'image_resource_type' =>
                $uploaded['resource_type'],

            'image_format' =>
                $uploaded['format']
                ?? strtolower(
                    $file->getClientOriginalExtension()
                ),

            'image_mime_type' =>
                $file->getMimeType(),

            'image_bytes' =>
                (int) ($uploaded['bytes'] ?? $file->getSize()),
        ];
    }

    /**
     * Check whether the current student owns the book.
     */
    private function isBookOwner(Book $book): bool
    {
        return auth()->check()
            && $book->user_id !== null
            && (int) $book->user_id ===
                (int) auth()->id();
    }

    /**
     * Stop students from managing another seller's book.
     */
    private function ensureBookOwner(Book $book): void
    {
        abort_unless(
            $this->isBookOwner($book),
            403,
            'You cannot manage another student’s book listing.'
        );
    }

    /**
     * Stop students from managing another buyer's order.
     */
    private function ensureOrderBuyer(
        BookOrder $order
    ): void {
        abort_unless(
            auth()->check()
            && $order->buyer_id !== null
            && (int) $order->buyer_id ===
                (int) auth()->id(),
            403,
            'You cannot manage another buyer’s purchase request.'
        );
    }

    /**
     * Remove an uploaded Cloudinary image.
     */
    private function removeUploadedImage(
        array $uploaded
    ): void {
        try {
            $this->cloudinary->destroy(
                $uploaded['public_id'],
                $uploaded['resource_type']
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}