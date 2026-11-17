<?php

namespace App\Notifications;

use App\Models\RentInstallment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentPaymentReminder extends Notification
{
    public function __construct(public RentInstallment $installment, public float $outstandingAmount) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $period = $this->installment->period_start->locale('fr')->translatedFormat('F Y');
        $isOverdue = $this->installment->due_date->lt(today());

        return (new MailMessage)
            ->subject($isOverdue ? 'Loyer en retard — '.$period : 'Rappel de loyer — '.$period)
            ->greeting('Bonjour '.$this->installment->lease->tenant->name.',')
            ->line('Nous vous rappelons le loyer de '.$period.' pour le logement '.$this->installment->lease->property->title.'.')
            ->line('Montant restant à régler : '.number_format($this->outstandingAmount, 0, ',', ' ').' FCFA.')
            ->line('Date d’échéance : '.$this->installment->due_date->format('d/m/Y').'.')
            ->salutation('Cordialement, '.config('app.name'));
    }
}
