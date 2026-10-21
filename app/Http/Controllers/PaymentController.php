<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('payments.index', [
            'payments' => Payment::query()->with('lease.property')->latest('payment_date')->paginate(12),
            'collectedAmount' => Payment::sum('amount'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lease_id' => ['required', 'exists:leases,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:especes,virement,mobile_money'],
        ]);

        $payment = Payment::create([
            ...$validated,
            'receipt_number' => 'Q-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
        ]);

        return to_route('payments.show', $payment)->with('success', 'Le paiement a été enregistré.');
    }

    public function show(Payment $payment): View
    {
        return view('payments.show', ['payment' => $payment->load('lease.property')]);
    }
}
