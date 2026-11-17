<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\RentInstallment;
use App\Services\ReceiptPdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $toRules = ['nullable', 'date_format:Y-m-d'];

        if ($request->filled('from')) {
            $toRules[] = 'after_or_equal:from';
        }

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'in:especes,virement,mobile_money'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => $toRules,
        ]);

        $payments = Payment::query()
            ->with(['lease.property', 'lease.tenant', 'installment'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('receipt_number', 'like', "%{$search}%")
                        ->orWhereHas('lease', function (Builder $leaseQuery) use ($search): void {
                            $leaseQuery->where('tenant_name', 'like', "%{$search}%")
                                ->orWhereHas('property', fn (Builder $propertyQuery): Builder => $propertyQuery->where('title', 'like', "%{$search}%"));
                        });
                });
            })
            ->when($filters['payment_method'] ?? null, fn (Builder $query, string $method): Builder => $query->where('payment_method', $method))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from): Builder => $query->whereDate('payment_date', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to): Builder => $query->whereDate('payment_date', '<=', $to))
            ->latest('payment_date')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('payments.index', [
            'payments' => $payments,
            'collectedAmount' => Payment::sum('amount'),
            'collectedThisMonth' => Payment::query()
                ->whereDate('payment_date', '>=', now()->startOfMonth()->toDateString())
                ->whereDate('payment_date', '<=', now()->toDateString())
                ->sum('amount'),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rent_installment_id' => ['required', Rule::exists('rent_installments', 'id')->where('status', 'open')],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', 'in:especes,virement,mobile_money'],
        ], [
            'rent_installment_id.exists' => 'Cette échéance est annulée ou introuvable.',
        ]);

        $payment = DB::transaction(function () use ($validated): Payment {
            $installment = RentInstallment::query()->lockForUpdate()->findOrFail($validated['rent_installment_id']);
            $lease = Lease::query()->lockForUpdate()->findOrFail($installment->lease_id);

            $outstandingAmount = max(
                (float) $installment->amount_due - (float) $installment->payments()->sum('amount'),
                0,
            );

            if ($lease->status !== 'actif' || $installment->status !== 'open' || $outstandingAmount <= 0) {
                throw ValidationException::withMessages([
                    'rent_installment_id' => 'Cette échéance n’accepte plus de paiement.',
                ]);
            }

            if ((float) $validated['amount'] > $outstandingAmount) {
                throw ValidationException::withMessages([
                    'amount' => 'Le montant dépasse le solde de cette échéance ('.number_format($outstandingAmount, 0, ',', ' ').' FCFA).',
                ]);
            }

            return $lease->payments()->create([
                'rent_installment_id' => $installment->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'receipt_number' => 'Q-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            ]);
        });

        return to_route('payments.show', $payment)->with('success', 'Le paiement a été enregistré et sa quittance est disponible.');
    }

    public function show(Payment $payment): View
    {
        return view('payments.show', [
            'payment' => $payment->load([
                'lease.property',
                'lease.tenant',
                'installment' => fn ($query) => $query->withSum('payments as paid_amount', 'amount'),
            ]),
        ]);
    }

    public function downloadReceipt(Payment $payment, ReceiptPdf $receiptPdf): StreamedResponse
    {
        $payment->load(['lease.property', 'lease.tenant', 'installment']);
        $content = $receiptPdf->render($payment);

        return response()->streamDownload(
            static function () use ($content): void {
                echo $content;
            },
            'quittance-'.$payment->receipt_number.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }
}
