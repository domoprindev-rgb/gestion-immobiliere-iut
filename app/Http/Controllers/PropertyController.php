<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(): View
    {
        return view('properties.index', [
            'properties' => Property::query()->latest()->paginate(10),
        ]);
    }

    public function dashboard(): View
    {
        return view('welcome', [
            'propertyCount' => Property::count(),
            'occupiedCount' => Property::where('status', 'loue')->count(),
            'paymentCount' => Payment::count(),
            'collectedAmount' => Payment::sum('amount'),
            'recentPayments' => Payment::query()->with('lease.property')->latest('payment_date')->limit(6)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'string', 'max:80'],
        ]);

        Property::create($validated);

        return to_route('properties.index')->with('success', 'Le logement a été ajouté.');
    }

    public function show(Property $property): View
    {
        return view('properties.show', [
            'property' => $property->load(['leases' => fn ($query) => $query->latest(), 'leases.payments' => fn ($query) => $query->latest('payment_date')]),
        ]);
    }
}
