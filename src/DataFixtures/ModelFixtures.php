<?php

namespace App\DataFixtures;

use App\Entity\Modelism\Category;
use App\Entity\Modelism\Image;
use App\Entity\Modelism\Model;
use App\Entity\Modelism\Opinion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ModelFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();
        $this->generateModels(20);
        $this->manager->flush();
    }

    public function generateModels(int $number): void
    {
        for ($i = 1; $i <= $number; $i++) {
            $thumbnail = $this->faker->image('public/uploads/models/thumbnails');
            $category  = $this->getReference('category' . mt_rand(1, 10));
            $option    = $this->getReference('option' . mt_rand(1, 20));

            $model = new Model();
            $model->setName(ucfirst($this->faker->words(mt_rand(3, 4), true)));
            $model->setCreatedAt(new \DateTimeImmutable('now'));
            $model->setThumbnail(str_replace('public/uploads/models/thumbnails/', '', $thumbnail));
            $model->setDescription(ucfirst($this->faker->realText(mt_rand(500, 1500))));
            $model->setPrice($this->faker->randomNumber(2));
            for ($k = 1; $k <= 5; $k++) {
                $fileName = $this->faker->image('public/uploads/models/modelsAttachments');
                $image    = new Image();
                $image->setName(str_replace('public/uploads/models/modelsAttachments/', '', $fileName));
                $image->setCreatedAt(new \DateTimeImmutable('now'));
                $model->addImage($image);
            }
            $model->addCategory($category);
            $model->addOption($option);
            for ($k = 1; $k <= 5; $k++) {
                $opinion = new Opinion();
                $opinion->setVote($this->faker->numberBetween(1, 5));
                $opinion->setCreatedAt(new \DateTimeImmutable('now'));
                $opinion->setComment(ucfirst($this->faker->realText(mt_rand(100, 300))));
                $opinion->setUser($this->getReference('user' . mt_rand(1, 40)));
                $opinion->setModel($model);
                $this->manager->persist($opinion);
            }

            $this->manager->persist($model);
            $this->addReference("model{$i}", $model);

        }
    }
}
