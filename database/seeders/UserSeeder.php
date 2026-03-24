<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public const string USER_ID = '0196f4a0-6f40-7000-8000-000000000001';
    public const string USER_EMAIL = 'user@example.com';

    public function run(): void
    {
        $this->create(self::USER_ID, self::USER_EMAIL);
    }

    private function create(?string $id, string $email): void
    {
        User::factory()->create(array_filter([
            'id' => $id,
            'email' => $email,
        ]));
    }
}
