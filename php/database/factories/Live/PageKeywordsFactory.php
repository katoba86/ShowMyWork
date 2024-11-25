<?php

namespace Database\Factories\Live;

use App\Models\Live\PageKeywords;
use Illuminate\Database\Eloquent\Factories\Factory;


class PageKeywordsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PageKeywords::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'keyword' => $this->faker->name(),
            'language'=>'en',
        ];
    }



}
