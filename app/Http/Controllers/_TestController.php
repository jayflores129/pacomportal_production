<?php 

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class _TestController extends Controller
{
    public function createRole() {
        // Role::create(['name' => 'staff']);
        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'customer']);
        Auth::user()->assignRole('staff', 'admin', 'customer');
    }

    public function createPermission() {
        
    }
} 