<?php
// routes/web.php

$route = $_SERVER['REQUEST_URI'];

$blockedDirectories = ['app', 'assets', 'config', 'database', 'resources', 'routes', 'storage'];

// Periksa apakah URL mengarah ke direktori yang diblokir
foreach ($blockedDirectories as $blockedDir) {
    $blockedDirPattern = '/' . preg_quote($blockedDir, '/') . '/';
    if (preg_match($blockedDirPattern, $route)) {
        header("HTTP/1.0 404 Not Found");
        echo "404 NOT FOUND";
        exit;
    }
}

if ($route === "/resources/page/Home") {
    echo "Error";
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request
    switch ($route) {
        case '/':
            // require 'resources/page/test/Home/index.php';
            require 'resources/page/test/Home/index.php';
            break;
        case '/page2':
            require 'templates/page2.php';
            break;
        // Daftar rute yang diperbolehkan (whitelist)
        case '/allowed-route':
            require 'allowed-route.php';
            break;
        default:
            echo "404 Not Found";
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request
    switch ($route) {
        case '/':
            // Process the form data or perform any other actions
            // ...

            // Redirect to the main page after processing
            // header('Location: /');
            // // exit();
            break;
        default:
            echo "404 Not Found";
            break;
    }
}
