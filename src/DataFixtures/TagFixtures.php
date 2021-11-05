<?php

namespace App\DataFixtures;

use App\Entity\Development\Note;
use App\Entity\Development\Section;
use App\Entity\Development\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class TagFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();
        $this->generateTags(6);
        $this->manager->flush();
    }

    public function generateTags(int $number): void
    {
        for ($i = 1; $i <= $number; $i++) {
            $tag = (new Tag())->setName(ucfirst($this->faker->words(mt_rand(1, 1), true)));
            $this->manager->persist($tag);
            $this->addReference("tag{$i}", $tag);
        }
    }
}
