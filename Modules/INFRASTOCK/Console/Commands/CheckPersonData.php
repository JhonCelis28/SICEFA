<?php

namespace Modules\INFRASTOCK\Console\Commands;

use Illuminate\Console\Command;
use Modules\SICA\Entities\Person;

class CheckPersonData extends Command
{
    protected $signature = 'infrastock:check-person {id}';
    protected $description = 'Verifica los datos de una persona específica';

    public function handle()
    {
        $id = $this->argument('id');
        
        $this->info("Verificando persona ID: {$id}");
        
        $person = Person::find($id);
        
        if (!$person) {
            $this->error('Persona no encontrada');
            return;
        }
        
        $this->info("Persona encontrada: {$person->first_name} {$person->first_last_name}");
        $this->info("Document Type: " . ($person->document_type ?? 'N/A'));
        $this->info("Phone: " . ($person->phone ?? 'N/A'));
        $this->info("Document Number: " . ($person->document_number ?? 'N/A'));
        $this->info("Address: " . ($person->address ?? 'N/A'));
        
        // Mostrar todos los campos
        $this->info("Todos los campos:");
        foreach ($person->getAttributes() as $key => $value) {
            $this->line("  {$key}: " . ($value ?? 'N/A'));
        }
    }
}
