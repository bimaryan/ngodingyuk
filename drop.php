<?php

$config = include('config/database.php');

try {
    $pdo = new PDO("mysql:host={$config['host']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mengecek apakah database sudah ada
    $databaseName = $config['database'];
    $checkDatabase = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$databaseName'")->fetch();

    if (!$checkDatabase) {
        // Jika database belum ada, buat database
        $pdo->exec("CREATE DATABASE $databaseName");
        echo "Database '$databaseName' created successfully.\n";
    }

    // Membuat koneksi baru ke database yang telah dibuat atau yang sudah ada
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Jalankan migrasi setelah membuat koneksi baru
    function runMigration($pdo, $migrationFile, $method = 'up')
    {
        require $migrationFile;
        $migration = new Migration($pdo);
        if ($method === 'up') {
            $migration->up();
        } elseif ($method === 'down') {
            $migration->down();
        }
    }

    // Rollback semua migrasi
    $migrations = glob('database/migrations/*.php');
    rsort($migrations); // Urutkan mundur untuk rollback
    foreach ($migrations as $migration) {
        runMigration($pdo, $migration, 'down');
    }
    
    sort($migrations);
    // Jalankan kembali semua migrasi
    foreach ($migrations as $migration) {
        runMigration($pdo, $migration);
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
