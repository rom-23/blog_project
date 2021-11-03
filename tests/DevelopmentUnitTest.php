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
            ->setContent('jkjk jk jlk lplj kjlk kjj lkj kl')
            ->setCreatedAt(new \DateTimeImmutable('now'))
            ->setUpdatedAt(new \DateTimeImmutable('now'));
    }

    public function assertHasError(Development $dev, int $number = 0)
    {
        $kernel = self::bootKernel();
        $errors  = $kernel->getContainer()->get('validator')->validate($dev);
        $messages = [];
        foreach($errors as $error){
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
    }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidityEntity()
    {
        $this->assertHasError($this->getEntity(), 0);

    }

    public function testInvalidTitleEntity()
    {
        $this->assertHasError($this->getEntity()->setTitle(' '), 1);
        $this->assertHasError($this->getEntity()->setTitle('p'), 1);
        $this->assertHasError($this->getEntity()->setTitle('ppp'), 1);
    }

    public function testInvalidContentBlankEntity()
    {
        $this->assertHasError($this->getEntity()->setContent(''), 1);
    }

    public function testInvalidUsedTitle() // unicite de l'entite
    {
        $this->assertHasError($this->getEntity()->setTitle('in totam in'), 1);

    }

}
