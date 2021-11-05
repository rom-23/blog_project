<?php

namespace App\DataFixtures;

use App\Entity\Development\Development;
use App\Entity\Development\DevelopmentFile;
use App\Entity\Development\Note;
use App\Entity\Development\Post;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    private ObjectManager $manager;
    private Generator $faker;
    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();
        $this->generateDevelopments(20);
        $this->manager->flush();
    }

    public function generateDevelopments(int $number): void
    {
        for ($i = 1; $i <= $number; $i++) {
            $dev = new Development();
            [
                'dateObject' => $dateObject,
                'dateString' => $dateString
            ] = $this->generateRandomDateBetweenRange('01/01/2020', '09/12/2020');

            $title = $this->faker->words(mt_rand(3, 4), true);
            $slug  = $this->slugger->slug(strtolower($title));
            $tags  = $this->getReference('tag' . mt_rand(1, 6));

            $dev->setTitle(ucfirst($title));
            $dev->setContent($this->faker->realText(mt_rand(100, 500)));
            $dev->setCreatedAt($dateObject);
            $dev->setSlug($slug);
            $dev->setFiles(new ArrayCollection([]));
            $dev->setSection($this->getReference('section' . mt_rand(1, 6)));
            $dev->setUpdatedAt(null);
            $dev->addTag($tags);

            // On upload et on génère les documents
            for ($k = 1; $k <= 2; $k++) {
                $fileName = $this->faker->image('public/uploads/dev-files');
                $filePfd  = new DevelopmentFile();
                $filePfd->setName(str_replace('public/uploads/dev-files/', '', $fileName));
                $filePfd->setCreatedAt($dateObject);
                $filePfd->setPath('public/uploads/dev-files/' . $fileName);
                $dev->addFile($filePfd);
            }
            $this->manager->persist($dev);

            for ($j = 1; $j <= 2; $j++) {
                $note = (new Note())
                    ->setTitle(ucfirst($this->faker->realText(mt_rand(10, 30))))
                    ->setContent(ucfirst($this->faker->realText(mt_rand(10, 250))))
                    ->setCreatedAt($dateObject);
                $user = $this->getReference('user' . mt_rand(1, 6));
                $this->manager->persist($note);

                $note->setDevelopment($dev);
                $note->setUser($user);
                $dev->addNote($note);

                for ($k = 1; $k <= 2; $k++) {
                    $user = $this->getReference('user' . mt_rand(1, 6));
                    $post = (new Post())
                        ->setTitle(ucfirst($this->faker->realText(mt_rand(10, 30))))
                        ->setContent(ucfirst($this->faker->realText(mt_rand(10, 350))))
                        ->setCreatedAt($dateObject)
                        ->setParent(null);
                    $this->manager->persist($post);

                    $post->setDevelopment($dev);
                    $post->setUser($user);
                }
            }
        }
    }

    /**
     * @throws HttpException
     */
    #[ArrayShape(['dateObject' => "\DateTimeImmutable", 'dateString' => "string"])]
    private function generateRandomDateBetweenRange(string $start, string $end): array
    {
        $startDate = \DateTime::createFromFormat('d/m/Y', $start);
        $endDate   = \DateTime::createFromFormat('d/m/Y', $end);
        if (!$startDate || !$endDate) {
            throw new HttpException(400, 'Date is not on the requested format');
        }
        $randomTimeStamp   = mt_rand($startDate->getTimestamp(), $endDate->getTimestamp());
        $dateTimeImmutable = (new DateTimeImmutable())->setTimestamp($randomTimeStamp);
        return [
            'dateObject' => $dateTimeImmutable,
            'dateString' => $dateTimeImmutable->format('d-m-Y')
        ];
    }

    public function getDependencies(): array
    {
        return [
            SectionFixtures::class,
            UserFixtures::class,
            TagFixtures::class,
            OptionFixtures::class,
            CategoryFixtures::class,
            ModelFixtures::class
        ];
    }
}
