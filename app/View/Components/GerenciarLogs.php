<?php

namespace App\View\Components;

use App\Models\Log;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GerenciarLogs extends Component
{
    public $logs;

    public function __construct()
    {
        $this->logs = Log::all();
    }

    public function render(): View|Closure|string
    {
        return view('components.gerenciar-logs', ['logs' => $this->logs]);
    }
}
