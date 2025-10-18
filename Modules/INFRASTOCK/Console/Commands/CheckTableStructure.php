<?php

namespace Modules\INFRASTOCK\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTableStructure extends Command
{
    protected $signature = 'infrastock:check-table-structure';
    protected $description = 'Verifica la estructura de la tabla people';

    public function handle()
    {
        $this->info('Verificando estructura de la tabla people...');
        
        $columns = DB::select('SHOW COLUMNS FROM people');
        
        $this->info('Campos relacionados con teléfono:');
        foreach ($columns as $column) {
            if (strpos($column->Field, 'phone') !== false || strpos($column->Field, 'telephone') !== false) {
                $this->line("  {$column->Field} - {$column->Type} - {$column->Null} - {$column->Default}");
            }
        }
        
        $this->info('Todos los campos de la tabla:');
        foreach ($columns as $column) {
            $this->line("  {$column->Field} - {$column->Type}");
        }
    }
}
