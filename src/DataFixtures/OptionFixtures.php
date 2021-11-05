<?php

namespace App\DataFixtures;

use App\Entity\Modelism\Option;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class OptionFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();
        $this->generateOptions(6);
        $this->manager->flush();
    }

    public function generateOptions(int $number): void
    {
        for ($i = 1; $i <= $number; $i++) {
            $option = (new Option())->setName(ucfirst($this->faker->words(mt_rand(1, 1), true)));
            $this->manager->persist($option);
            $this->addReference("option{$i}", $option);
        }
    }
}
