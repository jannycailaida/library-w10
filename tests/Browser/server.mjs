import { mkdtempSync, writeFileSync, mkdirSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, resolve, sep, basename } from 'node:path';
import { randomBytes } from 'node:crypto';
import { spawn, spawnSync } from 'node:child_process';

const temporaryRoot = mkdtempSync(join(tmpdir(), 'libraryhub-e2e-'));
const databaseFile = join(temporaryRoot, 'database.sqlite');
const compiledViews = join(temporaryRoot, 'views');
const isolatedStorage = join(temporaryRoot, 'storage');
const uploadedCovers = join(isolatedStorage, 'app', 'public', 'book-covers');
const publicRoot = resolve('public');
const routerFile = join(temporaryRoot, 'router.php');
writeFileSync(databaseFile, '');
mkdirSync(compiledViews);
for (const directory of [uploadedCovers, join(isolatedStorage, 'framework', 'sessions'), join(isolatedStorage, 'framework', 'cache', 'data'), join(isolatedStorage, 'logs')]) {
    mkdirSync(directory, { recursive: true });
}
const phpPath = (path) => "'" + path.replaceAll('\\', '/').replaceAll("'", "\\'") + "'";
writeFileSync(routerFile, `<?php
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
if (str_starts_with($uri, '/storage/')) {
    if (!preg_match('~\\A/storage/book-covers/([a-zA-Z0-9]{40}\\.(?:jpe?g|png|webp))\\z~', $uri, $matches)) {
        http_response_code(404);
        return;
    }
    $file = ${phpPath(uploadedCovers)}.'/'.$matches[1];
    if (!is_file($file)) {
        http_response_code(404);
        return;
    }
    header('Content-Type: '.mime_content_type($file));
    readfile($file);
    return;
}
if ($uri !== '/' && is_file(${phpPath(publicRoot)}.$uri)) {
    return false;
}
require ${phpPath(join(publicRoot, 'index.php'))};
`);
const environment = {
    ...process.env,
    APP_ENV: 'e2e',
    APP_DEBUG: 'false',
    APP_URL: 'http://127.0.0.1:8011',
    APP_KEY: 'base64:' + randomBytes(32).toString('base64'),
    APP_CONFIG_CACHE: join(temporaryRoot, 'config.php'),
    DB_URL: '',
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: databaseFile,
    CACHE_STORE: 'array',
    SESSION_DRIVER: 'file',
    MAIL_MAILER: 'array',
    QUEUE_CONNECTION: 'sync',
    VIEW_COMPILED_PATH: compiledViews,
    LARAVEL_STORAGE_PATH: isolatedStorage,
    BCRYPT_ROUNDS: '4',
};

function artisan(argumentsList) {
    const result = spawnSync('php', ['-d', 'variables_order=EGPCS', 'artisan', ...argumentsList, '--no-interaction'], {
        env: environment, encoding: 'utf8', windowsHide: true,
    });
    if (result.status !== 0) throw new Error(result.stderr || result.stdout || 'Artisan failed');
    return result.stdout;
}

function cleanTemporaryFiles() {
    const target = resolve(temporaryRoot);
    if (target.startsWith(resolve(tmpdir()) + sep) && basename(target).startsWith('libraryhub-e2e-')) {
        rmSync(target, { recursive: true, force: true });
    }
}

try {
    if (!artisan(['config:show', 'database.default']).includes('sqlite')
        || !artisan(['config:show', 'database.connections.sqlite.database']).includes(databaseFile)) {
        throw new Error('Browser tests require their dedicated temporary SQLite database.');
    }
    const publicDiskRoot = artisan(['config:show', 'filesystems.disks.public.root']).replaceAll('\\', '/');
    if (!publicDiskRoot.includes(join(isolatedStorage, 'app', 'public').replaceAll('\\', '/'))) {
        throw new Error('Browser tests require their dedicated temporary upload storage.');
    }
    artisan(['migrate', '--seed', '--force']);
} catch (error) {
    cleanTemporaryFiles();
    throw error;
}

const server = spawn('php', ['-d', 'variables_order=EGPCS', '-S', '127.0.0.1:8011', '-t', publicRoot, routerFile], {
    env: environment, stdio: ['ignore', 'inherit', 'pipe'], windowsHide: true,
});
server.stderr.on('data', (chunk) => {
    const messages = chunk.toString().split('\n').filter(line => line.trim()
        && !/\] 127\.0\.0\.1:\d+ (?:Accepted|Closing|\[200\]: (?:GET|HEAD) )/.test(line));
    if (messages.length) process.stderr.write(messages.join('\n') + '\n');
});
let stopping = false;
function stop() {
    if (stopping) return;
    stopping = true;
    if (process.platform === 'win32') {
        spawnSync('taskkill', ['/pid', String(server.pid), '/t', '/f'], { stdio: 'ignore', windowsHide: true });
    } else {
        server.kill('SIGTERM');
    }
    cleanTemporaryFiles();
}
process.on('SIGTERM', () => { stop(); process.exit(0); });
process.on('SIGINT', () => { stop(); process.exit(0); });
server.on('exit', (code) => {
    stop();
    process.exit(code ?? 0);
});
