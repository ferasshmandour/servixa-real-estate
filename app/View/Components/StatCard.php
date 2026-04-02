<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatCard extends Component
{
    public string $label;
    public mixed $value;
    public string $icon;
    public ?string $trend;
    public string $color;

    public function __construct(
        string $label,
        mixed $value = 0,
        string $icon = 'chart-bar',
        ?string $trend = null,
        string $color = 'purple'
    ) {
        $this->label = $label;
        $this->value = $value;
        $this->icon = $icon;
        $this->trend = $trend;
        $this->color = $color;
    }

    public function render()
    {
        return view('components.stat-card');
    }
}
