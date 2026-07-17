<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Берём первого клиента (или создаём, если нет)
        $client = User::where('role_id', 3)->first(); // role_id = 3 — клиент

        if (!$client) {
            $client = User::factory()->create([
                'name' => 'Тестовий клієнт',
                'email' => 'client@test.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
            ]);
        }

        // Получаем все теги
        $tags = Tag::all();

        // Создаём 10 заявок
        $orders = Order::factory()->count(10)->create([
            'client_id' => $client->id,
            'status' => 'new',
            'worker_id' => null,
        ]);

        // Привязываем теги к заявкам
        foreach ($orders as $order) {
            $randomTags = $tags->random(rand(1, 3))->pluck('id')->toArray();
            $order->tags()->attach($randomTags);
        }
    }
}
