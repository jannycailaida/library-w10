import { test, expect } from '@playwright/test';

async function signIn(page, role) {
    await page.goto('/login');
    await page.getByLabel('Email address', { exact: true }).fill(role + '@library.test');
    await page.locator('#password').fill('password');
    await page.getByRole('button', { name: 'Sign in', exact: true }).click();
    await expect(page).toHaveURL(/\/dashboard$/);
}

async function register(page, email, name = 'Browser QA Reader') {
    await page.goto('/register');
    await page.getByLabel('Full name', { exact: true }).fill(name);
    await page.getByLabel('Email address', { exact: true }).fill(email);
    await page.locator('#password').fill('library-password');
    await page.locator('#password_confirmation').fill('library-password');
    await page.getByRole('button', { name: 'Create account', exact: true }).click();
    await expect(page).toHaveURL(/\/dashboard$/);
}

const guidelineStatuses = {
    guest: [200, 302, 302, 302, 302, 302, 302, 302],
    member: [200, 403, 403, 403, 200, 403, 403, 302],
    librarian: [200, 302, 302, 302, 200, 200, 403, 302],
    admin: [200, 302, 302, 302, 200, 200, 200, 302],
};

for (const [roleIndex, role] of Object.keys(guidelineStatuses).entries()) {
    test('guideline access matrix for ' + role, async ({ page, browser }, testInfo) => {
        const staffContext = await browser.newContext({ baseURL: 'http://127.0.0.1:8011' });
        let editablePath;
        let borrowablePath;
        let payload;
        try {
            const staff = await staffContext.newPage();
            await signIn(staff, 'librarian');
            await staff.goto('/books/create');
            const token = await staff.locator('meta[name="csrf-token"]').getAttribute('content');
            const authorId = await staff.locator('#author_id option').nth(1).getAttribute('value');
            payload = {
                title: 'Guideline ' + role + ' catalogue record', author_id: authorId,
                isbn: '9799000000' + roleIndex + '00', published_year: '2026', is_reference: '0',
            };
            for (const [index, title] of ['editable', 'borrowable'].entries()) {
                const response = await staff.request.post('/books', {
                    form: { ...payload, _token: token, isbn: '9799000000' + roleIndex + '0' + (index + 1), title: 'Guideline ' + role + ' ' + title },
                    maxRedirects: 0,
                });
                expect(response.status(), 'create isolated matrix fixture').toBe(302);
                const path = new URL(response.headers().location, staff.url()).pathname;
                expect(path).toMatch(/^\/books\/\d+$/);
                if (index === 0) editablePath = path;
                else borrowablePath = path;
            }
        } finally {
            await staffContext.close();
        }

        if (role !== 'guest') await signIn(page, role);
        await page.goto('/books');
        const token = await page.locator('meta[name="csrf-token"]').getAttribute('content');
        const editable = await page.request.get(editablePath + '/edit', { maxRedirects: 0 });
        const editableHtml = await editable.text();
        const editableIsbn = '9799000000' + roleIndex + '01';
        if (role === 'librarian' || role === 'admin') expect(editableHtml).toContain(editableIsbn);
        const cases = [
            { action: 'Browse catalogue', method: 'GET', path: '/books' },
            { action: 'Create book', method: 'POST', path: '/books', data: payload },
            { action: 'Update book', method: 'PUT', path: editablePath, data: { ...payload, isbn: editableIsbn, title: 'Guideline ' + role + ' revised' } },
            { action: 'Delete book', method: 'DELETE', path: editablePath },
            { action: 'View own loans', method: 'GET', path: '/my/loans' },
            { action: 'Open circulation desk', method: 'GET', path: '/desk' },
            { action: 'Manage users', method: 'GET', path: '/admin/users' },
            { action: 'Borrow available book', method: 'POST', path: borrowablePath + '/borrow' },
        ];
        const results = [];
        for (const [index, entry] of cases.entries()) {
            const options = { method: entry.method, maxRedirects: 0 };
            if (entry.method !== 'GET') options.form = { _token: token, ...entry.data };
            const response = await page.request.fetch(entry.path, options);
            const expected = guidelineStatuses[role][index];
            const location = response.headers().location;
            results.push({ action: entry.action, method: entry.method, path: entry.path, expected, actual: response.status(), location });
            expect(response.status(), role + ': ' + entry.action).toBe(expected);
            if (role === 'guest' && expected === 302) {
                expect(new URL(location, page.url()).pathname).toBe('/login');
            } else if (entry.action === 'Create book' && expected === 302) {
                const createdPath = new URL(location, page.url()).pathname;
                expect(createdPath).toMatch(/^\/books\/\d+$/);
                expect(await (await page.request.get(createdPath)).text()).toContain(payload.title);
            } else if (entry.action === 'Update book' && expected === 302) {
                expect(new URL(location, page.url()).pathname).toBe(editablePath);
                expect(await (await page.request.get(editablePath)).text()).toContain('Guideline ' + role + ' revised');
            } else if (entry.action === 'Delete book' && expected === 302) {
                expect(new URL(location, page.url()).pathname).toBe('/books');
                expect((await page.request.get(editablePath)).status()).toBe(404);
            } else if (entry.action === 'Borrow available book' && expected === 302) {
                expect(new URL(location, page.url()).pathname).toBe('/my/loans');
                expect(await (await page.request.get('/my/loans')).text()).toContain('Guideline ' + role + ' borrowable');
            }
        }
        if (role === 'guest' || role === 'member') {
            const unchanged = await page.request.get(editablePath);
            expect(unchanged.status()).toBe(200);
            expect(await unchanged.text()).toContain('Guideline ' + role + ' editable');
            const search = await page.request.get('/books?' + new URLSearchParams({ search: payload.title }));
            expect(await search.text()).toContain('No books found');
        }
        await testInfo.attach('guideline-' + role + '-statuses.json', {
            body: JSON.stringify({ role, results }, null, 2), contentType: 'application/json',
        });
    });
}

const routes = {
    guest: ['/books', '/books/1', '/login', '/register', '/forgot-password', '/reset-password/preview-token'],
    member: ['/dashboard', '/books', '/books/1', '/my/loans', '/profile', '/confirm-password'],
    librarian: ['/dashboard', '/books', '/books/1', '/books/create', '/books/1/edit', '/my/loans', '/desk', '/profile', '/confirm-password'],
    admin: ['/dashboard', '/books', '/books/1', '/books/create', '/books/1/edit', '/my/loans', '/desk', '/admin/users', '/profile', '/confirm-password'],
};

for (const role of Object.keys(routes)) {
    for (const width of [1440, 1024, 768, 390]) {
        test(role + ' pages are usable at ' + width + 'px', async ({ page }, testInfo) => {
            await page.setViewportSize({ width, height: width < 768 ? 844 : 1000 });
            const errors = [];
            page.on('pageerror', error => errors.push(error.message));
            if (role !== 'guest') await signIn(page, role);
            for (const path of routes[role]) {
                const response = await page.goto(path);
                expect(response.status(), path).toBe(200);
                await expect(page.locator('h1')).toBeVisible();
                await expect(page.locator('body')).not.toContainText('Undefined variable');
                expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), path + ' overflows').toBe(true);
                expect(await page.evaluate(() => typeof window.Alpine)).toBe('object');
                await page.screenshot({ path: testInfo.outputPath(path.replaceAll('/', '_') + '.png'), fullPage: false });
            }
            if (width < 1024) {
                await page.goto('/books');
                await page.getByRole('button', { name: 'Toggle navigation menu' }).click();
                await expect(page.getByRole('navigation', { name: 'Mobile navigation' })).toBeVisible();
                await expect(page.getByRole('button', { name: 'Toggle navigation menu' })).toHaveAttribute('aria-expanded', 'true');
            }
            expect(errors).toEqual([]);
        });
    }
}

test('live catalogue search preserves focus, handles empty results, and previews apostrophes accessibly', async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/books');
    const input = page.getByLabel('Search the collection', { exact: true });
    expect((await input.boundingBox()).y).toBeLessThan(400);
    await input.fill('no-matching-title-browser-qa');
    await expect(page.getByRole('heading', { name: 'No books found' })).toBeVisible();
    await expect(input).toBeFocused();
    await input.fill('Clean Architecture');
    await expect(page.getByRole('heading', { name: /1 book matching/ })).toBeVisible();
    const trigger = page.getByRole('button', { name: 'Quick view', exact: true });
    await trigger.click();
    const preview = page.getByRole('dialog', { name: /Clean Architecture/ });
    await expect(preview).toBeVisible();
    await expect(preview.locator('h2')).toContainText("A Craftsman's Guide");
    expect(await page.evaluate(() => document.querySelector('dialog[open]').contains(document.activeElement))).toBe(true);
    await page.keyboard.press('Tab');
    expect(await page.evaluate(() => document.querySelector('dialog[open]').contains(document.activeElement))).toBe(true);
    await page.keyboard.press('Escape');
    await expect(preview).not.toBeVisible();
    await expect(trigger).toBeFocused();
    expect(errors).toEqual([]);
});

test('password visibility works on registration, login, recovery, confirmation, and profile', async ({ page }) => {
    for (const path of ['/login', '/register', '/reset-password/preview-token']) {
        await page.goto(path);
        await page.locator('#password').fill('visibility-check');
        await page.getByRole('button', { name: 'Show password', exact: true }).first().click();
        await expect(page.locator('#password')).toHaveAttribute('type', 'text');
        await page.getByRole('button', { name: 'Hide password', exact: true }).first().click();
        await expect(page.locator('#password')).toHaveAttribute('type', 'password');
    }
    await signIn(page, 'member');
    await page.goto('/confirm-password');
    await page.getByRole('button', { name: 'Show password', exact: true }).click();
    await expect(page.locator('#password')).toHaveAttribute('type', 'text');
    await page.goto('/profile');
    await page.locator('#current_password').fill('visibility-check');
    await page.getByRole('button', { name: 'Show password', exact: true }).first().click();
    await expect(page.locator('#current_password')).toHaveAttribute('type', 'text');
});

test('member permissions are enforced and errors provide a usable return path', async ({ page }) => {
    await signIn(page, 'member');
    for (const path of ['/desk', '/admin/users', '/books/create', '/books/1/edit']) {
        const response = await page.goto(path);
        expect(response.status()).toBe(403);
        await expect(page.getByRole('heading', { name: 'Access restricted' })).toBeVisible();
        await expect(page.getByRole('link', { name: 'Back to catalogue' })).toBeVisible();
    }
    const response = await page.goto('/missing-library-page');
    expect(response.status()).toBe(404);
    await expect(page.getByRole('heading', { name: 'We couldn’t find that page' })).toBeVisible();
});

test('a new reader can register, view verification, borrow, and have staff record the return', async ({ page, browser }) => {
    const email = 'qa-borrowing@library.test';
    await register(page, email, 'Browser QA Borrower');
    await page.goto('/verify-email');
    await expect(page.getByRole('heading', { name: 'Check your email' })).toBeVisible();
    await page.goto('/books?filter=available');
    const first = page.locator('#catalogue-results article').first();
    const title = await first.locator('h3').innerText();
    await first.getByRole('button', { name: 'Borrow', exact: true }).click();
    await expect(page).toHaveURL(/\/my\/loans$/);
    await expect(page.getByRole('status')).toContainText('You borrowed');
    await expect(page.locator('article').filter({ hasText: title }).getByText('On loan', { exact: true })).toBeVisible();
    await expect(page.locator('article').filter({ hasText: title })).toContainText('Due date');

    const staffContext = await browser.newContext({ baseURL: 'http://127.0.0.1:8011' });
    try {
        const staff = await staffContext.newPage();
        await signIn(staff, 'librarian');
        await staff.goto('/desk?search=' + encodeURIComponent(email));
        await staff.getByRole('button', { name: /Record return of/ }).click();
        await expect(staff.getByRole('status')).toContainText('Return recorded');
        await page.reload();
        await expect(page.locator('article').filter({ hasText: title }).getByText('Returned', { exact: true }).first()).toBeVisible();
        await page.goto('/books?' + new URLSearchParams({ search: title, filter: 'available' }));
        await expect(page.getByRole('heading', { name: /1 book matching/ })).toBeVisible();
    } finally {
        await staffContext.close();
    }
});

test('staff book creation, editing, and confirmed deletion work end to end', async ({ page }) => {
    await signIn(page, 'librarian');
    await page.goto('/books/create');
    await page.getByLabel('Book title', { exact: true }).fill('Browser QA catalogue record');
    await page.getByLabel('Author', { exact: true }).selectOption({ index: 1 });
    await page.getByLabel('ISBN', { exact: true }).fill('9990000000001');
    await page.getByLabel('Publication year', { exact: true }).fill('2026');
    await page.getByRole('button', { name: 'Add book', exact: true }).click();
    await expect(page.getByRole('heading', { name: 'Browser QA catalogue record', exact: true })).toBeVisible();
    await page.getByRole('link', { name: 'Edit book', exact: true }).click();
    await page.getByLabel('Book title', { exact: true }).fill('Browser QA revised catalogue record');
    await page.getByRole('button', { name: 'Save changes', exact: true }).click();
    await expect(page.getByRole('heading', { name: 'Browser QA revised catalogue record', exact: true })).toBeVisible();
    await page.getByRole('button', { name: 'Delete book', exact: true }).click();
    await expect(page.getByRole('dialog', { name: 'Confirm this action' })).toBeVisible();
    await page.getByRole('button', { name: 'Cancel', exact: true }).click();
    await expect(page.getByRole('heading', { name: 'Browser QA revised catalogue record', exact: true })).toBeVisible();
    await page.getByRole('button', { name: 'Delete book', exact: true }).click();
    await page.getByRole('button', { name: 'Confirm', exact: true }).click();
    await expect(page).toHaveURL(/\/books$/);
    await expect(page.getByRole('status').first()).toContainText('Book removed');
});

test('administrator can search users and confirm role changes while their own role stays protected', async ({ page }) => {
    await register(page, 'qa-roles@library.test', 'Browser QA Roles');
    await page.getByRole('button', { name: 'Sign out', exact: true }).click();
    await signIn(page, 'admin');
    await page.goto('/admin/users?search=qa-roles%40library.test');
    await page.getByLabel('Role for Browser QA Roles', { exact: true }).selectOption('librarian');
    await page.getByRole('button', { name: 'Save role for Browser QA Roles' }).click();
    await expect(page.getByRole('dialog', { name: 'Confirm this action' })).toBeVisible();
    await page.getByRole('button', { name: 'Confirm', exact: true }).click();
    await expect(page.getByRole('status')).toContainText('updated to librarian');
    await page.goto('/admin/users?search=admin%40library.test');
    await expect(page.getByText('Your role is protected.', { exact: true })).toBeVisible();
    await expect(page.getByRole('button', { name: /Save role for/ })).toHaveCount(0);
});

test('profile updates, password changes, and account deletion remain connected', async ({ page }) => {
    await register(page, 'qa-profile@library.test', 'Browser QA Profile');
    await page.goto('/profile');
    await page.getByLabel('Full name', { exact: true }).fill('Updated QA Profile');
    await page.getByRole('button', { name: 'Save profile', exact: true }).click();
    await expect(page.getByRole('status')).toContainText('profile details have been saved');
    await page.locator('#current_password').fill('library-password');
    await page.locator('#new_password').fill('updated-library-password');
    await page.locator('#new_password_confirmation').fill('updated-library-password');
    await page.getByRole('button', { name: 'Update password', exact: true }).click();
    await expect(page.getByRole('status')).toContainText('password has been updated');
    await page.getByText('Delete my account', { exact: true }).click();
    await page.locator('#delete_password').fill('updated-library-password');
    await page.getByRole('button', { name: 'Permanently delete account', exact: true }).click();
    await page.getByRole('button', { name: 'Confirm', exact: true }).click();
    await expect(page).toHaveURL(/\/books$/);
    await expect(page.getByRole('link', { name: 'Sign in', exact: true })).toBeVisible();
});
