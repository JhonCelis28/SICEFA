<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\SICA\Entities\Person;
use Modules\SICA\Entities\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * Seeder para crear un usuario instructor de prueba
 */
class CreateInstructorTestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            // Buscar el rol de Instructor
            $role = Role::where('name', 'Instructor')
                ->where('app_id', 19) // app_id de INFRASTOCK
                ->first();

            if (!$role) {
                $this->command->error('El rol "Instructor" no existe. Por favor, ejecuta primero INFRASTOCKRolesSeeder.');
                return;
            }

            // Verificar si el usuario ya existe
            $existingUser = User::where('email', 'instructor@test.com')->first();
            
            if ($existingUser) {
                // Si existe, solo asegurar que tenga el rol de Instructor
                $existingUser->roles()->syncWithoutDetaching([$role->id]);
                $this->command->info('Usuario instructor ya existe. Rol asignado/verificado.');
                DB::commit();
                return;
            }

            // Crear la persona
            $person = Person::create([
                'first_name' => 'Instructor',
                'first_last_name' => 'Prueba',
                'second_last_name' => 'Test',
                'document_type' => 'Cédula de ciudadanía',
                'document_number' => '1234567890',
                'telephone1' => '3001234567',
                'address' => 'Dirección de prueba',
                'eps_id' => 1, // Valor por defecto para EPS
                'population_group_id' => 1, // Valor por defecto para grupo poblacional
                'pension_entity_id' => 1, // Valor por defecto para entidad de pensión
                'date_of_issue' => now()->format('Y-m-d'), // Fecha de emisión por defecto
                'date_of_birth' => '1990-01-01', // Fecha de nacimiento por defecto
                'gender' => 'Masculino', // Género por defecto
                'marital_status' => 'Soltero(a)', // Estado civil por defecto
                'blood_type' => 'O+', // Tipo de sangre por defecto
                'military_card' => null, // Libreta militar por defecto
                'socioeconomical_status' => 'No registra', // Estrato socioeconómico por defecto
                'sisben_level' => null, // Nivel SISBEN por defecto
            ]);

            // Crear el usuario
            $user = User::create([
                'person_id' => $person->id,
                'email' => 'instructor@test.com',
                'password' => Hash::make('instructor123'), // Contraseña: instructor123
                'nickname' => 'Instructor Prueba',
                'email_verified_at' => now(),
            ]);

            // Asignar rol de Instructor
            $user->roles()->attach($role->id);

            DB::commit();

            $this->command->info('Usuario instructor de prueba creado exitosamente!');
            $this->command->info('Email: instructor@test.com');
            $this->command->info('Contraseña: instructor123');
            $this->command->info('Rol: Instructor');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error al crear usuario instructor: ' . $e->getMessage());
        }
    }
}

