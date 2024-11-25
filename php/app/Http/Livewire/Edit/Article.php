<?php

namespace App\Http\Livewire\Edit;

use App\AppConfig;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Article extends Component
{

    use WithFileUploads;

    private $pid;
    public $photo;
    public $state = [];
    public $fileTitle, $fileName;


    public array $article;



    public function mount($id)
    {
        $this->pid = $id;
    }


    public function send()
    {
        $this->validate([
            'photo' => ['image', 'max:4096', 'mimes:jpeg,png,jpg,gif'] // 1MB Max
        ]);
        echo "<pre>";print_r($this->photo);exit();
    }


    public function getPhotoUrl()
    {
        return "http://".env('PREVIEW_HOST')."/media/".$this->article["id"].".jpg";
    }

    public function render()
    {


        $user = Auth::user();
        $id = session(AppConfig::PID);
        $currentPage = $user->currentTeam->pages()->where(
            'id', $id
        )->firstOrFail();

        $this->article =  $currentPage->articles()->where('id',$this->pid)->firstOrFail()->toArray();


        return view('livewire.edit.article',[
            'page'=>$currentPage,
        ]);
    }
}
