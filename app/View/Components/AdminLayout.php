<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminLayout extends Component
{
    public function __construct(public string $title = 'Admin') {}

    public function render()
    {
        return view('admin.layout');
    }
}
