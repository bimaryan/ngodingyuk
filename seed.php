<?php
include "app/Database/Migrations.php";

$config = include('config/database.php');
$pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']}", $config['username'], $config['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function runSeeder($pdo, $seedFile)
{
    require_once $seedFile;
    $className = pathinfo($seedFile, PATHINFO_FILENAME);
    $seedClass = "database\\seeder\\$className";
    $seed = new $seedClass($pdo);
    echo $seed->run();
}

$seeder = glob('database/seeder/*.php');
sort($seeder);
foreach ($seeder as $file) {
    runSeeder($pdo, $file);
}
