<?php

namespace Database\Factories\Pool;

use App\Models\Pool\PoolArticle;
use Database\Factories\MockObject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PoolArticleFactory extends Factory
{
    use MockObject;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PoolArticle::class;



    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $title =$this->faker->slug;

        return [
            'title' =>$title,
            'slug'=>Str::slug($title),
            'content'=>$this->faker->realText(500),
            'excerpt'=>$this->faker->realText(200),
            'meta'=>[],
            'iid'=>$this->faker->numberBetween(1,PHP_INT_MAX)
        ];
    }
}
