<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public string $name;
    public ?string $label;
    public array $options;
    public mixed $selected;
    public ?string $placeholder;

    public function __construct(
        string $name,
        array $options = [],
        ?string $label = null,
        mixed $selected = null,
        ?string $placeholder = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = $selected;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.select');
    }
}
