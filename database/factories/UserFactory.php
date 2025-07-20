<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => '+7999' . fake()->randomNumber(7, true),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    // Мутатор для форматирования телефона
    public function phone(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                if (empty($value)) {
                    return null;
                }

                return preg_replace(
                    '/^\+7(\d{3})(\d{3})(\d{2})(\d{2})$/',
                    '+7 ($1) $2-$3-$4',
                    $value
                );
            },

            set: function ($value) {
                $cleaned = preg_replace('/[^0-9+]/', '', $value);

                if (strlen($cleaned) === 11 && str_starts_with($cleaned, '8')) {
                    return '+7' . substr($cleaned, 1);
                }

                if (strlen($cleaned) === 11 && str_starts_with($cleaned, '7')) {
                    return '+' . $cleaned;
                }

                return $cleaned;
            }
        );
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
