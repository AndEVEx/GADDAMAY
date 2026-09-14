<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Menu KKTP')]
class KktpHub extends Component
{
    public function render()
    {
        return view('livewire.guru.kktp-hub');
    }
}
