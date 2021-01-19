<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Concert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConcertFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Concert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => 'Example Band',
            'subtitle' => 'with fake openers',
            'additional_information' => 'For Tickets call 9527697962',
            'date' => Carbon::parse('+2 weeks'),
            'venue' => 'The Example Theater',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17324',
            'ticket_price' => 3250,
            'ticket_quantity' => 1
        ];
    }

    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => Carbon::parse('-1 week')
            ];
        });
    }

    public function unpublished()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => null
            ];
        });
    }
}
