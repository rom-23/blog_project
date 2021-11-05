<?php

namespace App\DataFixtures;

use App\Entity\Address;
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
            $fileName = $this->faker->image('public/uploads/user-image');
            $user     = (new User())
                ->setEmail($this->faker->email)
                ->setRoles(['ROLE_USER'])
                ->setPassword('1234')
                ->setImage(str_replace('public/uploads/user-image/', '', $fileName))
                ->setIsVerified($this->faker->numberBetween(0, 1))
                ->setAccountVerifiedAt(new \DateTimeImmutable('+ 55 minutes'))
                ->setRegisteredAt(new \DateTimeImmutable('now'))
                ->setAccountMustBeVerifiedBefore(new \DateTimeImmutable('+ 1 day'));
            $this->manager->persist($user);
            $this->addReference("user{$i}", $user);

            for ($k = 1; $k <= 3; $k++) {
                $address = (new Address())
                    ->setName(ucfirst($this->faker->words(mt_rand(2, 3), true)))
                    ->setFirstname($this->faker->firstName())
                    ->setCreatedAt(new \DateTimeImmutable('now'))
                    ->setLastname($this->faker->lastname())
                    ->setAddress($this->faker->streetAddress())
                    ->setPostal($this->faker->postcode())
                    ->setCity($this->faker->city())
                    ->setCountry($this->faker->country());

                $this->manager->persist($address);
                $user->addAddress($address);
                $address->setUser($user);
            }
        }
    }
}
