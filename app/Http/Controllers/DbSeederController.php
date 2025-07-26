<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DbSeederController extends Controller
{
    public function change_user()
    {
        $pacom_users = DB::table('pacom_user')->get();

        foreach ($pacom_users as $pacom_user) {
            $user = User::where('email', $pacom_user->userEmail)->first();
 
            if ($user && $pacom_user->userIsAdmin == 'y') {
                $user->assignRole('admin');
            }
            elseif ($user && $pacom_user->userIsCustomer == 'y') {
                $user->assignRole('customer');
            }
            elseif ($user && $pacom_user->userIsStaff == 'y') {
                $user->assignRole('staff');
            }
            elseif ($user && $pacom_user->userIsTrainer == 'y') {
                $user->assignRole('trainer');
            }
            else {
                $user->assignRole('customer');
            }
        }

        return 'success';
    }
}
