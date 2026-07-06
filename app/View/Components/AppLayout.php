<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Menentukan tampilan layout aplikasi yang digunakan membungkus konten halaman 
     * digunakan setelah login 
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
