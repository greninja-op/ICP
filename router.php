<?php
/**
 * Router for PHP Built-in Server
 * This handles routing for the development server
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|ico|svg|woff|woff2|ttf|eot)$/', $uri)) {
    return false; // Let PHP serve the file
}

// Route API requests to the backend folder
if (strpos($uri, '/backend/api/') === 0) {
    // Remove /backend/api/ prefix and route to the actual file
    $file = __DIR__ . '/backend/api' . substr($uri, strlen('/backend/api'));
    
    if (file_exists($file) && is_file($file)) {
        require $file;
        return true;
    }
}

// For all other requests, serve index.html if it exists
if ($uri === '/' || $uri === '') {
    $indexFile = __DIR__ . '/index.html';
    if (file_exists($indexFile)) {
        require $indexFile;
        return true;
    }
}

// Try to serve the requested file
$requestedFile = __DIR__ . $uri;
if (file_exists($requestedFile) && is_file($requestedFile)) {
    return false; // Let PHP serve it
}

// 404 for everything else
http_response_code(404);
echo json_encode(['error' => 'Not Found', 'path' => $uri]);
return true;
