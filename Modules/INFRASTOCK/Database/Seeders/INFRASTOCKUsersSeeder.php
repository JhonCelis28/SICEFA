<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\SICA\Entities\Person;
use Modules\SICA\Entities\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * @file INFRASTOCKUsersSeeder.php
 * @brief Seeder para crear usuarios de prueba del módulo INFRASTOCK.
 * 
 * Funcionalidades:
 * - Crea registros en la tabla 'people' para cada rol si no existen.
 * - Crea usuarios en la tabla 'users' vinculados a dichas personas.
 * - Asigna automáticamente los roles correspondientes de INFRASTOCK.
 * 
 * Cambios realizados:
 * - Se definieron 9 usuarios de prueba (Admin, Agroindustria, Operario, Aseo, Ganadería, Convivencia, Vigilancia, Ciencias Básicas y Psicóla).
 * - Se asignaron contraseñas únicas siguiendo el patrón: [nombre]infrastock
 * - Se excluyó el rol de Instructor ya que su gestión es externa al módulo.
 * 
 * Credenciales:
 * - Admin: admin@infrastock.com / admininfrastock
 * - Agroindustria: agroindustria@infrastock.com / agroindustriainfrastock
 * - Operario: operario@infrastock.com / operarioinfrastock
 * - Aseo: aseo@infrastock.com / aseoinfrastock
 * - Ganadería: ganaderia@infrastock.com / ganaderiainfrastock
 * - Convivencia: convivencia@infrastock.com / convivenciainfrastock
 * - Vigilancia: vigilancia@infrastock.com / vigilanciainfrastock
 * - Ciencias Básicas: cienciasbasicas@infrastock.com / cienciasbasicasinfrastock
 * - Psicóla: psicola@infrastock.com / psicolainfrastock
 */
class INFRASTOCKUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersData = [
            [
                'email' => 'admin@infrastock.com',
                'role_slug' => 'infrastock.admin',
                'name' => 'Admin Infastock',
                'first_name' => 'Admin',
                'first_last_name' => 'Infastock',
                'document' => '10002000',
                'password' => 'admininfrastock'
            ],
            [
                'email' => 'agroindustria@infrastock.com',
                'role_slug' => 'infrastock.agroindustria',
                'name' => 'Agroindustria Infastock',
                'first_name' => 'Agroindustria',
                'first_last_name' => 'Infastock',
                'document' => '10002001',
                'password' => 'agroindustriainfrastock'
            ],
            [
                'email' => 'operario@infrastock.com',
                'role_slug' => 'operario',
                'name' => 'Operario Infastock',
                'first_name' => 'Operario',
                'first_last_name' => 'Infastock',
                'document' => '10002002',
                'password' => 'operarioinfrastock'
            ],
            [
                'email' => 'aseo@infrastock.com',
                'role_slug' => 'aseo',
                'name' => 'Aseo Infastock',
                'first_name' => 'Aseo',
                'first_last_name' => 'Infastock',
                'document' => '10002003',
                'password' => 'aseoinfrastock'
            ],
            [
                'email' => 'ganaderia@infrastock.com',
                'role_slug' => 'infrastock.ganaderia',
                'name' => 'Ganaderia Infastock',
                'first_name' => 'Ganaderia',
                'first_last_name' => 'Infastock',
                'document' => '10002004',
                'password' => 'ganaderiainfrastock'
            ],
            [
                'email' => 'convivencia@infrastock.com',
                'role_slug' => 'infrastock.centro-convivencia',
                'name' => 'Convivencia Infastock',
                'first_name' => 'Convivencia',
                'first_last_name' => 'Infastock',
                'document' => '10002005',
                'password' => 'convivenciainfrastock'
            ],
            [
                'email' => 'vigilancia@infrastock.com',
                'role_slug' => 'infrastock.vigilancia',
                'name' => 'Vigilancia Infastock',
                'first_name' => 'Vigilancia',
                'first_last_name' => 'Infastock',
                'document' => '10002006',
                'password' => 'vigilanciainfrastock'
            ],
            [
                'email' => 'cienciasbasicas@infrastock.com',
                'role_slug' => 'infrastock.ciencias-basicas',
                'name' => 'Ciencias Basicas Infastock',
                'first_name' => 'Ciencias',
                'first_last_name' => 'Basicas',
                'document' => '10002007',
                'password' => 'cienciasbasicasinfrastock'
            ],
            [
                'email' => 'psicola@infrastock.com',
                'role_slug' => 'infrastock.psicola',
                'name' => 'Psicola Infastock',
                'first_name' => 'Psicola',
                'first_last_name' => 'Infastock',
                'document' => '10002008',
                'password' => 'psicolainfrastock'
            ],
        ];

        try {
            DB::beginTransaction();

            foreach ($usersData as $userData) {
                // Buscar el rol
                $role = Role::where('slug', $userData['role_slug'])->first();

                if (!$role) {
                    $this->command->error("El rol con slug \"{$userData['role_slug']}\" no existe. Saltando usuario {$userData['email']}.");
                    continue;
                }

                // Verificar si el usuario ya existe
                $user = User::where('email', $userData['email'])->first();

                if ($user) {
                    // Actualizar contraseña si ya existe (opcional, pero útil si se cambió el patrón)
                    $user->update([
                        'password' => Hash::make($userData['password'])
                    ]);
                    $user->roles()->syncWithoutDetaching([$role->id]);
                    $this->command->info("Usuario {$userData['email']} ya existe. Contraseña actualizada y rol verificado.");
                    continue;
                }

                // Verificar si la persona ya existe por documento
                $person = Person::where('document_number', $userData['document'])->first();

                if (!$person) {
                    // Crear la persona
                    $person = Person::create([
                        'first_name' => $userData['first_name'],
                        'first_last_name' => $userData['first_last_name'],
                        'document_type' => 'Cédula de ciudadanía',
                        'document_number' => $userData['document'],
                        'telephone1' => '3000000000',
                        'address' => 'Dirección de prueba INFRASTOCK',
                        'eps_id' => 1,
                        'population_group_id' => 1,
                        'pension_entity_id' => 1,
                        'date_of_issue' => now()->format('Y-m-d'),
                        'date_of_birth' => '1990-01-01',
                        'gender' => 'Masculino',
                        'marital_status' => 'Soltero(a)',
                        'blood_type' => 'O+',
                        'socioeconomical_status' => 'No registra',
                    ]);
                }

                // Crear el usuario
                $user = User::create([
                    'person_id' => $person->id,
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'nickname' => $userData['name'],
                    'email_verified_at' => now(),
                ]);

                // Asignar rol
                $user->roles()->attach($role->id);

                $this->command->info("Usuario {$userData['email']} creado exitosamente con el rol {$role->name} y contraseña {$userData['password']}.");
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error al ejecutar el seeder de usuarios: ' . $e->getMessage());
        }
    }
}
