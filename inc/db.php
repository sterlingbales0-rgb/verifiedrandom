<?php
// the sqlite file is created in data/ on first run
// needs pdo_sqlite, which cPanel has on by default

declare(strict_types=1);

function vr_db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $pdo = new PDO('sqlite:' . $dir . '/verifiedrandom.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("CREATE TABLE IF NOT EXISTS drawings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT NOT NULL,
        name TEXT NOT NULL DEFAULT '',
        min INTEGER NOT NULL,
        max INTEGER NOT NULL,
        result INTEGER NOT NULL,
        draw_time TEXT NOT NULL,
        created TEXT NOT NULL
    )");
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_drawings_code ON drawings(code)');

    $pdo->exec("CREATE TABLE IF NOT EXISTS desired_results (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        max_num INTEGER NOT NULL,
        desired INTEGER NOT NULL,
        created TEXT NOT NULL
    )");

    return $pdo;
}
