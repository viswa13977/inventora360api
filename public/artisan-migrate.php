<?php
// Secure with a secret token to avoid abuse
$token = $_GET['token'] ?? '';
if ($token !== 'YOUR_SECRET_TOKEN_HERE') {
    http_response_code(403);
    exit('Unauthorized');
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run migrations
$exitCode = $kernel->call('migrate', ['--force' => true]);

echo "Migration status: " . $exitCode;
