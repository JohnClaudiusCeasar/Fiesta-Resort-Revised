<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class DashboardController extends Controller
{
    
    public function show(){
        return Inertia::render('client/Dashboard', [
            'user' => auth()->check() ? auth()->user() : null,
            'usersCount' => User::count(),
        ]);
    }
}
