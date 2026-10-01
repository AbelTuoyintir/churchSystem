<?php

use App\Livewire\Contributions\Form as ContributionForm;
use App\Livewire\Funds\Form as FundForm;
use App\Livewire\Funds\Index as FundsIndex;
use App\Livewire\Pledges\Form as PledgeForm;
use App\Models\Contribution;
use App\Models\Fund;
use App\Models\Person;
use App\Models\Pledge;
use App\Models\User;
use Livewire\Livewire;

test('leader and viewer receive 403 on funds and pledges routes', function () {
    $fund = Fund::create(['name' => 'Building Fund']);
    $person = Person::factory()->create();
    $pledge = Pledge::create([
        'person_id' => $person->id,
        'fund_id' => $fund->id,
        'amount' => 500,
        'frequency' => 'monthly',
        'start_date' => now()->toDateString(),
    ]);

    foreach (['leader', 'viewer'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->get('/funds')->assertForbidden();
        $this->actingAs($user)->get('/funds/create')->assertForbidden();
        $this->actingAs($user)->get("/funds/{$fund->id}/edit")->assertForbidden();

        $this->actingAs($user)->get('/pledges')->assertForbidden();
        $this->actingAs($user)->get('/pledges/create')->assertForbidden();
        $this->actingAs($user)->get("/pledges/{$pledge->id}/edit")->assertForbidden();
    }
});

test('admin can create and edit a fund and view total received', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create fund
    Livewire::actingAs($admin)
        ->test(FundForm::class)
        ->set('name', 'Missions Fund')
        ->set('description', 'Supporting local and foreign missions')
        ->set('goal_amount', '10000.00')
        ->set('is_tax_deductible', true)
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect(route('funds.index'));

    $fund = Fund::where('name', 'Missions Fund')->first();
    expect($fund)->not->toBeNull();
    expect((float) $fund->goal_amount)->toBe(10000.00);

    // Add contribution to fund
    Contribution::create([
        'fund_id' => $fund->id,
        'amount' => 2500.00,
        'contributed_on' => now()->toDateString(),
        'method' => 'online',
    ]);

    // View index
    Livewire::actingAs($admin)
        ->test(FundsIndex::class)
        ->assertSee('Missions Fund')
        ->assertSee('$2,500.00');
});

test('recording contribution with is_anonymous clears person_id', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create();
    $fund = Fund::create(['name' => 'Youth Fund']);

    // Record anonymous contribution
    Livewire::actingAs($admin)
        ->test(ContributionForm::class)
        ->set('person_id', $person->id)
        ->set('fund_id', $fund->id)
        ->set('amount', '150.00')
        ->set('contributed_on', '2026-03-15')
        ->set('method', 'cash')
        ->set('is_anonymous', true)
        ->call('save')
        ->assertRedirect(route('contributions.index'));

    $contribution = Contribution::where('fund_id', $fund->id)->first();
    expect($contribution)->not->toBeNull();
    expect($contribution->person_id)->toBeNull();
    expect($contribution->is_anonymous)->toBeTrue();
});

test('admin can create and update pledge', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create();
    $fund = Fund::create(['name' => 'General Fund']);

    Livewire::actingAs($admin)
        ->test(PledgeForm::class)
        ->set('person_id', $person->id)
        ->set('fund_id', $fund->id)
        ->set('amount', '200.00')
        ->set('frequency', 'monthly')
        ->set('start_date', '2026-01-01')
        ->set('end_date', '2026-12-31')
        ->call('save')
        ->assertRedirect(route('pledges.index'));

    $pledge = Pledge::where('person_id', $person->id)->first();
    expect($pledge)->not->toBeNull();
    expect((float) $pledge->amount)->toBe(200.00);
    expect($pledge->frequency)->toBe('monthly');
});
