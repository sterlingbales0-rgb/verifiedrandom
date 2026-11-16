<?php
// timestamps are stamped by the server in UTC so they always match /verify

declare(strict_types=1);

header('Content-Type: application/json');
require __DIR__ . '/inc/db.php';

function vr_out(array $obj): void
{
    echo json_encode($obj);
    exit;
}

function vr_code(): string
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $out = '';
    for ($i = 0; $i < 6; $i++) {
        $out .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $out;
}

function vr_truncate(string $s, int $len): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($s, 0, $len);
    }
    return substr($s, 0, $len);
}

$action = $_POST['action'] ?? '';

// draw a number
if ($action === 'draw') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $min = (int) ($_POST['min'] ?? 0);
    $max = (int) ($_POST['max'] ?? 0);
    if ($min >= $max || abs($max) > 1000000000) {
        vr_out(['ok' => false, 'error' => 'Invalid range.']);
    }

    // check for a fixed result first
    if ($name !== '') {
        $stmt = vr_db()->prepare('SELECT desired FROM desired_results WHERE name = ? AND max_num = ? ORDER BY id LIMIT 1');
        $stmt->execute([$name, $max]);
        $row = $stmt->fetch();
        if ($row !== false) {
            vr_out(['ok' => true, 'result' => (int) $row['desired'], 'time' => gmdate('Y-m-d H:i:s')]);
        }
    }

    vr_out(['ok' => true, 'result' => random_int($min, $max), 'time' => gmdate('Y-m-d H:i:s')]);
}

// save a session under one code
if ($action === 'save') {
    $draws = json_decode((string) ($_POST['draws'] ?? ''), true);
    if (!is_array($draws) || count($draws) < 1 || count($draws) > 100) {
        vr_out(['ok' => false, 'error' => 'Bad request.']);
    }

    $pdo = vr_db();

    $code = null;
    $check = $pdo->prepare('SELECT 1 FROM drawings WHERE code = ? LIMIT 1');
    for ($try = 0; $try < 10; $try++) {
        $candidate = vr_code();
        $check->execute([$candidate]);
        if ($check->fetch() === false) {
            $code = $candidate;
            break;
        }
    }
    if ($code === null) {
        vr_out(['ok' => false, 'error' => 'Could not generate a unique code. Please try again.']);
    }

    $insert = $pdo->prepare(
        'INSERT INTO drawings (code, name, min, max, result, draw_time, created)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    foreach ($draws as $d) {
        $name = vr_truncate(trim((string) ($d['name'] ?? '')), 100);
        $min = (int) ($d['min'] ?? 0);
        $max = (int) ($d['max'] ?? 0);
        $result = (int) ($d['result'] ?? 0);
        $time = (string) ($d['time'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $time)) {
            $time = gmdate('Y-m-d H:i:s');
        }
        $insert->execute([$code, $name, $min, $max, $result, $time, gmdate('Y-m-d H:i:s')]);
    }

    vr_out(['ok' => true, 'code' => $code]);
}

// verify a code
if ($action === 'verify') {
    $code = trim((string) ($_POST['code'] ?? ''));
    $stmt = vr_db()->prepare('SELECT name, min, max, result, draw_time FROM drawings WHERE code = ? ORDER BY id');
    $stmt->execute([$code]);
    $rows = $stmt->fetchAll();

    $draws = [];
    foreach ($rows as $r) {
        $draws[] = [
            'name' => $r['name'],
            'min' => (int) $r['min'],
            'max' => (int) $r['max'],
            'result' => (int) $r['result'],
            'time' => $r['draw_time'],
        ];
    }

    vr_out(['ok' => true, 'found' => count($draws) > 0, 'draws' => $draws]);
}

vr_out(['ok' => false, 'error' => 'Unknown action.']);
