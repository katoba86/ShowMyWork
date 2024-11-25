<?php

namespace App\Http\Livewire\List;

use App\AppConfig;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Article extends Component
{
    public function render()
    {
        $user = Auth::user();
        $id = session(AppConfig::PID);
        $currentPage = $user->currentTeam->pages()->where(
            'id', $id
        )->firstOrFail();

        return view('livewire.list.article',[
            'page'=>$currentPage
        ]);
    }
}
