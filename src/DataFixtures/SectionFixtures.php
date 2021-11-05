<?php

namespace App\DataFixtures;

use App\Entity\Development\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class SectionFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();
        $this->generateSections(10);
        $this->manager->flush();
    }

    public function generateSections(int $number): void
    {
        for ($i = 1; $i <= $number; $i++) {
            $section = (new Section())->setTitle(ucfirst($this->faker->word));
            $this->manager->persist($section);
            $this->addReference("section{$i}", $section);
        }
    }
}
