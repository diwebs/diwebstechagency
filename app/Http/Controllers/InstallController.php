<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InstallController extends Controller
{
    public function __construct()
    {
        if (file_exists(storage_path('installed'))) {
            abort(404);
        }
    }

    public function showInstallForm()
    {
        $requirements = $this->checkRequirements();
        $isCompatible = !in_array(false, $requirements);

        return view('install.index', compact('requirements', 'isCompatible'));
    }

    public function setupDatabase(Request $request)
    {
        $request->validate([
            'db_connection' => 'required|in:sqlite,mysql',
            'db_host' => 'required_if:db_connection,mysql|nullable|string',
            'db_port' => 'required_if:db_connection,mysql|nullable|integer',
            'db_database' => 'required|string',
            'db_username' => 'required_if:db_connection,mysql|nullable|string',
            'db_password' => 'nullable|string',
        ]);

        $connection = $request->db_connection;
        $database = $request->db_database;

        if ($connection === 'sqlite') {
            $dbPath = $database;
            // Check if path is relative
            if (!\Illuminate\Support\Str::startsWith($dbPath, '/') && !\Illuminate\Support\Str::startsWith($dbPath, '\\') && !str_contains($dbPath, ':\\')) {
                // If it starts with 'database/', resolve from base_path, otherwise database_path
                if (\Illuminate\Support\Str::startsWith($dbPath, 'database/')) {
                    $dbPath = base_path($dbPath);
                } else {
                    $dbPath = database_path($dbPath);
                }
            }
            
            $dbPath = str_replace('\\', '/', $dbPath);
            
            // Create sqlite file if it doesn't exist
            if (!file_exists($dbPath)) {
                if (!touch($dbPath)) {
                    return response()->json(['message' => 'Failed to create SQLite database file. Check directory permissions.'], 422);
                }
            }

            // Write .env config
            $this->updateEnv([
                'DB_CONNECTION' => 'sqlite',
                'DB_DATABASE' => $dbPath,
                'DB_HOST' => '',
                'DB_PORT' => '',
                'DB_USERNAME' => '',
                'DB_PASSWORD' => '',
            ]);
        } else {
            // Test MySQL Connection
            $config = [
                'driver' => 'mysql',
                'host' => $request->db_host,
                'port' => $request->db_port,
                'database' => $request->db_database,
                'username' => $request->db_username,
                'password' => $request->db_password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ];

            try {
                // Set temporary connection config
                config(['database.connections.install_test' => $config]);
                DB::connection('install_test')->getPdo();
            } catch (\Exception $e) {
                return response()->json(['message' => 'Database connection failed: ' . $e->getMessage()], 422);
            }

            // Write .env config
            $this->updateEnv([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_database,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password ?? '',
            ]);
        }

        // Clear config cache to apply new database configuration
        Artisan::call('config:clear');

        return response()->json(['message' => 'Database configured successfully. Proceeding to admin setup.']);
    }

    public function setupAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:12|confirmed',
        ]);

        try {
            // 1. Run Migrations
            Artisan::call('migrate:fresh', ['--force' => true]);

            // 2. Run Database Seeds
            Artisan::call('db:seed', ['--force' => true]);

            // 3. Delete Default Seeded Admin
            User::where('role', 'super_admin')->delete();

            // 4. Create custom admin user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'super_admin',
                'status' => 'active',
                'country' => 'Nigeria', // default value
            ]);

            // 5. Create the installation lock file
            file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));

            // Clear configurations
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return response()->json(['message' => 'Installation completed successfully! Redirecting...']);
        } catch (\Exception $e) {
            Log::error('Installation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Installation failed: ' . $e->getMessage()], 500);
        }
    }

    private function checkRequirements()
    {
        return [
            'PHP Version (>= 8.2)' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'BCMath Extension'     => extension_loaded('bcmath'),
            'Ctype Extension'      => extension_loaded('ctype'),
            'Fileinfo Extension'   => extension_loaded('fileinfo'),
            'JSON Extension'       => extension_loaded('json'),
            'Mbstring Extension'   => extension_loaded('mbstring'),
            'OpenSSL Extension'    => extension_loaded('openssl'),
            'PDO Extension'        => extension_loaded('pdo'),
            'Tokenizer Extension'  => extension_loaded('tokenizer'),
            'XML Extension'        => extension_loaded('xml'),
            'Env Writable'         => is_writable(base_path('.env')) || is_writable(base_path()),
            'Storage Writable'     => is_writable(storage_path()) && is_writable(storage_path('logs')),
            'Cache Writable'       => is_writable(base_path('bootstrap/cache')),
        ];
    }

    private function updateEnv($data)
    {
        $path = base_path('.env');
        if (!file_exists($path)) {
            $path = base_path('.env.example');
        }

        $content = file_get_contents($path);

        foreach ($data as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
            } else {
                $content .= "\n{$key}=\"{$value}\"";
            }
        }

        file_put_contents(base_path('.env'), $content);
    }
}
