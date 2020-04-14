<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();

        DB::table('users')->delete();

        $users = array(
            ['name' => 'Thanh', 'email' => 'thanh@lumine', 'password' => Hash::make('thanh')],
            ['name' => 'Quang', 'email' => 'kyoto69@lumine', 'password' => Hash::make('midoban')],
            ['name' => 'Test', 'email' => 'test@lumine', 'password' => Hash::make('test')],
            ['name' => 'Guest', 'email' => 'guest@lumine', 'password' => Hash::make('guest')],
            ['name' => 'Admin1', 'email' => 'admin1@lumine', 'password' => Hash::make('admin')],
            ['name' => 'Admin2', 'email' => 'admin2@lumine', 'password' => Hash::make('admin')],
            ['name' => 'Admin3', 'email' => 'admin3@lumine', 'password' => Hash::make('admin')],
            ['name' => 'Admin4', 'email' => 'admin4@lumine', 'password' => Hash::make('admin')],
            ['name' => 'Admin5', 'email' => 'admin5@lumine', 'password' => Hash::make('admin')],
            ['name' => 'Customer1', 'email' => 'customer1@lumine', 'password' => Hash::make('customer')],
            ['name' => 'Customer2', 'email' => 'customer2@lumine', 'password' => Hash::make('customer')],
            ['name' => 'Customer3', 'email' => 'customer3@lumine', 'password' => Hash::make('customer')],
            ['name' => 'Customer4', 'email' => 'customer4@lumine', 'password' => Hash::make('customer')],
            ['name' => 'Customer5', 'email' => 'customer5@lumine', 'password' => Hash::make('customer')],
        );

        // Loop through each user above and create the record for them in the database
        foreach ($users as $user) {
            User::create($user);
        }

        Model::reguard();
    }

}
