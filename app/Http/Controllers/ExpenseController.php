<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $toRules = ['nullable', 'date_format:Y-m-d'];

        if ($request->filled('from')) {
            $toRules[] = 'after_or_equal:from';
        }

        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => $toRules,
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
        ]);

        $query = Expense::query()
            ->when($filters['from'] ?? null, fn (Builder $query, string $from): Builder => $query->whereDate('expense_date', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to): Builder => $query->whereDate('expense_date', '<=', $to))
            ->when($filters['property_id'] ?? null, fn (Builder $query, int $propertyId): Builder => $query->where('property_id', $propertyId));

        return view('expenses.index', [
            'expenses' => (clone $query)->with('property')->latest('expense_date')->orderByDesc('id')->paginate(15)->withQueryString(),
            'totalAmount' => (clone $query)->sum('amount'),
            'properties' => Property::query()->orderBy('title')->get(['id', 'title']),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'vendor' => ['nullable', 'string', 'max:150'],
            'lease_id' => ['nullable', 'integer', 'exists:leases,id'],
        ]);

        if (! empty($validated['lease_id'])
            && ! Property::query()->whereKey($validated['property_id'])->whereHas('leases', fn (Builder $query): Builder => $query->whereKey($validated['lease_id']))->exists()) {
            throw ValidationException::withMessages([
                'lease_id' => 'Le bail sélectionné ne correspond pas au logement choisi.',
            ]);
        }

        Expense::query()->create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return to_route('expenses.index')->with('success', 'La dépense a été enregistrée.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return to_route('expenses.index')->with('success', 'La dépense a été supprimée.');
    }
}
