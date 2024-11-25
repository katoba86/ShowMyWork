<?php

namespace App\Http\Livewire\Pages;

use App\AppConfig;
use App\Models\Live\Page;
use App\Models\Live\PageOptions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateHeaderImage extends Component
{


     use WithFileUploads;

    /**
     * The new avatar for the user.
     *
     * @var mixed
     */
    public $photo;
    public $state = [];
    public $fileTitle, $fileName;

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {
        $id = session(AppConfig::PID);
        $stateArray = Auth::user()->withoutRelations()->toArray();
        $currentPage = Auth::user()->currentTeam->pages()->where(
            'id', $id
        )->firstOrFail();
        $stateArray["page"]=$currentPage->toArray();

        $this->state = $stateArray;
    }


    public function getPhotoUrl()
    {
        $currentPage = Auth::user()->currentTeam->pages()->where(
            'id', $this->state["page"]["id"]
        )->firstOrFail();
        $option= $currentPage->getOption('header');
        return "http://".env('PREVIEW_HOST')."/media/".$option;
    }

    public function updateHeaderImage()
    {

        $this->validate([
            'photo' => ['image', 'max:4096', 'mimes:jpeg,png,jpg,gif'] // 1MB Max
        ]);
        $path = $this->photo->store('photos');
        /* @var $currentPage Page */
        $currentPage = Auth::user()->currentTeam->pages()->where(
            'id', $this->state["page"]["id"]
        )->firstOrFail();



        $currentPage->options()->updateOrCreate([
            'key'=>'header',
            'page'=>$this->state["page"]["id"]
        ],[
            'value'=>$path
        ]);






    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }


    public function render()
    {
        return view('livewire.pages.update-header-image');
    }
}
