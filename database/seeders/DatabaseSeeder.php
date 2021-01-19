<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Concert;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create([
            'email' => 'grant@test.com',
            'password' => bcrypt('grant')
        ]);

        $concert = Concert::factory()->published()->for($user)->create();

        $oldOrder = Order::factory()->hasTickets(1, ['concert_id' => $concert->id])->create(['created_at' => Carbon::parse('11 days ago')]);

        $recentOrders = Order::factory()
            ->count(10)
            ->hasTickets(1, ['concert_id' => $concert->id])
            ->state(new Sequence(
                ['created_at' => Carbon::parse('1 days ago')],
                ['created_at' => Carbon::parse('2 days ago')],
                ['created_at' => Carbon::parse('3 days ago')],
                ['created_at' => Carbon::parse('4 days ago')],
                ['created_at' => Carbon::parse('5 days ago')],
                ['created_at' => Carbon::parse('6 days ago')],
                ['created_at' => Carbon::parse('7 days ago')],
                ['created_at' => Carbon::parse('8 days ago')],
                ['created_at' => Carbon::parse('9 days ago')],
                ['created_at' => Carbon::parse('10 days ago')],
            ))
            ->create();
    }
}
