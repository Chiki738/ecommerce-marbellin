<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

class TestDbConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-db-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        try {
            DB::connection()->getPdo();
            $this->info('¡Conexión a la base de datos exitosa!');
        } catch (\Exception $e) {
            $this->error('Error al conectar a la base de datos: ' . $e->getMessage());
        }
    }
}
