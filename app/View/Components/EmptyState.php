<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EmptyState extends Component
{
    public string $message;
    public ?string $action;
    public ?string $href;

    public function __construct(
        string $message = 'No records found.',
        ?string $action = null,
        ?string $href = null
    ) {
        $this->message = $message;
        $this->action = $action;
        $this->href = $href;
    }

    public function render()
    {
        return view('components.empty-state');
    }
}
