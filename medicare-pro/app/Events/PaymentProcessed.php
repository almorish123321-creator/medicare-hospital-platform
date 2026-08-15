<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PaymentProcessed Event
 *
 * Fired after a payment has been successfully processed.
 * Used by listeners to trigger side-effects such as sending
 * receipt emails, generating PDF invoices, or updating dashboards.
 */
class PaymentProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Payment $payment) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Contracts\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('patient.' . $this->payment->invoice->patient_id),
            new PrivateChannel('hospital.' . $this->payment->invoice->hospital_id),
        ];
    }

    /**
     * The data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => (float) $this->payment->amount,
            'method' => $this->payment->payment_method,
            'invoice_status' => $this->payment->invoice->status,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'payment.processed';
    }
}
