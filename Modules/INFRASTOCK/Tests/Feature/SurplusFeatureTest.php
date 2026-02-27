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
use Modules\INFRASTOCK\Entities\Surplus;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\SICA\Entities\Person;
use Carbon\Carbon;

class SurplusFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected $operator;
    protected $equipment;
    protected $requestItem;
    protected $productiveUnitWarehouse;
    protected $supplyRequest;

    protected function setUp(): void
    {
        parent::setUp();
        
        $operatorRole = Role::firstOrCreate(['slug' => 'operario', 'app_id' => 19], ['name' => 'Operario']);

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

        $operatorPerson = Person::create(array_merge($basePersonData, [
            'first_name' => 'Test', 'first_last_name' => 'Operator', 'document_number' => '999999993'
        ]));

        $this->operator = User::create([
            'person_id' => $operatorPerson->id,
            'email' => 'operator_test@example.com',
            'nickname' => 'Test Operator',
            'password' => \Hash::make('password')
        ]);
        $this->operator->roles()->attach($operatorRole->id);

        $category = InfrastockCategory::create([
            'name' => 'General Materials',
            'type' => 'supply'
        ]);

        $this->equipment = Equipment::create([
            'name' => 'Test Material',
            'infrastock_category_id' => $category->id,
            'price' => 500,
            'amount' => 100,
            'measurement_unit' => 'Unidad',
            'labor_id' => 1,
            'inventory_id' => 1
        ]);

        $this->productiveUnitWarehouse = ProductiveUnitWarehouse::first();

        // Create an approved SupplyRequest
        $this->supplyRequest = InfrastockRequest::create([
            'user_id' => $this->operator->id,
            'productive_unit_warehouse_id' => $this->productiveUnitWarehouse->id,
            'description' => 'Need materials for work',
            'status' => 'approved'
        ]);

        // Create an approved item (requesting 10, approved 10)
        $this->requestItem = RequestItem::create([
            'request_id' => $this->supplyRequest->id,
            'equipment_id' => $this->equipment->id,
            'requested_amount' => 10,
            'approved_amount' => 10,
            'delivered_amount' => 10,
            'status' => 'approved'
        ]);
    }

    public function test_operator_can_create_valid_surplus()
    {
        $response = $this->actingAs($this->operator)->post(route('infrastock.operator.surplus.store'), [
            'request_id' => $this->supplyRequest->id,
            'request_item_id' => $this->requestItem->id,
            'surplus_amount' => 3, // Valid, 3 < 10
            'reason' => 'Finished work early, 3 left over.',
            'surplus_date' => Carbon::now()->format('Y-m-d')
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('surpluses', [
            'equipment_id' => $this->equipment->id,
            'user_id' => $this->operator->id,
            'request_id' => $this->supplyRequest->id,
            'request_item_id' => $this->requestItem->id,
            'surplus_amount' => 3,
            'status' => 'pending'
        ]);
    }

    public function test_operator_cannot_return_more_surplus_than_approved()
    {
        // Try returning 15, when only 10 were approved
        $response = $this->actingAs($this->operator)->post(route('infrastock.operator.surplus.store'), [
            'request_id' => $this->supplyRequest->id,
            'request_item_id' => $this->requestItem->id,
            'surplus_amount' => 15, 
            'reason' => 'Some typo returning more than possible',
            'surplus_date' => Carbon::now()->format('Y-m-d')
        ]);

        // The controller redirects back with an error like: 'La cantidad ingresada (15) supera el saldo pendiente (10).'
        $response->assertSessionHas('error');
        
        $this->assertDatabaseMissing('surpluses', [
            'surplus_amount' => 15,
            'reason' => 'Some typo returning more than possible'
        ]);
    }

    public function test_operator_cannot_create_surplus_without_valid_request()
    {
        $response = $this->actingAs($this->operator)->post(route('infrastock.operator.surplus.store'), [
            'request_id' => 99999, // Invalid
            'request_item_id' => 99999, // Invalid
            'surplus_amount' => 2,
            'reason' => 'No request attached',
            'surplus_date' => Carbon::now()->format('Y-m-d')
        ]);

        $response->assertSessionHasErrors(['request_id', 'request_item_id']);
    }
}
