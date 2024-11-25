<?php

namespace Database\Seeders;

use App\Models\Live\Page;
use App\Models\Live\PageKeywords;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /// Keywords blog child education family
        Team::factory()->count(1)->has(
            Page::factory()->state(
                [
                    'targetLanguage' => 'de',
                    'name'=>'kinder-entwicklung.de',
                    'domain'=>'kinder-entwicklung.de',
                ],
            )->count(1)
            ->has(PageKeywords::factory()->count(1)->state([
                'keyword'=>'child development blog',
                'language'=>'en'
            ]))
        )->create();

    }
}
