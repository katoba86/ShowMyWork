<?php

namespace App\Http\Livewire;

use App\Actions\Crud\Pages\AddOption;
use App\AppConfig;
use App\Models\Live\Page;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Livewire\Component;

class Crud extends Component
{
    public $createOptionForm = [
        'name' => '',
        'value' => '',
    ];

    /**
     * @var bool
     */
    public $isManagingOptions = false;
    /**
     * @var User
     */
    public $user;
    /**
     * @var Page
     */
    public $managingOptionsFor;

    public function setAndRedirect($id)
    {


        session([AppConfig::PID=>$id]);
        $this->redirect('/page/overview');
    }


    public function deleteOption($optionId)
    {

        $this->managingOptionsFor->options()->where('id', $optionId)->first()->delete();
    }

    public function createOption()
    {
        AddOption::run($this->managingOptionsFor,$this->createOptionForm['name'],$this->createOptionForm['value']);
    }

    public function manageOptions($id)
    {
        $user = Auth::user();
        $this->isManagingOptions = true;

        $this->managingOptionsFor = $user->currentTeam->pages()->where(
            'id', $id
        )->firstOrFail();


    }

    public function render(Request  $request)
    {
        $this->user = $request->user();

        $team =  $request->user()->currentTeam;


        return view('livewire.pages',[
            'request' => $request,
            'user' => $request->user(),
            'page'=>null,
            'team' => $team,
        ]);
    }
}
