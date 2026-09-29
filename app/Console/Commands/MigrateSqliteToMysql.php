<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class MigrateSqliteToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate-sqlite-to-mysql {--fresh : Whether to run migrate:fresh on MySQL before importing data} {--force : Force the migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all tables and records from SQLite database file to MySQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sqlitePath = database_path('database.sqlite');

        if (!File::exists($sqlitePath)) {
            $this->error("SQLite database file not found at: {$sqlitePath}");
            return Command::FAILURE;
        }

        $this->info("SQLite file found at: {$sqlitePath}");
        $this->info("Current active connection: " . config('database.default'));

        if (config('database.default') !== 'mysql') {
            $this->warn("Your active database connection is NOT set to 'mysql'. Please ensure you have configured MySQL in your .env file.");
            if (!$this->option('force') && !$this->confirm('Do you want to proceed anyway?')) {
                return Command::FAILURE;
            }
        }

        // Configure connection dynamically
        config(['database.connections.sqlite_import' => [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
            'prefix' => '',
        ]]);

        $this->info('Running migrations on target database...');
        if ($this->option('fresh')) {
            $this->info('Dropping all existing tables on target database manually...');
            $targetConnection = DB::connection();
            $driver = $targetConnection->getDriverName();
            
            if ($driver === 'mysql') {
                $targetConnection->statement('SET FOREIGN_KEY_CHECKS=0;');
                $tables = $targetConnection->select('SHOW TABLES');
                $dbName = DB::getDatabaseName();
                $key = 'Tables_in_' . $dbName;
                foreach ($tables as $table) {
                    if (isset($table->$key)) {
                        $tableName = $table->$key;
                        $targetConnection->statement("DROP TABLE IF EXISTS `{$tableName}`");
                    }
                }
                $targetConnection->statement('SET FOREIGN_KEY_CHECKS=1;');
            } else if ($driver === 'sqlite') {
                $targetConnection->statement('PRAGMA foreign_keys = OFF;');
                $tables = $targetConnection->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($tables as $table) {
                    $targetConnection->statement("DROP TABLE IF EXISTS \"{$table->name}\"");
                }
                $targetConnection->statement('PRAGMA foreign_keys = ON;');
            }
        }

        $this->call('migrate', ['--force' => true]);

        $sqliteDb = DB::connection('sqlite_import');
        
        try {
            $tables = $sqliteDb->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        } catch (\Exception $e) {
            $this->error("Failed to query SQLite database tables: " . $e->getMessage());
            return Command::FAILURE;
        }

        $exclude = ['migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];

        $this->info('Disabling foreign key checks on target database...');
        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $migratedTables = 0;
        $totalRecords = 0;

        foreach ($tables as $table) {
            $tableName = $table->name;

            if (in_array($tableName, $exclude)) {
                $this->line("Skipping system table: {$tableName}");
                continue;
            }

            if (!Schema::hasTable($tableName)) {
                $this->warn("Table '{$tableName}' does not exist on target database. Skipping.");
                continue;
            }

            $this->info("Migrating table: {$tableName}...");

            // Truncate table on target database to avoid primary key conflicts
            DB::table($tableName)->truncate();

            // Fetch records from SQLite
            $rows = $sqliteDb->table($tableName)->get();
            $count = 0;
            
            if ($rows->isNotEmpty()) {
                $rowArrays = [];
                foreach ($rows as $row) {
                    $rowArrays[] = (array) $row;
                }
                
                $chunks = array_chunk($rowArrays, 100);
                foreach ($chunks as $chunk) {
                    DB::table($tableName)->insert($chunk);
                    $count += count($chunk);
                }
            }

            $this->info("Successfully migrated table '{$tableName}': {$count} records.");
            $migratedTables++;
            $totalRecords += $count;
        }

        $this->info('Re-enabling foreign key checks on target database...');
        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->info("Migration completed successfully! Migrated {$migratedTables} tables and {$totalRecords} records total.");
        return Command::SUCCESS;
    }
}
