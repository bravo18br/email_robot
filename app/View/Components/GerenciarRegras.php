<?php

namespace App\View\Components;

use App\Models\Regra;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GerenciarRegras extends Component
{
    public $regras;

    public function __construct()
    {
        $this->regras = Regra::all();
    }

    public function render(): View|Closure|string
    {
        return view('components.gerenciar-regras', ['regras' => $this->regras]);
    }
}
