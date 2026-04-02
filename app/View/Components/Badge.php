<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Badge extends Component
{
    public string $status;

    public function __construct(string $status = 'pending')
    {
        $this->status = $status;
    }

    public function render()
    {
        return view('components.badge');
    }
}
