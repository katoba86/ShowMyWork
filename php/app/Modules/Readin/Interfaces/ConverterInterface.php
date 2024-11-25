<?php


namespace App\Modules\Readin\Interfaces;


use App\Models\Category;

interface ConverterInterface
{
        public function getCategories():void;
        public function getTags():void;
        public function getPosts():void;
        public function getMetaInformations():void;



}
