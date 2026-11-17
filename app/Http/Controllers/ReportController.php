<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\RentInstallment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->period($request);
        $installments = $this->installments($from, $to)->get();

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'payments' => Payment::query()
                ->with(['lease.property', 'lease.tenant', 'installment'])
                ->whereDate('payment_date', '>=', $from->toDateString())
                ->whereDate('payment_date', '<=', $to->toDateString())
                ->latest('payment_date')
                ->get(),
            'expenses' => Expense::query()
                ->with('property')
                ->whereDate('expense_date', '>=', $from->toDateString())
                ->whereDate('expense_date', '<=', $to->toDateString())
                ->latest('expense_date')
                ->get(),
            'expectedAmount' => $installments->sum(fn (RentInstallment $installment): float => (float) $installment->amount_due),
            'rentCollectedForInstallments' => $installments->sum(fn (RentInstallment $installment): float => min((float) ($installment->paid_amount ?? 0), (float) $installment->amount_due)),
            'outstandingAmount' => $installments->sum(fn (RentInstallment $installment): float => $installment->outstanding_amount),
            'collectedAmount' => Payment::query()
                ->whereDate('payment_date', '>=', $from->toDateString())
                ->whereDate('payment_date', '<=', $to->toDateString())
                ->sum('amount'),
            'expenseAmount' => Expense::query()
                ->whereDate('expense_date', '>=', $from->toDateString())
                ->whereDate('expense_date', '<=', $to->toDateString())
                ->sum('amount'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->period($request);
        $payments = Payment::query()
            ->with(['lease.property', 'lease.tenant', 'installment'])
            ->whereDate('payment_date', '>=', $from->toDateString())
            ->whereDate('payment_date', '<=', $to->toDateString())
            ->orderBy('payment_date')
            ->get();
        $expenses = Expense::query()
            ->with('property')
            ->whereDate('expense_date', '>=', $from->toDateString())
            ->whereDate('expense_date', '<=', $to->toDateString())
            ->orderBy('expense_date')
            ->get();

        return response()->streamDownload(function () use ($payments, $expenses): void {
            $output = fopen('php://output', 'wb');

            if ($output === false) {
                throw new \RuntimeException('Impossible de créer le fichier CSV.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Type', 'Date', 'Référence', 'Locataire/Fournisseur', 'Logement', 'Période', 'Mode/Catégorie', 'Montant FCFA'], ';', '"', '');

            foreach ($payments as $payment) {
                fputcsv($output, [
                    'Encaissement',
                    $payment->payment_date->format('Y-m-d'),
                    $payment->receipt_number,
                    $payment->lease->tenant?->name ?? $payment->lease->tenant_name,
                    $payment->lease->property->title,
                    $payment->installment?->period_start->format('Y-m') ?? 'Non affectée',
                    $payment->payment_method,
                    $payment->amount,
                ], ';', '"', '');
            }

            foreach ($expenses as $expense) {
                fputcsv($output, [
                    'Dépense',
                    $expense->expense_date->format('Y-m-d'),
                    $expense->id,
                    $expense->vendor,
                    $expense->property->title,
                    '',
                    $expense->category,
                    '-'.$expense->amount,
                ], ';', '"', '');
            }

            fclose($output);
        }, "rapport-{$from->format('Y-m-d')}-{$to->format('Y-m-d')}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{Carbon, Carbon}
     */
    private function period(Request $request): array
    {
        $toRules = ['nullable', 'date_format:Y-m-d'];

        if ($request->filled('from')) {
            $toRules[] = 'after_or_equal:from';
        }

        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => $toRules,
        ]);

        return [
            Carbon::parse($validated['from'] ?? now()->startOfMonth()->toDateString())->startOfDay(),
            Carbon::parse($validated['to'] ?? today()->toDateString())->endOfDay(),
        ];
    }

    private function installments(Carbon $from, Carbon $to): Builder
    {
        return RentInstallment::query()
            ->where('status', 'open')
            ->whereDate('period_start', '>=', $from->copy()->startOfMonth()->toDateString())
            ->whereDate('period_start', '<=', $to->copy()->startOfMonth()->toDateString())
            ->whereDate('due_date', '<=', $to->toDateString())
            ->withSum([
                'payments as paid_amount' => fn (Builder $query): Builder => $query->whereDate('payment_date', '<=', $to->toDateString()),
            ], 'amount');
    }
}
