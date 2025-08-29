<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Position;

class UserTokenSeeder extends Seeder
{
    public function run(): void
    {
        $posMgr = Position::firstOrCreate(['name' => 'Manager']);
        $posEng = Position::firstOrCreate(['name' => 'Engineer']);

        $userMgr = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
                'position_id' => $posMgr->id,
            ]
        );

        $userEng = User::updateOrCreate(
            ['email' => 'engineer@example.com'],
            [
                'name' => 'Engineer User',
                'password' => bcrypt('password'),
                'position_id' => $posEng->id,
            ]
        );

        $tokenMgr = $userMgr->createToken('manager')->plainTextToken;
        $tokenEng = $userEng->createToken('engineer')->plainTextToken;

        $this->command->info('Manager TOKEN: ' . $tokenMgr);
        $this->command->info('Engineer TOKEN: ' . $tokenEng);
    }
}
