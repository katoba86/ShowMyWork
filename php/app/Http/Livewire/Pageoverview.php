<?php

namespace App\Http\Livewire;

use App\AppConfig;
use Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Pageoverview extends Component
{

    use WithFileUploads;


    public $photo;


    public function save()
    {

    }

    public function render()
    {

        $user = Auth::user();
        $id = session(AppConfig::PID);
        $currentPage = $user->currentTeam->pages()->where(
            'id', $id
        )->firstOrFail();



        return view('livewire.pages.detail',[
            'page'=>$currentPage
        ]);
    }
}
