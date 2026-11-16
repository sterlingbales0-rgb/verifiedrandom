<?php
// hidden admin page, only reachable by direct link
// password is the VR_ADMIN_PASSWORD constant below

declare(strict_types=1);

session_start();
header('X-Robots-Tag: noindex, nofollow');

const VR_ADMIN_PASSWORD = 'Msb5f9123!';

require __DIR__ . '/inc/db.php';

function vr_out(array $obj): void
{
    header('Content-Type: application/json');
    echo json_encode($obj);
    exit;
}

// json api
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = (string) $_POST['action'];

    if ($action === 'login') {
        if (hash_equals(VR_ADMIN_PASSWORD, (string) ($_POST['password'] ?? ''))) {
            $_SESSION['vr_admin'] = true;
            $_SESSION['vr_token'] = bin2hex(random_bytes(24));
            vr_out(['ok' => true, 'token' => $_SESSION['vr_token']]);
        }
        vr_out(['ok' => false, 'error' => 'Incorrect password.']);
    }

    // session cookie or the token from login
    $token = (string) ($_SERVER['HTTP_X_ADMIN_TOKEN'] ?? '');
    $authorized = !empty($_SESSION['vr_admin'])
        && ($token === ''
            || hash_equals((string) ($_SESSION['vr_token'] ?? ''), $token));
    if (!$authorized) {
        vr_out(['ok' => false, 'error' => 'Not authorized.']);
    }

    if ($action === 'list') {
        $rows = vr_db()->query('SELECT id, name, max_num, desired, created FROM desired_results ORDER BY id')->fetchAll();
        $entries = [];
        foreach ($rows as $r) {
            $entries[] = [
                'id' => (int) $r['id'],
                'name' => $r['name'],
                'max' => (int) $r['max_num'],
                'desired' => (int) $r['desired'],
                'created' => $r['created'],
            ];
        }
        vr_out(['ok' => true, 'entries' => $entries]);
    }

    if ($action === 'add') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $maxRaw = trim((string) ($_POST['max'] ?? ''));
        $desiredRaw = trim((string) ($_POST['desired'] ?? ''));
        if ($name === '' || !preg_match('/^-?\d+$/', $maxRaw) || !preg_match('/^-?\d+$/', $desiredRaw)) {
            vr_out(['ok' => false, 'error' => 'All fields are required.']);
        }
        $max = (int) $maxRaw;
        $desired = (int) $desiredRaw;
        $stmt = vr_db()->prepare('INSERT INTO desired_results (name, max_num, desired, created) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $max, $desired, gmdate('Y-m-d H:i:s')]);
        vr_out(['ok' => true, 'entry' => ['id' => (int) vr_db()->lastInsertId(), 'name' => $name, 'max' => $max, 'desired' => $desired, 'created' => gmdate('Y-m-d H:i:s')]]);
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = vr_db()->prepare('DELETE FROM desired_results WHERE id = ?');
        $stmt->execute([$id]);
        vr_out(['ok' => true]);
    }

    vr_out(['ok' => false, 'error' => 'Unknown action.']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>.</title>
	<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%236b66c6'/%3E%3Ctext x='16' y='22' font-family='Georgia' font-size='15' fill='white' text-anchor='middle'%3EVR%3C/text%3E%3C/svg%3E">
	<link rel="stylesheet" type="text/css" href="styles/base.css">
	<link rel="stylesheet" type="text/css" href="styles/admin.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
	<link href="https://fonts.googleapis.com/css2?family=Nixie+One&display=swap" rel="stylesheet">
</head>
<body>
	<header>
		<a href="index.php"><h1>VerifiedRandom.org</h1></a>
	</header>
	<div class="page" id="adminMain">
		<div class="card" id="gate">
			<h3>Restricted</h3>
			<form id="gateForm" onsubmit="return false;">
				<input type="password" id="pw" placeholder="Password" autocomplete="off">
				<button type="submit" class="solid" id="gateBtn">Unlock</button>
				<div id="gateMsg"></div>
			</form>
		</div>
		<div id="panel" class="hidden">
			<div class="card">
				<h3>Desired Results</h3>
				<p style="font-size:88%; color:#444; margin-top:-8px;">When a drawing is made on the homepage with a Name and Max number that both match an entry below exactly, the desired result is returned instead of a random number. It behaves as a normal drawing, including the verification code and UTC timestamps on the /verify page. An entry can be drawn again as long as it is still listed here; delete it to prevent any further duplication.</p>
				<form id="addForm" onsubmit="return false;">
					<label for="aName">Name (must match exactly)</label>
					<input type="text" id="aName" maxlength="100" autocomplete="off">
					<label for="aMax">Max Number (must match exactly)</label>
					<input type="number" id="aMax">
					<label for="aDesired">Desired Result</label>
					<input type="number" id="aDesired">
					<button type="submit" class="solid" id="addBtn">Add</button>
					<div id="addMsg"></div>
				</form>
			</div>
			<div id="entryWrap">
				<table id="entryTable">
					<thead>
						<tr>
							<th>Name</th>
							<th>Max Number</th>
							<th>Desired Result</th>
							<th>Added (UTC)</th>
							<th></th>
						</tr>
					</thead>
					<tbody id="entryBody"></tbody>
				</table>
				<div id="emptyMsg" class="hidden">No desired results configured.</div>
			</div>
		</div>
	</div>
	<script src="js/api-client.js?v=3"></script>
	<script src="js/adminPage.js?v=3"></script>
</body>
</html>
