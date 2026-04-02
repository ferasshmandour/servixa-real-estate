<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Avatar extends Component
{
    public string $name;
    public ?string $src;
    public string $size;

    public function __construct(string $name = '', ?string $src = null, string $size = 'md')
    {
        $this->name = $name;
        $this->src = $src;
        $this->size = $size;
    }

    public function initials(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        return $initials ?: '?';
    }

    public function render()
    {
        return view('components.avatar');
    }
}
