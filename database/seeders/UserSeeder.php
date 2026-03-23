<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->create('user');
    }

    private function create(string $name): void
    {
        User::factory()->create([
            'email' => $name . '@example.com',
        ]);
    }
}
