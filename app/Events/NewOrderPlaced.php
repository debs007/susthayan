<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Powers the franchise dashboard's live order queue - appears the instant payment confirms, no refresh. */
class NewOrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("franchise.{$this->order->franchise_id}")];
    }

    public function broadcastAs(): string
    {
        return 'order.new';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'total_amount' => (string) $this->order->total_amount,
            'fulfillment_type' => $this->order->fulfillment_type->value,
        ];
    }
}
