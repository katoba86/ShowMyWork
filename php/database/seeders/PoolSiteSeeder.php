<?php


namespace Database\Seeders;


use App\Models\Pool\PoolArticle;
use App\Models\Pool\PoolSite;
use Illuminate\Database\Seeder;

class PoolSiteSeeder extends Seeder
{


    public function run()
    {

        PoolSite::factory()->count(1)->create([
            'sourceLanguage'=>'en',
            ''
        ]);
    }

}
