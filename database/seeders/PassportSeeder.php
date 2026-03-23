<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class PassportSeeder extends Seeder
{
    public function run(): void
    {
        $clientRepository = app(ClientRepository::class);

        $clientRepository->createPersonalAccessGrantClient(
            'Personal Access Client',
            'users',
        );
    }
}
