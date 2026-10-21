<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeaseController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'tenant_name' => ['required', 'string', 'max:150'],
            'tenant_email' => ['required', 'email', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'rent_amount' => ['required', 'numeric', 'min:1'],
        ]);

        $property = Property::findOrFail($validated['property_id']);
        abort_unless($property->status === 'disponible', 422, 'Ce logement est déjà loué.');

        $property->leases()->create($validated);
        $property->update(['status' => 'loue']);

        return to_route('properties.show', $property)->with('success', 'Le bail a été créé. Vous pouvez maintenant enregistrer les loyers.');
    }
}
