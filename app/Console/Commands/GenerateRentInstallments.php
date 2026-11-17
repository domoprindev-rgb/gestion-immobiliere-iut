<?php

namespace App\Console\Commands;

use App\Services\RentInstallmentGenerator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rents:generate-installments')]
#[Description('Generate current and upcoming rent installments for active leases')]
class GenerateRentInstallments extends Command
{
    public function handle(RentInstallmentGenerator $generator): int
    {
        $created = $generator->generateForActiveLeases();

        $this->info("{$created} échéance(s) générée(s).");

        return self::SUCCESS;
    }
}
