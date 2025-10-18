<?php

namespace Modules\INFRASTOCK\Console\Commands;

use Illuminate\Console\Command;
use Modules\SICA\Entities\Person;

class TestPhoneField extends Command
{
    protected $signature = 'infrastock:test-phone';
    protected $description = 'Prueba el campo de teléfono';

    public function handle()
    {
        $this->info('Creando usuario de prueba...');
        
        $person = Person::create([
            'first_name' => 'Test',
            'first_last_name' => 'User',
            'document_type' => '1',
            'document_number' => time(),
            'telephone1' => '3001234567',
            'address' => 'Test Address',
            'eps_id' => 1,
            'population_group_id' => 1,
            'pension_entity_id' => 1,
            'date_of_issue' => now()->format('Y-m-d'),
            'date_of_birth' => '1990-01-01',
            'gender' => '1',
            'marital_status' => '1',
            'blood_type' => '1',
            'military_card' => '1',
            'socioeconomical_status' => '1',
            'sisben_level' => '1'
        ]);
        
        $this->info('Persona creada con ID: ' . $person->id);
        $this->info('Phone: ' . $person->telephone1);
        
        // Verificar si se guardó correctamente
        $savedPerson = Person::find($person->id);
        $this->info('Phone guardado: ' . $savedPerson->telephone1);
    }
}
