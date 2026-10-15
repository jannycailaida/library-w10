import { mkdtempSync, writeFileSync, mkdirSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, resolve, sep, basename } from 'node:path';
import { randomBytes } from 'node:crypto';
import { spawn, spawnSync } from 'node:child_process';

const temporaryRoot = mkdtempSync(join(tmpdir(), 'libraryhub-e2e-'));
const databaseFile = join(temporaryRoot, 'database.sqlite');
const compiledViews = join(temporaryRoot, 'views');
writeFileSync(databaseFile, '');
mkdirSync(compiledViews);
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
    BCRYPT_ROUNDS: '4',
};

function artisan(argumentsList) {
    const result = spawnSync('php', ['artisan', ...argumentsList, '--no-interaction'], {
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
    artisan(['migrate', '--seed', '--force']);
} catch (error) {
    cleanTemporaryFiles();
    throw error;
}

const server = spawn('php', ['artisan', 'serve', '--host=127.0.0.1', '--port=8011', '--tries=1', '--no-reload', '--no-interaction'], {
    env: environment, stdio: 'inherit', windowsHide: true,
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
