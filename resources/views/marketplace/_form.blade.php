@php
    $editing = isset($book);
@endphp

<div class="grid gap-8 lg:grid-cols-2">

    {{-- Left side: Book information --}}
    <section>
        <h2 class="text-lg font-bold text-slate-950">
            Book Information
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Provide accurate information about the book.
        </p>

        <div class="mt-5 space-y-5">

            {{-- Title --}}
            <div>
                <label
                    for="title"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    Book Title <span class="text-red-500">*</span>
                </label>

                <input
                    id="title"
                    name="title"
                    type="text"
                    maxlength="150"
                    value="{{ old('title', $book->title ?? '') }}"
                    placeholder="Example: Operating System Concepts"
                    required
                    class="min-h-11 w-full rounded-xl border
                           border-slate-300 px-4 text-sm outline-none
                           focus:border-blue-500 focus:ring-4
                           focus:ring-blue-100"
                >
            </div>

            {{-- Author --}}
            <div>
                <label
                    for="author"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    Author <span class="text-red-500">*</span>
                </label>

                <input
                    id="author"
                    name="author"
                    type="text"
                    maxlength="120"
                    value="{{ old('author', $book->author ?? '') }}"
                    placeholder="Enter author name"
                    required
                    class="min-h-11 w-full rounded-xl border
                           border-slate-300 px-4 text-sm outline-none
                           focus:border-blue-500 focus:ring-4
                           focus:ring-blue-100"
                >
            </div>

            <div class="grid gap-5 sm:grid-cols-2">

                {{-- Price --}}
                <div>
                    <label
                        for="price"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Price (BDT) <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="price"
                        name="price"
                        type="number"
                        min="0"
                        max="1000000"
                        step="0.01"
                        value="{{ old('price', $book->price ?? '') }}"
                        placeholder="Example: 500"
                        required
                        class="min-h-11 w-full rounded-xl border
                               border-slate-300 px-4 text-sm outline-none
                               focus:border-blue-500 focus:ring-4
                               focus:ring-blue-100"
                    >
                </div>

                {{-- Condition --}}
                <div>
                    <label
                        for="condition"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Condition <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="condition"
                        name="condition"
                        required
                        class="min-h-11 w-full rounded-xl border
                               border-slate-300 bg-white px-4 text-sm
                               outline-none focus:border-blue-500
                               focus:ring-4 focus:ring-blue-100"
                    >
                        <option value="">
                            Select Condition
                        </option>

                        @foreach ($conditions as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'condition',
                                        $book->condition ?? ''
                                    ) === $value
                                )
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">

                {{-- Course --}}
                <div>
                    <label
                        for="course"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Course <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="course"
                        name="course"
                        type="text"
                        maxlength="120"
                        value="{{ old('course', $book->course ?? '') }}"
                        placeholder="Example: CSE311"
                        required
                        class="min-h-11 w-full rounded-xl border
                               border-slate-300 px-4 text-sm outline-none
                               focus:border-blue-500 focus:ring-4
                               focus:ring-blue-100"
                    >
                </div>

                {{-- Category --}}
                <div>
                    <label
                        for="category"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Category <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="category"
                        name="category"
                        required
                        class="min-h-11 w-full rounded-xl border
                               border-slate-300 bg-white px-4 text-sm
                               outline-none focus:border-blue-500
                               focus:ring-4 focus:ring-blue-100"
                    >
                        <option value="">
                            Select Category
                        </option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category }}"
                                @selected(
                                    old(
                                        'category',
                                        $book->category ?? ''
                                    ) === $category
                                )
                            >
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label
                    for="description"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    maxlength="2000"
                    placeholder="Write about the edition, highlights, missing pages, or other details."
                    class="w-full rounded-xl border border-slate-300
                           px-4 py-3 text-sm outline-none
                           focus:border-blue-500 focus:ring-4
                           focus:ring-blue-100"
                >{{ old('description', $book->description ?? '') }}</textarea>
            </div>

        </div>
    </section>

    {{-- Right side: Image and contact information --}}
    <section>
        <h2 class="text-lg font-bold text-slate-950">
            Image and Seller Contact
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Upload a clear image and provide your contact information.
        </p>

        {{-- Image --}}
        <div class="mt-5">
            <label
                for="image"
                class="mb-2 block text-sm font-bold text-slate-700"
            >
                Book Image

                @unless ($editing)
                    <span class="text-red-500">*</span>
                @endunless
            </label>

            @if ($editing)
                <div class="mb-4 rounded-xl bg-slate-100 p-4">
                    <p class="mb-3 text-xs font-bold uppercase text-slate-500">
                        Current Image
                    </p>

                    <img
                        src="{{ $book->image_url }}"
                        alt="{{ $book->title }}"
                        class="h-48 w-full object-contain"
                    >
                </div>
            @endif

            <div class="rounded-2xl border-2 border-dashed
                        border-slate-300 bg-slate-50 p-6 text-center">
                <div class="text-4xl">
                    🖼️
                </div>

                <p class="mt-3 text-sm font-bold text-slate-700">
                    Choose a book image
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    JPG, JPEG, PNG or WEBP — Maximum 5 MB
                </p>

                <input
                    id="image"
                    name="image"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    @if (! $editing) required @endif
                    class="mt-4 block w-full rounded-xl border
                           border-slate-300 bg-white p-3 text-sm"
                >
            </div>
        </div>

        {{-- Seller Information --}}
        <div class="mt-6 grid gap-5 sm:grid-cols-2">

            <div>
                <label
                    for="seller_name"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    Seller Name <span class="text-red-500">*</span>
                </label>

                <input
                    id="seller_name"
                    name="seller_name"
                    type="text"
                    maxlength="120"
                    value="{{ old(
                        'seller_name',
                        $book->seller_name ?? auth()->user()?->name
                    ) }}"
                    placeholder="Enter your name"
                    required
                    class="min-h-11 w-full rounded-xl border
                           border-slate-300 px-4 text-sm outline-none
                           focus:border-blue-500 focus:ring-4
                           focus:ring-blue-100"
                >
            </div>

            <div>
                <label
                    for="seller_phone"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    Phone Number
                </label>

                <input
                    id="seller_phone"
                    name="seller_phone"
                    type="text"
                    maxlength="30"
                    value="{{ old(
                        'seller_phone',
                        $book->seller_phone ?? ''
                    ) }}"
                    placeholder="01XXXXXXXXX"
                    class="min-h-11 w-full rounded-xl border
                           border-slate-300 px-4 text-sm outline-none
                           focus:border-blue-500 focus:ring-4
                           focus:ring-blue-100"
                >
            </div>

            <div class="sm:col-span-2">
                <label
                    for="seller_email"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    Email Address <span class="text-red-500">*</span>
                </label>

                <input
                    id="seller_email"
                    name="seller_email"
                    type="email"
                    maxlength="150"
                    value="{{ old(
                        'seller_email',
                        $book->seller_email ?? auth()->user()?->email
                    ) }}"
                    placeholder="example@email.com"
                    required
                    class="min-h-11 w-full rounded-xl border
                           border-slate-300 px-4 text-sm outline-none
                           focus:border-blue-500 focus:ring-4
                           focus:ring-blue-100"
                >
            </div>
        </div>
    </section>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 border-t
            border-slate-200 pt-6 sm:flex-row sm:justify-end">

    <a
        href="{{ $editing
            ? route('marketplace.show', $book)
            : route('marketplace.index') }}"
        class="inline-flex min-h-11 items-center justify-center
               rounded-xl border border-slate-300 px-5
               text-sm font-bold text-slate-700 hover:bg-slate-50"
    >
        Cancel
    </a>

    <button
        type="submit"
        class="inline-flex min-h-11 items-center justify-center
               rounded-xl bg-blue-600 px-6 text-sm font-bold
               text-white hover:bg-blue-700"
    >
        {{ $editing ? 'Save Changes' : ' List Book for Sale' }}
    </button>
</div>