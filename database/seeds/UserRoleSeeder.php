<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Role;
use App\Permission;

class UserRoleSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        DB::table('roles')->delete();
        DB::table('permissions')->delete();
        DB::table('permission_role')->delete();
        DB::table('role_user')->delete();

        // Create role
        $owner = new Role();
        $owner->name = 'owner';
        $owner->display_name = 'Project Owner'; // optional
        $owner->description = 'User is the owner of a given project'; // optional
        $owner->save();

        $admin = new Role();
        $admin->name = 'admin';
        $admin->display_name = 'Administrator'; // optional
        $admin->description = 'User is allowed to manage and edit other users'; // optional
        $admin->save();

        $customer = new Role();
        $customer->name = 'customer';
        $customer->display_name = 'Common Customer'; // optional
        $customer->description = 'Nothing special about this role'; // optional
        $customer->save();

        // Create permission
        $tickerApi = new Permission();
        $tickerApi->name = 'ticker-api';
        $tickerApi->display_name = 'Ticker API'; // optional
        $tickerApi->description = 'using ticker related api'; // optional
        $tickerApi->save();

        $tickerChartApi = new Permission();
        $tickerChartApi->name = 'ticker-chart-api';
        $tickerChartApi->display_name = 'Ticker Chart API'; // optional
        $tickerChartApi->description = 'using ticker chart'; // optional
        $tickerChartApi->save();

        $userAccountApi = new Permission();
        $userAccountApi->name = 'user-account-api';
        $userAccountApi->display_name = 'User Account API'; // optional
        $userAccountApi->description = 'using user account function'; // optional
        $userAccountApi->save();

        $userAccountTradeApi = new Permission();
        $userAccountTradeApi->name = 'user-account-trade-api';
        $userAccountTradeApi->display_name = 'User Account Trade API'; // optional
        $userAccountTradeApi->description = 'using user trade function'; // optional
        $userAccountTradeApi->save();

        $adminApi = new Permission();
        $adminApi->name = 'admin-api';
        $adminApi->display_name = 'Admin API'; // optional
        $adminApi->description = 'using admin menu'; // optional
        $adminApi->save();

        // Attach permission to role
        $admin->attachPermissions(array($adminApi, $tickerChartApi, $tickerApi, $userAccountApi, $userAccountTradeApi));
        $owner->attachPermissions(array($adminApi, $tickerChartApi, $tickerApi, $userAccountApi, $userAccountTradeApi));
        $customer->attachPermissions(array($tickerChartApi, $tickerApi, $userAccountApi, $userAccountTradeApi));

        // Attach role to user
        User::where('name', '=', 'Thanh')->first()->attachRole($owner);
        User::where('name', '=', 'Quang')->first()->attachRole($owner);
        User::where('name', '=', 'Test')->first()->attachRole($admin);
        User::where('name', '=', 'Guest')->first()->attachRole($customer);
        foreach (User::where('name', 'like', 'Admin%')->get() as $a) {
            $a->attachRole($admin);
        }
        foreach (User::where('name', 'like', 'Customer%')->get() as $c) {
            $c->attachRole($customer);
        }

        Model::reguard();
    }

}
