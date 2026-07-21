<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Публічний список — тільки доступні заявки (status = new, worker_id = null).
     */
    public function index()
    {
        $orders = Order::where('status', 'new')->whereNull('worker_id')->with('tags', 'client')->paginate(10);
        return Inertia::render('Orders/Index', ['orders' => $orders]);
    }

    /**
     * Список заявок для виконавця — тільки доступні.
     */
    public function availableOrders()
    {
        $orders = Order::where('status', 'new')->whereNull('worker_id')->with('tags', 'client')->paginate(10);
        return Inertia::render('Orders/Available', ['orders' => $orders]);
    }

    /**
     * Мої заявки — тільки заявки поточного клієнта.
     */
    public function myOrders()
    {
        $orders = Order::where('client_id', auth()->id())->with('tags')->paginate(10);
        return Inertia::render('Orders/MyOrders', ['orders' => $orders]);
    }

    /**
     * Всі заявки для адміна (повний список, без фільтрів).
     */
    public function adminIndex()
    {
        $orders = Order::with('tags', 'client', 'worker')->paginate(10);
        return Inertia::render('Admin/Orders/Index', ['orders' => $orders]);
    }

    /**
     * Взяти заявку в роботу (тільки для виконавця).
     */
    public function takeOrder(Order $order)
    {
        // Перевіряємо, що заявка доступна
        if ($order->status !== 'new' || $order->worker_id !== null) {
            return redirect()->back()->with('error', 'Ця заявка вже не доступна.');
        }

        $order->update([
            'worker_id' => auth()->id(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('worker.orders.index')->with('success', 'Заявку взято в роботу.');
    }

    /**
     * Форма створення заявки.
     */
    public function create()
    {
        $tags = Tag::all();
        return Inertia::render('Orders/Create', ['tags' => $tags]);
    }

    /**
     * Збереження заявки.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'deadline' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $order = Order::create([
            'client_id' => auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'deadline' => $data['deadline'],
            'status' => 'new',
        ]);

        if (!empty($data['tags'])) {
            $order->tags()->attach($data['tags']);
        }

        return redirect()->route('client.orders.my')->with('success', 'Заявка створена');
    }

    /**
     * Перегляд однієї заявки.
     */
    public function show(Order $order)
    {
        $order->load('tags', 'client', 'worker');
        return Inertia::render('Orders/Show', ['order' => $order]);
    }

    /**
     * Форма редагування заявки.
     */
    public function edit(Order $order)
    {
        $this->authorize('update', $order);
        $tags = Tag::all();
        return Inertia::render('Orders/Edit', [
            'order' => $order->load('tags'),
            'tags' => $tags,
        ]);
    }

    /**
     * Оновлення заявки.
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'deadline' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $order->update($data);

        if ($request->has('tags')) {
            $order->tags()->sync($data['tags']);
        } else {
            $order->tags()->detach();
        }

        return redirect()->route('client.orders.my')->with('success', 'Заявка оновлена');
    }

    /**
     * Видалення заявки.
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $order->delete();
        return redirect()->route('client.orders.my')->with('success', 'Заявка видалена');
    }
}
