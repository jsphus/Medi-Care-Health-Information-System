<?php   
// Router script for PHP built-in server
// Usage: php -S localhost:8000 router.php

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$request = trim($requestPath, '/');

// Remove query string from request path
$request = strtok($request, '?');

// If it's the root, set to empty string
if ($request === 'index.php' || $request === '') {
    $request = '';
}

// If it's a static file that exists, serve it directly
if ($request && file_exists(__DIR__ . '/' . $request)) {
    $extension = pathinfo($request, PATHINFO_EXTENSION);
    $staticExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'svg', 'woff', 'woff2', 'ttf', 'eot', 'pdf', 'zip'];
    if (in_array(strtolower($extension), $staticExtensions)) {
        return false; // Let PHP serve the static file
    }
}

// If it's a directory, try index.php
if ($request && is_dir(__DIR__ . '/' . $request)) {
    if (file_exists(__DIR__ . '/' . $request . '/index.php')) {
        require __DIR__ . '/' . $request . '/index.php';
        return true;
    }
}

// Otherwise, route through the application router
// Set the REQUEST_URI to the cleaned request for the router
$_SERVER['REQUEST_URI'] = '/' . $request . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
require __DIR__ . '/index.php';

