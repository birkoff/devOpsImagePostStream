<?php

use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 20; $i++) {
            $data[] = [
                'title'      => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'imageUrl'   => $faker->imageUrl($width = 640, $height = 480)
            ];
        }

        $this->insert('post', $data);
    }
}
