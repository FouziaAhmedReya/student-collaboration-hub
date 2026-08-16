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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BookMarketplaceController extends Controller
{
    public function __construct(
        private readonly CloudinaryService $cloudinary
    ) {}

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

        $status = $request->string(
            'status',
            'active'
        )->toString();

        $sort = $request->string(
            'sort',
            'latest'
        )->toString();

        $books = Book::query()
            ->when(
                $search,
                function (
                    Builder $query,
                    string $search
                ) {
                    $query->where(
                        function (
                            Builder $query
                        ) use ($search) {
                            $query
                                ->where(
                                    'title',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'author',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'course',
                                    'like',
                                    '%'.$search.'%'
                                );
                        }
                    );
                }
            )
            ->when(
                $category,
                fn (
                    Builder $query,
                    string $value
                ) => $query->where(
                    'category',
                    $value
                )
            )
            ->when(
                $course,
                fn (
                    Builder $query,
                    string $value
                ) => $query->where(
                    'course',
                    $value
                )
            )
            ->when(
                $condition,
                fn (
                    Builder $query,
                    string $value
                ) => $query->where(
                    'condition',
                    $value
                )
            )
            ->when(
                $status !== 'all',
                fn (
                    Builder $query
                ) => $query->where(
                    'status',
                    $status
                )
            );

        match ($sort) {
            'oldest' => $books->oldest(),

            'title' => $books->orderBy(
                'title'
            ),

            'price_low' => $books->orderBy(
                'price'
            ),

            'price_high' => $books->orderByDesc(
                'price'
            ),

            default => $books->latest(),
        };

        return view(
            'marketplace.index',
            [
                'books' => $books
                    ->paginate(12)
                    ->withQueryString(),

                'categories' => Book::CATEGORIES,

                'conditions' => Book::CONDITIONS,

                'courses' => Book::query()
                    ->distinct()
                    ->orderBy('course')
                    ->pluck('course'),
            ]
        );
    }

    public function create(): View
    {
        return view(
            'marketplace.create',
            [
                'categories' => Book::CATEGORIES,
                'conditions' => Book::CONDITIONS,
            ]
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate(
            $this->bookRules(
                requireImage: true
            )
        );

        try {
            $uploaded = $this->cloudinary->upload(
                $request->file('image'),

                config(
                    'services.cloudinary.book_folder'
                )
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'image' => $exception->getMessage(),
                ]);
        }

        try {
            $book = DB::transaction(
                function () use (
                    $request,
                    $validated,
                    $uploaded
                ) {
                    return Book::create([
                        ...$this->bookMetadata(
                            $validated
                        ),

                        ...$this->imageMetadata(
                            $request,
                            $uploaded
                        ),

                        'user_id' => auth()->id(),

                        'owner_token' =>
                            $this->actorToken(
                                $request
                            ),

                        'status' => 'active',
                    ]);
                }
            );
        } catch (Throwable $exception) {
            $this->removeUploadedImage(
                $uploaded
            );

            throw $exception;
        }

        return redirect()
            ->route(
                'marketplace.show',
                $book
            )
            ->with(
                'success',
                '“'.$book->title.'” is now listed in the marketplace.'
            );
    }

    public function show(
        Request $request,
        Book $book
    ): View {
        return view(
            'marketplace.show',
            [
                'book' => $book,

                'isOwner' =>
                    $this->isBookOwner(
                        $book,
                        $request
                    ),
            ]
        );
    }

    public function edit(
        Request $request,
        Book $book
    ): View {
        $this->ensureBookOwner(
            $book,
            $request
        );

        return view(
            'marketplace.edit',
            [
                'book' => $book,
                'categories' => Book::CATEGORIES,
                'conditions' => Book::CONDITIONS,
            ]
        );
    }

    public function update(
        Request $request,
        Book $book
    ): RedirectResponse {
        $this->ensureBookOwner(
            $book,
            $request
        );

        $validated = $request->validate(
            $this->bookRules(
                requireImage: false
            )
        );

        $uploaded = null;

        if ($request->hasFile('image')) {
            try {
                $uploaded =
                    $this->cloudinary->upload(
                        $request->file('image'),

                        config(
                            'services.cloudinary.book_folder'
                        )
                    );
            } catch (Throwable $exception) {
                report($exception);

                return back()
                    ->withInput()
                    ->withErrors([
                        'image' =>
                            $exception->getMessage(),
                    ]);
            }
        }

        $oldImage = [
            'public_id' =>
                $book->image_public_id,

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
                ) {
                    $attributes =
                        $this->bookMetadata(
                            $validated
                        );

                    if ($uploaded) {
                        $attributes = [
                            ...$attributes,

                            ...$this->imageMetadata(
                                $request,
                                $uploaded
                            ),
                        ];
                    }

                    $book->update(
                        $attributes
                    );
                }
            );
        } catch (Throwable $exception) {
            if ($uploaded) {
                $this->removeUploadedImage(
                    $uploaded
                );
            }

            throw $exception;
        }

        if ($uploaded) {
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
            ->route(
                'marketplace.show',
                $book
            )
            ->with(
                'success',
                'The book listing was updated.'
            );
    }

    public function destroy(
        Request $request,
        Book $book
    ): RedirectResponse {
        $this->ensureBookOwner(
            $book,
            $request
        );

        if (
            $book->orders()
                ->where(
                    'status',
                    'pending'
                )
                ->exists()
        ) {
            return back()->withErrors([
                'delete' =>
                    'Handle the pending purchase request before deleting.',
            ]);
        }

        try {
            $this->cloudinary->destroy(
                $book->image_public_id,
                $book->image_resource_type
            );

            $book->delete();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'delete' =>
                    'The book could not be deleted. '.
                    $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route(
                'marketplace.manage'
            )
            ->with(
                'success',
                'The book listing was deleted.'
            );
    }

    public function manage(
        Request $request
    ): View {
        $token = $this->actorToken(
            $request
        );

        $userId = auth()->id();

        $sellingBooks = Book::query()
            ->with([
                'orders' =>
                    fn ($query) =>
                        $query->latest(),
            ])
            ->where(
                function (
                    Builder $query
                ) use (
                    $token,
                    $userId
                ) {
                    $query->where(
                        'owner_token',
                        $token
                    );

                    if ($userId) {
                        $query->orWhere(
                            'user_id',
                            $userId
                        );
                    }
                }
            )
            ->latest()
            ->get();

        $buyingOrders =
            BookOrder::query()
                ->with('book')
                ->where(
                    function (
                        Builder $query
                    ) use (
                        $token,
                        $userId
                    ) {
                        $query->where(
                            'buyer_token',
                            $token
                        );

                        if ($userId) {
                            $query->orWhere(
                                'buyer_id',
                                $userId
                            );
                        }
                    }
                )
                ->latest()
                ->get();

        return view(
            'marketplace.manage',
            [
                'sellingBooks' =>
                    $sellingBooks,

                'buyingOrders' =>
                    $buyingOrders,
            ]
        );
    }

    public function purchase(
        Request $request,
        Book $book
    ): RedirectResponse {
        if (
            $this->isBookOwner(
                $book,
                $request
            )
        ) {
            return back()->withErrors([
                'purchase' =>
                    'You cannot buy your own book.',
            ]);
        }

        $validated =
            $request->validate([
                'buyer_name' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'buyer_email' => [
                    'required',
                    'email',
                    'max:150',
                ],

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
            ) {
                $lockedBook =
                    Book::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $book->id
                        );

                if (
                    $lockedBook->status
                    !== 'active'
                ) {
                    throw ValidationException::withMessages([
                        'purchase' =>
                            'This book is no longer available.',
                    ]);
                }

                $lockedBook
                    ->orders()
                    ->create([
                        ...$validated,

                        'buyer_id' =>
                            auth()->id(),

                        'buyer_token' =>
                            $this->actorToken(
                                $request
                            ),

                        'status' =>
                            'pending',
                    ]);

                $lockedBook->update([
                    'status' => 'reserved',
                ]);
            }
        );

        return redirect()
            ->route(
                'marketplace.manage'
            )
            ->with(
                'success',
                'Your purchase request was sent to the seller.'
            );
    }

    public function acceptOrder(
        Request $request,
        BookOrder $order
    ): RedirectResponse {
        $order->loadMissing(
            'book'
        );

        $this->ensureBookOwner(
            $order->book,
            $request
        );

        DB::transaction(
            function () use ($order) {
                $lockedOrder =
                    BookOrder::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $order->id
                        );

                $lockedBook =
                    Book::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $lockedOrder->book_id
                        );

                if (
                    $lockedOrder->status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'This request has already been handled.',
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
            'Purchase request accepted.'
        );
    }

    public function rejectOrder(
        Request $request,
        BookOrder $order
    ): RedirectResponse {
        $order->loadMissing(
            'book'
        );

        $this->ensureBookOwner(
            $order->book,
            $request
        );

        DB::transaction(
            function () use ($order) {
                $lockedOrder =
                    BookOrder::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $order->id
                        );

                $lockedBook =
                    Book::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $lockedOrder->book_id
                        );

                if (
                    $lockedOrder->status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'This request has already been handled.',
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
            'Request declined. The book is available again.'
        );
    }

    public function cancelOrder(
        Request $request,
        BookOrder $order
    ): RedirectResponse {
        $this->ensureOrderBuyer(
            $order,
            $request
        );

        DB::transaction(
            function () use ($order) {
                $lockedOrder =
                    BookOrder::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $order->id
                        );

                $lockedBook =
                    Book::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $lockedOrder->book_id
                        );

                if (
                    $lockedOrder->status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'Only pending requests can be cancelled.',
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

    public function relist(
        Request $request,
        Book $book
    ): RedirectResponse {
        $this->ensureBookOwner(
            $book,
            $request
        );

        $book->update([
            'status' => 'active',
        ]);

        return back()->with(
            'success',
            'The book is available again.'
        );
    }

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
                Rule::in(
                    Book::CATEGORIES
                ),
            ],

            'condition' => [
                'required',

                Rule::in(
                    array_keys(
                        Book::CONDITIONS
                    )
                ),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'seller_name' => [
                'required',
                'string',
                'max:120',
            ],

            'seller_email' => [
                'required',
                'email',
                'max:150',
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

    private function bookMetadata(
        array $validated
    ): array {
        return [
            'title' =>
                $validated['title'],

            'author' =>
                $validated['author'],

            'price' =>
                $validated['price'],

            'course' =>
                $validated['course'],

            'category' =>
                $validated['category'],

            'condition' =>
                $validated['condition'],

            'description' =>
                $validated['description']
                ?? null,

            'seller_name' =>
                $validated['seller_name'],

            'seller_email' =>
                $validated['seller_email'],

            'seller_phone' =>
                $validated['seller_phone']
                ?? null,
        ];
    }

    private function imageMetadata(
        Request $request,
        array $uploaded
    ): array {
        $file =
            $request->file('image');

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
                (int) $uploaded['bytes'],
        ];
    }

    private function actorToken(
        Request $request
    ): string {
        $token =
            $request->session()
                ->get(
                    'marketplace_actor_token'
                );

        if (
            ! is_string($token)
            || strlen($token) !== 64
        ) {
            $token = Str::random(64);

            $request->session()->put(
                'marketplace_actor_token',
                $token
            );
        }

        return $token;
    }

    private function isBookOwner(
        Book $book,
        Request $request
    ): bool {
        if (
            auth()->check()
            && (int) $book->user_id
                === (int) auth()->id()
        ) {
            return true;
        }

        return is_string(
            $book->owner_token
        )
            && hash_equals(
                $book->owner_token,

                $this->actorToken(
                    $request
                )
            );
    }

    private function ensureBookOwner(
        Book $book,
        Request $request
    ): void {
        abort_unless(
            $this->isBookOwner(
                $book,
                $request
            ),

            403,

            'You cannot manage another student’s listing.'
        );
    }

    private function ensureOrderBuyer(
        BookOrder $order,
        Request $request
    ): void {
        $isBuyer =
            auth()->check()
            && (int) $order->buyer_id
                === (int) auth()->id();

        if (! $isBuyer) {
            $isBuyer =
                is_string(
                    $order->buyer_token
                )
                && hash_equals(
                    $order->buyer_token,

                    $this->actorToken(
                        $request
                    )
                );
        }

        abort_unless(
            $isBuyer,

            403,

            'You cannot manage another buyer’s request.'
        );
    }

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
