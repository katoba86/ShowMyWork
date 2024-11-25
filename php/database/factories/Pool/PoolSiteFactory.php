<?php

namespace Database\Factories\Pool;

use App\Models\Pool\PoolSite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoolSiteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PoolSite::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'domain' => $this->faker->domainName,
            'sourceLanguage'=>$this->faker->languageCode
        ];
    }
}
