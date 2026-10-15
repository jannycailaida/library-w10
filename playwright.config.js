import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/Browser',
    fullyParallel: false,
    workers: 1,
    timeout: 90000,
    expect: { timeout: 10000 },
    reporter: [['list'], ['html', { open: 'never' }]],
    use: {
        baseURL: 'http://127.0.0.1:8011',
        browserName: 'chromium',
        locale: 'en-US',
        timezoneId: 'Asia/Manila',
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
    },
    webServer: {
        command: 'node tests/Browser/server.mjs',
        url: 'http://127.0.0.1:8011/login',
        reuseExistingServer: false,
        timeout: 120000,
    },
});
