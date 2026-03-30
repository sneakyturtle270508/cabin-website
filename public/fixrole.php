<?php

$dbPath = '/var/www/html/database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);

// Check roles
$stmt = $pdo->query('SELECT * FROM roles');
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Roles:\n";
foreach ($roles as $r) {
    echo '- '.$r['handle'].' ('.$r['title'].")\n";
}

// Check role_user
$stmt = $pdo->query('SELECT * FROM role_user');
$roleUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nRole-User mappings:\n";
foreach ($roleUsers as $ru) {
    echo '- user_id: '.$ru['user_id'].', role_id: '.$ru['role_id']."\n";
}

// Check role_permissions
$stmt = $pdo->query('SELECT * FROM role_permissions');
$perms = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nRole permissions:\n";
foreach ($perms as $p) {
    echo '- role_id: '.$p['role_id'].', permission: '.$p['permission']."\n";
}

// Ensure super role exists and user has it
$stmt = $pdo->query("SELECT id FROM roles WHERE handle = 'super'");
$superRole = $stmt->fetch();
if (! $superRole) {
    $pdo->exec("INSERT INTO roles (title, handle) VALUES ('Super', 'super')");
    echo "\nCreated super role\n";
    $stmt = $pdo->query("SELECT id FROM roles WHERE handle = 'super'");
    $superRole = $stmt->fetch();
}

$stmt = $pdo->query("SELECT id FROM users WHERE email = 'admin@example.com'");
$user = $stmt->fetch();

if ($superRole && $user) {
    $stmt = $pdo->prepare('SELECT * FROM role_user WHERE user_id = ? AND role_id = ?');
    $stmt->execute([$user['id'], $superRole['id']]);
    if (! $stmt->fetch()) {
        $pdo->exec("INSERT INTO role_user (user_id, role_id) VALUES ({$user['id']}, {$superRole['id']})");
        echo "\nAssigned super role to admin\n";
    }

    $stmt = $pdo->prepare('SELECT * FROM role_permissions WHERE role_id = ? AND permission = ?');
    $stmt->execute([$superRole['id'], 'super']);
    if (! $stmt->fetch()) {
        $pdo->exec("INSERT INTO role_permissions (role_id, permission) VALUES ({$superRole['id']}, 'super')");
        echo "Added super permission\n";
    }
}

echo "\nDone!\n";
