<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * menentukan tampilan layout aplikasi yang belum login digunakaan untuk membungkus halaman 
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
