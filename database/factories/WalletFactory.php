<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Wallet;

class WalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wallet::class;

    /**
     * @param int $userId
     *
     * @return Factory
     */
    public function userId(int $userId): Factory
    {
        return $this->state(function (array $attributes) use($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }

    /**
     * @param float $balance
     *
     * @return Factory
     */
    public function balance(string $balance): Factory
    {
        return $this->state(function (array $attributes) use($balance) {
            return [
                'balance' => $balance,
            ];
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'balance' => $this->faker->randomFloat(2, 0.01, 999.99),
        ];
    }
}
