<?php

namespace App\Tests;

use App\Entity\Development\Development;
use App\Repository\Development\DevelopmentRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DevelopmentRepositoryTest extends KernelTestCase
{

    public function testCount()
    {
        self::bootKernel();
        $devs = self::getContainer()->get(DevelopmentRepository::class)->count([]);
        $this->assertEquals(8, $devs);
    }


}
