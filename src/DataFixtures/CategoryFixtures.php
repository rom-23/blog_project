<?php

namespace App\DataFixtures;

use App\Entity\Modelism\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class CategoryFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();
        $this->generateCategories(10);
        $this->manager->flush();
    }

    public function generateCategories(int $number): void
    {
        for ($i = 1; $i <= $number; $i++) {
            $category = (new Category())
                ->setName(ucfirst($this->faker->words(mt_rand(1, 1), true)));
            $this->manager->persist($category);
            $this->addReference("category{$i}", $category);
        }
    }
}
