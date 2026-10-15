import Alpine from 'alpinejs';

Alpine.data('catalogue', () => ({
    selected: null,
    trigger: null,
    openBook(book, trigger) {
        this.selected = book;
        this.trigger = trigger;
        this.$refs.preview.showModal();
        this.$nextTick(() => this.$refs.previewClose.focus());
    },
    closeBook() {
        this.$refs.preview.close();
        this.selected = null;
        this.trigger?.focus();
    },
}));

window.Alpine = Alpine;
Alpine.start();

document.querySelectorAll('[data-author-picker]').forEach((container) => {
    const select = container.querySelector('select');
    const fields = container.querySelector('[data-new-author]');
    const input = fields.querySelector('input');

    function updateAuthorField() {
        const addingAuthor = select.value === 'new';
        fields.classList.toggle('hidden', !addingAuthor);
        input.disabled = !addingAuthor;
        input.required = addingAuthor;
    }

    updateAuthorField();
    select.addEventListener('change', () => {
        updateAuthorField();
        if (!input.disabled) input.focus();
    });
});

document.querySelectorAll('[data-cover-upload]').forEach((container) => {
    const input = container.querySelector('input[type="file"]');
    const preview = container.querySelector('[data-cover-preview]');
    const image = container.querySelector('[data-cover-preview-image]');
    const filename = container.querySelector('[data-cover-filename]');
    const reset = container.querySelector('[data-cover-reset]');
    const currentCover = image.getAttribute('src');
    let previewUrl;

    function restoreCover() {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = null;
        if (currentCover) image.src = currentCover;
        else image.removeAttribute('src');
        preview.classList.toggle('hidden', !currentCover);
        filename.textContent = 'Current cover';
        reset.classList.add('hidden');
    }

    input.addEventListener('change', () => {
        restoreCover();
        const file = input.files[0];
        if (!file || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return;
        previewUrl = URL.createObjectURL(file);
        image.src = previewUrl;
        filename.textContent = file.name;
        preview.classList.remove('hidden');
        reset.classList.remove('hidden');
    });
    reset.addEventListener('click', () => {
        input.value = '';
        restoreCover();
    });
});

const searchForm = document.querySelector('[data-catalogue-search]');
if (searchForm) {
    let pendingRequest;
    let debounceTimer;
    const status = searchForm.querySelector('[data-search-status]');
    searchForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearTimeout(debounceTimer);
        pendingRequest?.abort();
        const controller = new AbortController();
        pendingRequest = controller;
        const url = new URL(searchForm.action);
        url.search = new URLSearchParams(new FormData(searchForm)).toString();
        const current = document.getElementById('catalogue-results');
        current.setAttribute('aria-busy', 'true');
        current.classList.add('opacity-60');
        status.textContent = 'Searching the collection…';
        try {
            const response = await fetch(url, { signal: controller.signal, headers: { Accept: 'text/html' } });
            if (!response.ok) throw new Error('Search failed');
            const documentResult = new DOMParser().parseFromString(await response.text(), 'text/html');
            const results = documentResult.getElementById('catalogue-results');
            if (!results) throw new Error('No search results');
            if (controller.signal.aborted) return;
            current.replaceWith(results);
            window.history.replaceState(null, '', url);
            status.textContent = results.querySelector('h2')?.textContent || 'Search complete.';
        } catch (error) {
            if (error.name === 'AbortError') return;
            current.setAttribute('aria-busy', 'false');
            current.classList.remove('opacity-60');
            status.textContent = 'Search could not update. Press Search to retry, or reload the page.';
        }
    });
    searchForm.querySelector('input[type="search"]').addEventListener('input', () => {
        clearTimeout(debounceTimer);
        pendingRequest?.abort();
        debounceTimer = setTimeout(() => searchForm.requestSubmit(), 350);
    });
    searchForm.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => searchForm.requestSubmit());
    });
}

const confirmation = document.getElementById('confirmation-dialog');
let pendingConfirmation;
const approvedForms = new WeakSet();

confirmation?.querySelector('[data-confirm-cancel]').addEventListener('click', () => confirmation.close());
confirmation?.addEventListener('click', (event) => {
    if (event.target === confirmation) confirmation.close();
});
confirmation?.querySelector('[data-confirm-accept]').addEventListener('click', () => {
    const pending = pendingConfirmation;
    confirmation.close();
    if (!pending) return;
    approvedForms.add(pending.form);
    if (pending.submitter) pending.form.requestSubmit(pending.submitter);
    else pending.form.requestSubmit();
});
confirmation?.addEventListener('close', () => { pendingConfirmation = null; });

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() === 'get' || event.defaultPrevented) return;
    if (form.dataset.confirm && confirmation && !approvedForms.has(form)) {
        event.preventDefault();
        pendingConfirmation = { form, submitter: event.submitter };
        confirmation.querySelector('#confirmation-description').textContent = form.dataset.confirm;
        confirmation.showModal();
        return;
    }
    approvedForms.delete(form);
    form.setAttribute('aria-busy', 'true');
    form.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = true;
    });
});

window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[aria-busy="true"]').forEach((form) => {
        form.removeAttribute('aria-busy');
        form.querySelectorAll('button[type="submit"]').forEach((button) => { button.disabled = false; });
    });
});

document.addEventListener('error', (event) => {
    const image = event.target;
    if (image instanceof HTMLImageElement && image.matches('[data-book-cover]')) {
        image.hidden = true;
        const fallback = document.createElement('span');
        fallback.className = 'p-3 text-center text-sm text-stone-600';
        fallback.textContent = 'Cover unavailable';
        image.parentElement.append(fallback);
    }
}, true);
