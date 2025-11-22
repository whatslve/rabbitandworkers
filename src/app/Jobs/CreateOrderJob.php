<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class CreateOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels, Queueable;

    public int $orderId;
    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::find($this->orderId);
        if(!$order){
            return;
        }
        $order->status = 'done';
        $order->paid_at = now();
        $order->save();
        $queueName = 'notifications';
        $connection = Queue::connection('rabbitmq');
        try {
            // Проверяем / создаём очередь
            $connection->getChannel()->queue_declare(
                $queueName,
                false,  // passive: false → создаём если нет
                true,   // durable: очередь сохраняется при перезапуске брокера
                false,  // exclusive
                false   // auto_delete
            );
        } catch (\PhpAmqpLib\Exception\AMQPProtocolChannelException $e) {
            Log::error($e);
            return;
        }
        NotifyOrderPaidJob::dispatch($order->id)->onQueue('notifications');
    }
}
