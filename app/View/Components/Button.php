<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;
    public string $type;
    public ?string $href;
    public string $size;
    public bool $confirm;

    public function __construct(
        string $variant = 'primary',
        string $type = 'button',
        ?string $href = null,
        string $size = 'md',
        bool $confirm = false
    ) {
        $this->variant = $variant;
        $this->type = $type;
        $this->href = $href;
        $this->size = $size;
        $this->confirm = $confirm;
    }

    public function render()
    {
        return view('components.button');
    }
}
