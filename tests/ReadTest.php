<?php

namespace App\Tests;

use App\Entity\Development\Development;
use App\Entity\Development\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadTest extends WebTestCase
{
    /**
     * @param string $uri
     */
    public function test()
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router.default');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user =  $entityManager->getRepository(User::class)->findOneBy(['email'=>'adm@adm.com']);

        $client->loginUser($user);

        $dev           = $entityManager->getRepository(Development::class)->findOneBy([]);
        $crawler       = $client->request(Request::METHOD_GET, $router->generate('development_publication_view', ['id' => $dev->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('html h5', $dev->getTitle());

        $form = $crawler->filter('form[name=post]')->form([
            'post[title]' => 'Title',
            'post[content]' => 'Content'
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('html h5', $dev->getTitle());
        $this->assertSelectorTextContains('html', $dev->getContent());
    }
}
