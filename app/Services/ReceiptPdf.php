<?php

namespace App\Services;

use App\Models\Payment;

class ReceiptPdf
{
    public function render(Payment $payment): string
    {
        $tenantName = $payment->lease->tenant?->name ?? $payment->lease->tenant_name;
        $propertyTitle = $payment->lease->property->title;
        $period = $payment->installment?->period_start->locale('fr')->translatedFormat('F Y') ?? 'Période non affectée';
        $method = ucfirst(str_replace('_', ' ', $payment->payment_method));
        $isFullyPaid = $payment->installment !== null
            && (float) $payment->installment->payments()->sum('amount') >= (float) $payment->installment->amount_due;

        $lines = [
            'IMMOPILOT - GESTION LOCATIVE',
            $isFullyPaid ? 'QUITTANCE DE LOYER' : 'RECU DE PAIEMENT',
            '',
            'Reference : '.$payment->receipt_number,
            'Date du paiement : '.$payment->payment_date->format('d/m/Y'),
            'Periode concernee : '.$period,
            '',
            'Recu de : '.$tenantName,
            'Logement : '.$propertyTitle,
            'Adresse : '.$payment->lease->property->address,
            '',
            'Montant recu : '.number_format((float) $payment->amount, 0, ',', ' ').' FCFA',
            'Mode de paiement : '.$method,
            '',
            $isFullyPaid
                ? 'Cette quittance atteste le paiement integral du loyer de la periode.'
                : 'Ce document atteste uniquement le montant effectivement recu.',
        ];

        $content = "0.06 0.31 0.24 rg 0 690 595 152 re f\n";
        $content .= "1 1 1 rg\n";
        $y = 790;

        foreach ($lines as $index => $line) {
            $fontSize = $index < 2 ? 17 : 11;
            $safeLine = $this->escape($line);
            $content .= "BT /F1 {$fontSize} Tf 52 {$y} Td ({$safeLine}) Tj ET\n";
            $y -= $index === 1 ? 46 : 29;
        }

        $content .= "0.06 0.31 0.24 rg 52 70 491 1 re f\n";
        $content .= "0.25 0.29 0.34 rg BT /F1 9 Tf 52 52 Td (Document genere par ImmoPilot) Tj ET\n";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            '<< /Length '.strlen($content)." >>\nstream\n".$content.'endstream',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".count($offsets)."\n0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size ".count($offsets)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function escape(string $value): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        if ($ascii === false) {
            throw new \RuntimeException('Impossible de convertir le texte de la quittance.');
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $ascii);
    }
}
