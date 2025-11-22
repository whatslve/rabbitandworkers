<?php

namespace App\Http\Controllers;

use App\Jobs\CreateOrderJob;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'order_name' => 'string|nullable',
            'status' => 'string|nullable',
        ]);
        $order = Order::create($data);
        $queueName = 'billing';
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
            // Ловим ошибки, если что-то не так с брокером
            return response()->json(['error' => 'Не удалось создать очередь: ' . $e->getMessage()], 500);
        }
        CreateOrderJob::dispatch($order->id)->onQueue('billing');
        return response()->json($order, 201);
    }

    public function index() {
        $orders = Order::all();
        return response()->json($orders, 200);
    }
}
