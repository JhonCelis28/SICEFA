<?php

namespace Modules\INFRASTOCK\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Modules\SICA\Entities\Role;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\SICA\Entities\Person;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LoanFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected $instructor;
    protected $admin;
    protected $tool;
    protected $productiveUnitWarehouse;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        $instructorRole = Role::firstOrCreate(['slug' => 'instructor', 'app_id' => 19], ['name' => 'Instructor']);
        $adminRole = Role::firstOrCreate(['slug' => 'infrastock.admin', 'app_id' => 19], ['name' => 'Administrador']);

        // Create Person records for users
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

        $instructorPerson = Person::create(array_merge($basePersonData, [
            'first_name' => 'Test', 'first_last_name' => 'Instructor', 'document_number' => '999999991'
        ]));
        
        $adminPerson = Person::create(array_merge($basePersonData, [
            'first_name' => 'Test', 'first_last_name' => 'Admin', 'document_number' => '999999992'
        ]));

        // Setup Instructor
        $this->instructor = User::create([
            'person_id' => $instructorPerson->id,
            'email' => 'instructor_test@example.com',
            'nickname' => 'Test Instructor',
            'password' => \Hash::make('password')
        ]);
        $this->instructor->roles()->attach($instructorRole->id);

        // Setup Admin
        $this->admin = User::create([
            'person_id' => $adminPerson->id,
            'email' => 'admin_test@example.com',
            'nickname' => 'Test Admin',
            'password' => \Hash::make('password')
        ]);
        $this->admin->roles()->attach($adminRole->id);

        // Setup Tool
        $this->tool = Tool::create([
            'nombre' => 'Test Tool',
            'placa' => 'TOOL-001',
            'infrastock_category_id' => 1, // Assume category 1 exists or create if needed
            'cantidad_total' => 5,
            'cantidad_disponible' => 5,
            'estado' => 'disponible',
            'precio' => 1000
        ]);

        // Setup Productive Unit Warehouse
        $this->productiveUnitWarehouse = ProductiveUnitWarehouse::first();
        if (!$this->productiveUnitWarehouse) {
            $this->markTestSkipped('No ProductiveUnitWarehouse found in database to map loans.');
        }
    }

    public function test_instructor_can_request_a_loan()
    {
        $response = $this->actingAs($this->instructor)->post(route('infrastock.instructor.store-loan'), [
            '_form_type' => 'create',
            'tools' => [
                [
                    'tool_id' => $this->tool->id,
                    'amount' => 2
                ]
            ],
            'productive_unit_warehouse_id' => $this->productiveUnitWarehouse->id,
            'purpose' => 'For testing purposes',
            'required_date' => Carbon::tomorrow()->format('Y-m-d'),
            'return_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d')
        ]);

        $response->assertRedirect(route('infrastock.instructor.my-loans'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('warehouse_movements', [
            'item_type' => 'tool',
            'user_id' => $this->instructor->id,
            'movement_id' => $this->tool->id,
            'role' => 'Préstamo',
            'status' => 'pending',
            'amount' => 2
        ]);
    }

    public function test_admin_can_approve_loan_and_reduce_stock()
    {
        // Setup an existing pending loan
        $loan = WarehouseMovement::create([
            'productive_unit_warehouse_id' => $this->productiveUnitWarehouse->id,
            'movement_id' => $this->tool->id,
            'item_type' => 'tool',
            'user_id' => $this->instructor->id,
            'role' => 'Préstamo',
            'status' => 'pending',
            'amount' => 2,
            'purpose' => 'Testing validation'
        ]);

        // Approve loan as Admin
        $response = $this->actingAs($this->admin)->post(route('infrastock.admin.loans.approve-loan', $loan->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify status changed
        $this->assertDatabaseHas('warehouse_movements', [
            'id' => $loan->id,
            'status' => 'approved'
        ]);

        // Verify Tool stock was reduced
        $this->assertDatabaseHas('tools', [
            'id' => $this->tool->id,
            'cantidad_disponible' => 3 // 5 - 2
        ]);
    }

    public function test_instructor_can_return_a_loan()
    {
        Storage::fake('public');
        
        // Create an approved loan
        $loan = WarehouseMovement::create([
            'productive_unit_warehouse_id' => $this->productiveUnitWarehouse->id,
            'movement_id' => $this->tool->id,
            'item_type' => 'tool',
            'user_id' => $this->instructor->id,
            'role' => 'Préstamo',
            'status' => 'approved',
            'amount' => 2,
            'purpose' => 'Testing return validation'
        ]);
        
        $image = UploadedFile::fake()->image('tool_return.jpg');

        $response = $this->actingAs($this->instructor)->post(route('infrastock.instructor.return-loan', $loan->id), [
            '_form_type' => 'return',
            '_return_loan_id' => $loan->id,
            'amount' => 2,
            'description' => 'Returned in good condition',
            'return_image' => $image
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check the database for the new return record and ensure the ID mapping description exists
        $this->assertDatabaseHas('warehouse_movements', [
            'movement_id' => $this->tool->id,
            'item_type' => 'tool',
            'user_id' => $this->instructor->id,
            'role' => 'Devolución',
            'status' => 'pending',
            'amount' => 2,
            'description' => 'Returned in good condition | Devolución de préstamo #' . $loan->id
        ]);
    }

    public function test_instructor_cannot_return_more_than_borrowed()
    {
        Storage::fake('public');
        
        $loan = WarehouseMovement::create([
            'productive_unit_warehouse_id' => $this->productiveUnitWarehouse->id,
            'movement_id' => $this->tool->id,
            'item_type' => 'tool',
            'user_id' => $this->instructor->id,
            'role' => 'Préstamo',
            'status' => 'approved',
            'amount' => 2,
            'purpose' => 'Testing return validation over limit'
        ]);
        
        $image = UploadedFile::fake()->image('tool_return.jpg');

        $response = $this->actingAs($this->instructor)->post(route('infrastock.instructor.return-loan', $loan->id), [
            '_form_type' => 'return',
            '_return_loan_id' => $loan->id,
            'amount' => 5, // Trying to return 5 but only borrowed 2
            'description' => 'Returned in good condition',
            'return_image' => $image
        ]);

        // Expect successful redirect (controller ignores user's amount and forces loan amount)
        $response->assertSessionHas('success');
        
        // Assert return movement was created with max 2
        $this->assertDatabaseHas('warehouse_movements', [
            'item_type' => 'tool',
            'user_id' => $this->instructor->id,
            'role' => 'Devolución',
            'amount' => 2
        ]);
    }
}
