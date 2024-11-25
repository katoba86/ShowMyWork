<?php

namespace App\Http\Livewire\Tables;
use App\AppConfig;
use Auth;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class ArticleTable extends LivewireDatatable
{

    public $perPage = 20;

    public function builder()
    {
        $setId = session(AppConfig::PID);


        $page =Auth::user()->currentTeam->pages()->where(
            'id', $setId
        )->firstOrFail();

        return $page->articles();

    }

    public function columns()
    {
        return [


            NumberColumn::name('id')->label('id'),
            Column::name('title')->label('title')->searchable(),
            Column::name('slug')->label('slug')->searchable(),
            Column::callback(['id'], function ($id) {
                return "<a href='".route('article.edit',['id'=>$id])."'>Edit</a>";
            })

        ];
    }
}
