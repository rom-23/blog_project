<?php

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeTest extends WebTestCase
{
    /**
     * @dataProvider provideUri
     * @param string $uri
     */
    public function test(string $uri)
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, $uri);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function provideUri(): Generator
    {
        yield ['/'];
        yield ['/root/admin/dev/list'];
        yield ['/root/admin/dev/add'];
        yield ['/symfony/development/publication'];
//        yield ['/?page=3&field=p.title&order=desc&limit=10'];
    }
}
