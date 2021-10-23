<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * @param string $type
     *
     * @return Factory
     */
    public function type(string $type): Factory
    {
        $document = $this->faker->cpf(false);

        if ($type === User::TYPE_SHOPKEEPER) {
            $document = $this->faker->cnpj(false);
        }

        return $this->state(function (array $attributes) use($type, $document) {
            return [
                'type'     => $type,
                'document' => $document,
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
            'full_name' => $this->faker->name,
            'email'     => $this->faker->unique()->safeEmail,
            'password'  => Hash::make($this->faker->password),
        ];
    }
}
