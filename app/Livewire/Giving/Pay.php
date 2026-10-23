<?php

namespace App\Livewire\Giving;

use App\Models\Fund;
use App\Models\Pledge;
use Livewire\Component;

class Pay extends Component
{
    public float $amount = 100.00;
    public string $payment_type = 'offering'; // offering, tithe, donation, pledge, special_project
    public ?int $fund_id = null;
    public ?int $pledge_id = null;
    public string $email = '';
    public string $donor_name = '';
    public string $phone = '';
    public string $notes = '';
    public bool $is_anonymous = false;

    public function mount(?int $pledge_id = null): void
    {
        $user = auth()->user();
        if ($user) {
            $this->email = $user->email;
            $this->donor_name = $user->name;
            if ($user->person) {
                $this->phone = $user->person->phone ?? '';
            }
        }

        // Set default fund
        $firstFund = Fund::where('is_active', true)->first();
        if ($firstFund) {
            $this->fund_id = $firstFund->id;
        }

        if ($pledge_id) {
            $this->pledge_id = $pledge_id;
            $this->payment_type = 'pledge';
            $pledge = Pledge::find($pledge_id);
            if ($pledge) {
                $this->fund_id = $pledge->fund_id;
                $this->amount = $pledge->remaining_amount > 0 ? $pledge->remaining_amount : (float) $pledge->amount;
            }
        }
    }

    public function setQuickAmount(float $value): void
    {
        $this->amount = $value;
    }

    public function updatedPaymentType(): void
    {
        if ($this->payment_type !== 'pledge') {
            $this->pledge_id = null;
        }
    }

    public function updatedPledgeId(): void
    {
        if ($this->pledge_id) {
            $pledge = Pledge::find($this->pledge_id);
            if ($pledge) {
                $this->fund_id = $pledge->fund_id;
                $this->amount = $pledge->remaining_amount > 0 ? $pledge->remaining_amount : (float) $pledge->amount;
            }
        }
    }

    public function render()
    {
        $funds = Fund::where('is_active', true)->orderBy('name')->get();
        $user = auth()->user();
        $userPledges = collect();

        if ($user && $user->person_id) {
            $userPledges = Pledge::with('fund')
                ->where('person_id', $user->person_id)
                ->latest()
                ->get();
        }

        return view('livewire.giving.pay', [
            'funds' => $funds,
            'userPledges' => $userPledges,
            'currency' => config('paystack.currency', 'GHS'),
        ])->layout('layouts.app', ['header' => 'Online Giving & Tithe']);
    }
}
