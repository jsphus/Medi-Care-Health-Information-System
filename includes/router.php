<?php
$routes = require __DIR__ . '/routes.php';

$request = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$baseDir = basename(dirname(__DIR__));

// Redirect index.php to root URL
if ($request === 'index.php') {
    header('Location: /');
    exit;
}


// Adjust request if project folder name is included in path
if (strpos($request, $baseDir) === 0) {
    $request = substr($request, strlen($baseDir));
    $request = trim($request, '/');
}

// Route handling
if (array_key_exists($request, $routes)) {
    require __DIR__ . '/../' . $routes[$request];
} else {
    http_response_code(404);
    include __DIR__ . '/../views/partials/header.php';
    echo "<main style='padding:50px;text-align:center;'>
            <h1>404 - Page Not Found</h1>
            <p>The route '<b>{$request}</b>' does not exist.</p>
          </main>";
    include __DIR__ . '/../views/partials/footer.php';
}
