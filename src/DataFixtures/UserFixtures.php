<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class UserFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();
        $this->generateUsers(20);
        $this->manager->flush();
    }

    public function generateUsers(int $number): void
    {
        for ($i = 1; $i <= $number; $i++) {
            $user = (new User())
                ->setEmail($this->faker->email)
                ->setRoles(['ROLE_USER'])
                ->setPassword('1234')
                ->setIsVerified($this->faker->numberBetween(0, 1))
                ->setAccountVerifiedAt(new \DateTimeImmutable('+ 55 minutes'))
                ->setRegisteredAt(new \DateTimeImmutable('now'))
                ->setAccountMustBeVerifiedBefore(new \DateTimeImmutable('+ 1 day'));
            $this->manager->persist($user);
            $this->addReference("user{$i}", $user);
        }
    }
}
