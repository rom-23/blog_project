<?php

namespace App\Tests;

use App\Entity\Development\Development;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DevelopmentUnitTest extends KernelTestCase
{

    public function getEntity(): Development
    {
        return (new Development())
            ->setTitle('Test tiltle')
            ->setSlug('test-title')
            ->setContent('jkjk jk jlk  j kjlk kj j lkj kl')
            ->setCreatedAt(new \DateTimeImmutable('now'))
            ->setUpdatedAt(new \DateTimeImmutable('now'));
    }

    public function assertHasError(Development $dev, int $number = 0)
    {
        $kernel = self::bootKernel();
        $error = $kernel->getContainer()->get('validator')->validate($dev);
        $this->assertCount($number, $error);
    }

    public function testValidityEntity()
    {
        $this->assertHasError($this->getEntity(), 0 );

    }

    public function testInvalidEntity()
    {
        $this->assertHasError($this->getEntity()->setTitle('pp'), 1 );
    }

    public function testInvalidCodeBlank()
    {
        $this->assertHasError($this->getEntity()->setTitle(''), 1 );
    }

}
