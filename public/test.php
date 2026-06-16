<?php
// Boot Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

try {
    // 1. Ensure we have at least one client user
    $user = User::where('role', 'client')->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'status' => 'active'
        ]);
        echo "Created new test client user.\n";
    } else {
        echo "Found existing client user: {$user->email}\n";
    }

    // 2. Log in the user
    auth()->login($user);
    echo "Logged in as {$user->email}.\n";

    // 3. Instantiate controller
    $controller = app(\App\Http\Controllers\PortalController::class);
    $req = Request::create('/portal', 'GET');
    $req->setUserResolver(function() use ($user) { return $user; });

    echo "Running PortalController@dashboard...\n";
    $res = $controller->dashboard($req);
    echo "Controller returned response.\n";

    echo "Attempting to render view template...\n";
    $html = $res->render();
    echo "SUCCESS! Rendered template successfully without errors.\n";
    echo "Length of rendered HTML: " . strlen($html) . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
} catch (\Throwable $t) {
    echo "THROWABLE: " . $t->getMessage() . "\n";
    echo "File: " . $t->getFile() . " Line: " . $t->getLine() . "\n";
    echo $t->getTraceAsString() . "\n";
}
