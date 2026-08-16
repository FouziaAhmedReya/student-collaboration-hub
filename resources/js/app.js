import './bootstrap';

/*
|--------------------------------------------------------------------------
| File Drag and Drop
|--------------------------------------------------------------------------
*/

document.querySelectorAll('[data-file-drop]').forEach((dropZone) => {
    const input = dropZone.querySelector('input[type="file"]');
    const fileName = dropZone.querySelector('[data-file-name]');

    if (!input || !fileName) {
        return;
    }

    const showFileName = () => {
        if (input.files?.length) {
            fileName.textContent = input.files[0].name;
        }
    };

    input.addEventListener('change', showFileName);

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropZone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropZone.dataset.dragging = 'true';
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        dropZone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropZone.dataset.dragging = 'false';
        });
    });

    dropZone.addEventListener('drop', (event) => {
        if (!event.dataTransfer?.files.length) {
            return;
        }

        input.files = event.dataTransfer.files;
        showFileName();
    });
});

/*
|--------------------------------------------------------------------------
| Delete Confirmation
|--------------------------------------------------------------------------
*/

document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.dataset.confirmDelete
            || 'Delete this note and its Cloudinary file? This cannot be undone.';

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});

/*
|--------------------------------------------------------------------------
| Live Search
|--------------------------------------------------------------------------
|
| This code is used by both Notes and Marketplace.
| Results are requested from Laravel while the user is typing.
|
*/

document.querySelectorAll('[data-live-search-form]').forEach((form) => {
    const targetName = form.dataset.liveSearchTarget;

    const searchInput = form.querySelector(
        '[data-live-search-input]'
    );

    const filterInputs = form.querySelectorAll(
        'select[name]'
    );

    const searchStatus = form.querySelector(
        '[data-live-search-status]'
    );

    let searchTimer = null;
    let currentRequest = null;

    const getResultsContainer = () => {
        return document.querySelector(
            `[data-live-search-results="${targetName}"]`
        );
    };

    const setSearchStatus = (message) => {
        if (searchStatus) {
            searchStatus.textContent = message;
        }
    };

    const loadResults = async (url) => {
        const currentResults = getResultsContainer();

        if (!currentResults) {
            return;
        }

        if (currentRequest) {
            currentRequest.abort();
        }

        currentRequest = new AbortController();

        setSearchStatus('Searching...');

        currentResults.classList.add(
            'opacity-60',
            'pointer-events-none'
        );

        try {
            const response = await fetch(url, {
                method: 'GET',

                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'text/html',
                },

                signal: currentRequest.signal,
            });

            if (!response.ok) {
                throw new Error(
                    'The search request was unsuccessful.'
                );
            }

            const html = await response.text();

            const documentFromResponse =
                new DOMParser().parseFromString(
                    html,
                    'text/html'
                );

            const newResults =
                documentFromResponse.querySelector(
                    `[data-live-search-results="${targetName}"]`
                );

            if (!newResults) {
                throw new Error(
                    'The search results could not be found.'
                );
            }

            currentResults.innerHTML =
                newResults.innerHTML;

            window.history.replaceState(
                {},
                '',
                url
            );

            setSearchStatus('');
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error(error);

                setSearchStatus(
                    'Live search failed. Press Apply Filters.'
                );
            }
        } finally {
            currentResults.classList.remove(
                'opacity-60',
                'pointer-events-none'
            );
        }
    };

    const buildSearchUrl = () => {
        const formData = new FormData(form);

        const parameters =
            new URLSearchParams(formData);

        parameters.delete('page');

        const queryString =
            parameters.toString();

        return queryString
            ? `${form.action}?${queryString}`
            : form.action;
    };

    const scheduleSearch = (delay = 350) => {
        window.clearTimeout(searchTimer);

        searchTimer = window.setTimeout(() => {
            loadResults(
                buildSearchUrl()
            );
        }, delay);
    };

    /*
    |--------------------------------------------------------------------------
    | Search after typing a letter
    |--------------------------------------------------------------------------
    */

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            scheduleSearch(350);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Search immediately when a filter changes
    |--------------------------------------------------------------------------
    */

    filterInputs.forEach((filterInput) => {
        filterInput.addEventListener('change', () => {
            scheduleSearch(0);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Apply Filters Button
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        window.clearTimeout(searchTimer);

        loadResults(
            buildSearchUrl()
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Live Pagination
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', (event) => {
        const paginationLink = event.target.closest(
            `[data-live-pagination="${targetName}"] a`
        );

        if (!paginationLink) {
            return;
        }

        event.preventDefault();

        loadResults(
            paginationLink.href
        );
    });
});