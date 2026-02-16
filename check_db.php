<?php
try {
    $pdo = new PDO('sqlite:var/data.db'); // Assuming sqlite based on previous logs, adjust if needed
    $stmt = $pdo->query('PRAGMA table_info(students)');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $found = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'role') {
            $found = true;
            break;
        }
    }
    echo $found ? 'Column found' : 'Column NOT found';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
