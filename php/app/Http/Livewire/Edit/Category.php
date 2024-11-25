<?php

namespace App\Http\Livewire\Edit;

use App\AppConfig;
use App\Models\Live\Page;
use App\Modules\Translator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Category as Cat;

class Category extends Component
{

    /**
     * @var Page
     */
    public $page;
    /**
     * @var Cat
     */
    public $category;



    public $form = [

    ];


    public function submit()
    {

        $translator = new Translator($this->category);
        $translator->setLocale($this->page->getOption('targetLanguage'));
        $model = $translator->getTranslationModel('name','en');
        if($model){
            $model->value = $this->form['name'];
            $model->update();
        }else {
            $translator->createTranslation('name', $this->form['name']);
        }
        return 'test';
    }

    public function test()
    {
        $this->form["name"]=strtoupper($this->category["name"]);
    }

    public function mount($id){


        $user = Auth::user();
        $pid = session(AppConfig::PID);

        $currentPage = $user->currentTeam->pages()->where(
            'id', $pid
        )->firstOrFail();
        $this->page = $currentPage;
        $this->category = $this->page->categories()->where('id',(int)$id)->first();
        $this->form = $this->category->toArray();
    }

    public function render()
    {


        return view('livewire.edit.category');
    }
}
