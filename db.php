<?php
include "app/Database/Migrations.php";

$config = include('config/database.php');

// Dapatkan argumen dari baris perintah
$cliArgs = getopt('p:', ['path:']);

// Ambil nilai dari opsi --path
$migrationPath = isset($cliArgs['path']) ? $cliArgs['path'] : 'database/migrations/';

// Jika path berakhir dengan .php, anggap itu adalah file tunggal
if (pathinfo($migrationPath, PATHINFO_EXTENSION) === 'php') {
    $migrations = [$migrationPath];
} else {
    // Jika tidak, cari semua file PHP di direktori tersebut
    $migrations = glob("$migrationPath*.php");
    rsort($migrations); // Urutkan mundur untuk rollback
}

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
    function runMigration($pdo, $migrationFile, $migrationPath, $method = 'up')
    {
        if (file_exists($migrationFile)) {
            require_once $migrationFile;
            $className = pathinfo($migrationFile, PATHINFO_FILENAME);
            $migrationClass = "database\\migrations\\$className"; // Sertakan namespace
            $migration = new $migrationClass($pdo);
            if ($method === 'up') {
                echo $migration->up() . "\n";
            } elseif ($method === 'down') {
                echo $migration->down() . "\n";
            }
        } else {
            echo "File $migrationFile not found in $migrationPath\n";
        }
        // require_once $migrationFile;
        // $className = pathinfo($migrationFile, PATHINFO_FILENAME);
        // $migrationClass = "database\\migrations\\$className"; // Sertakan namespace
        // $migration = new $migrationClass($pdo);
        // if ($method === 'up') {
        //     echo $migration->up() . "\n";
        // } elseif ($method === 'down') {
        //     echo $migration->down() . "\n";
        // }
    }

    // Rollback semua migrasi
    // $migrations = glob("$migrationPath*.php");
    rsort($migrations); // Urutkan mundur untuk rollback
    foreach ($migrations as $migration) {
        runMigration($pdo, $migration, $migrationPath,'down');
    }
    
    sort($migrations);
    // Jalankan kembali semua migrasi
    foreach ($migrations as $migration) {
        runMigration($pdo, $migration, $migrationPath,);
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
