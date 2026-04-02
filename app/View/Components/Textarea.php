<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Textarea extends Component
{
    public string $name;
    public ?string $label;
    public mixed $value;
    public int $rows;
    public ?string $placeholder;

    public function __construct(
        string $name,
        ?string $label = null,
        mixed $value = null,
        int $rows = 4,
        ?string $placeholder = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->rows = $rows;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.textarea');
    }
}
