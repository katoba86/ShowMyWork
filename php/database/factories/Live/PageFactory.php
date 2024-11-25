<?php

namespace Database\Factories\Live;

use App\Models\Live\Page;
use Illuminate\Database\Eloquent\Factories\Factory;


class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'domain'=>$this->faker->domainName(),
            'targetLanguage'=>'en'
        ];
    }

    public function inLanguage(string $language){
        return $this->state(function (array $attributes) use ($language) {
            return [
                'targetLanguage' => $language,
            ];
        });
    }


}
