<?php

namespace Modules\INFRASTOCK\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Modules\SICA\Entities\Role;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Request as InfrastockRequest;
use Modules\INFRASTOCK\Entities\RequestItem;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\SICA\Entities\Person;

class SupplyRequestFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected $cleaningStaff;
    protected $admin;
    protected $equipment;
    protected $productiveUnitWarehouse;

    protected function setUp(): void
    {
        parent::setUp();
        
        $cleaningRole = Role::firstOrCreate(['slug' => 'aseo', 'app_id' => 19], ['name' => 'Personal de Aseo']);
        $adminRole = Role::firstOrCreate(['slug' => 'infrastock.admin', 'app_id' => 19], ['name' => 'Administrador']);

        $basePersonData = [
            'document_type' => 'Cédula de ciudadanía',
            'telephone1' => '3000000000',
            'eps_id' => 1,
            'population_group_id' => 1,
            'pension_entity_id' => 1,
            'date_of_issue' => now()->format('Y-m-d'),
            'date_of_birth' => '1990-01-01',
            'gender' => 'Masculino',
            'marital_status' => 'Soltero(a)',
            'blood_type' => 'O+',
            'socioeconomical_status' => 'No registra',
        ];

        $cleaningPerson = Person::create(array_merge($basePersonData, [
            'first_name' => 'Test', 'first_last_name' => 'Cleaner', 'document_number' => '999999995'
        ]));

        $this->cleaningStaff = User::create([
            'person_id' => $cleaningPerson->id,
            'email' => 'cleaner_test@example.com',
            'nickname' => 'Test Cleaner',
            'password' => \Hash::make('password')
        ]);
        $this->cleaningStaff->roles()->attach($cleaningRole->id);

        $adminPerson = Person::create(array_merge($basePersonData, [
            'first_name' => 'Test', 'first_last_name' => 'Admin2', 'document_number' => '999999996'
        ]));

        $this->admin = User::create([
            'person_id' => $adminPerson->id,
            'email' => 'admin_test2@example.com',
            'nickname' => 'Test Admin 2',
            'password' => \Hash::make('password')
        ]);
        $this->admin->roles()->attach($adminRole->id);

        $category = InfrastockCategory::create([
            'name' => 'Limpieza General',
            'type' => 'supply'
        ]);

        $this->equipment = Equipment::create([
            'name' => 'Detergente Test',
            'infrastock_category_id' => $category->id,
            'price' => 5000,
            'amount' => 50,
            'measurement_unit' => 'Litros',
            'labor_id' => 1,
            'inventory_id' => 1
        ]);

        $this->productiveUnitWarehouse = ProductiveUnitWarehouse::first();
    }

    public function test_cleaning_staff_can_request_supplies()
    {
        $response = $this->actingAs($this->cleaningStaff)->post(route('infrastock.cleaning-staff.requests.store'), [
            'productive_unit_warehouse_id' => $this->productiveUnitWarehouse->id,
            'description' => 'Cleaning supplies for this week',
            'equipments' => [
                $this->equipment->id => [
                    'amount' => 5
                ]
            ]
        ]);

        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('requests', [
            'user_id' => $this->cleaningStaff->id,
            'description' => 'Cleaning supplies for this week',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('request_items', [
            'equipment_id' => $this->equipment->id,
            'requested_amount' => 5,
            'status' => 'pending'
        ]);
    }

    public function test_admin_can_approve_supply_request_and_create_warehouse_movement()
    {
        // 1. Setup pending request
        $supplyRequest = InfrastockRequest::create([
            'user_id' => $this->cleaningStaff->id,
            'productive_unit_warehouse_id' => $this->productiveUnitWarehouse->id,
            'description' => 'Need soap',
            'status' => 'pending'
        ]);

        $requestItem = RequestItem::create([
            'request_id' => $supplyRequest->id,
            'equipment_id' => $this->equipment->id,
            'requested_amount' => 5,
            'status' => 'pending'
        ]);

        // 2. Admin approves request (auto approves full amount)
        $response = $this->actingAs($this->admin)->post(route('infrastock.admin.requests.approve', $supplyRequest->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 3. Verify request is approved and items updated
        $this->assertDatabaseHas('requests', [
            'id' => $supplyRequest->id,
            'status' => 'approved'
        ]);

        $this->assertDatabaseHas('request_items', [
            'id' => $requestItem->id,
            'approved_amount' => 5,
            'delivered_amount' => 5,
            'status' => 'approved'
        ]);

        // 4. Verify warehouse movement was created to deduct stock
        $this->assertDatabaseHas('warehouse_movements', [
            'item_type' => 'equipment',
            'role' => 'Entrega',
            'amount' => 5
        ]);
    }
}
