<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public string $name;
    public string $type;
    public ?string $label;
    public mixed $value;
    public ?string $placeholder;

    public function __construct(
        string $name,
        string $type = 'text',
        ?string $label = null,
        mixed $value = null,
        ?string $placeholder = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->value = $value;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.input');
    }
}
