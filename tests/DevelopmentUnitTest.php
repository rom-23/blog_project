<?php

namespace App\Tests;

use App\Entity\Development\Development;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DevelopmentUnitTest extends KernelTestCase
{
    public function getEntity(): Development
    {
        return (new Development())
            ->setTitle('Test tiltle')
            ->setSlug('test-title')
            ->setContent('jkjk jk jlk  j kjlk kj j lkj kl')
            ->setCreatedAt(new \DateTime('now'))
            ->setFileUrl('Url to file')
            ->setUpdatedAt(new \DateTime('now'));
    }

    public function assertHasError(Development $dev, int $number = 0 )
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($dev);
        $this->assertCount($number, $error);
    }

    public function testValidityEntity()
    {
        $this->assertHasError($this->getEntity(), 0 );

    }

    public function testInvalidEntity()
    {
        $this->assertHasError($this->getEntity()->setTitle('t'), 1 );

    }
}
