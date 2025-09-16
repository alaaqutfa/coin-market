<?php

namespace App\Http\Controllers;

class DashboardController
{
    public function dashboard() {
        // return view('dashboard');
        return redirect('products');
    }
}
