<?php

namespace App\Tests\Controller;

use App\Entity\DataHash;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public static function setUpBeforeClass(): void
    {
        passthru('bin/console doctrine:database:drop --force --env=test');
        passthru('bin/console doctrine:database:create --env=test');
        passthru('bin/console doctrine:schema:create --env=test');
        parent::setUpBeforeClass();
    }


    public function testStoreSuccess(): void
    {
        $this->sendPostData(['data' => 'some data 1']);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('CONTENT_TYPE', 'application/json');
        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('hash', $responseArray);
        $this->assertArrayNotHasKey('notice', $responseArray);
        $this->assertSame('db42c2bf3e98b9aea10a42b86ccab27254c89ff4', $responseArray['hash']);

        $this->sendPostData(['data' => 'some data 1']);
        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('hash', $responseArray);
        $this->assertArrayHasKey('notice', $responseArray);
    }

    public function testStoreFail(): void
    {
        $this->sendPostData('123');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testReadSuccess(): void
    {
        $this->sendPostData(['data' => 'some data 1']);
        $this->sendGetData('db42c2bf3e98b9aea10a42b86ccab27254c89ff4');
        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('item', $responseArray);
        $this->assertSame('some data 1', $responseArray['item']);
        $this->assertArrayNotHasKey('collisions', $responseArray);

        $this->sendPostData(['data' => 'some data 1']);
        $this->sendGetData('db42c2bf3e98b9aea10a42b86ccab27254c89ff4');
        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('item', $responseArray);
        $this->assertSame('some data 1', $responseArray['item']);
        $this->assertArrayHasKey('collisions', $responseArray);
        $this->assertSame('some data 1', end($responseArray['collisions']));

    }

    public function testReadFail(): void
    {
        $this->sendGetData('1a8af7b0d03f643a7eaf6d8bcb3e0e3f6d8d6b6f');
        $this->assertResponseStatusCodeSame(404);

        $this->sendGetData('qwe');
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Invalid hash code format.', $this->client->getResponse()->getContent());
    }
    
    private function sendPostData(mixed $data): void
    {
        $this->client->request(
            'POST',
            '/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }
    
    private function sendGetData(string $hashCode): void
    {
        $this->client->request(
            'GET',
            "/hash/$hashCode"
        );
    }

    public static function tearDownAfterClass(): void
    {
        passthru('bin/console doctrine:database:drop --force --env=test');
        parent::tearDownAfterClass();
    }

    protected function tearDown(): void
    {
        $this->entityManager->createQueryBuilder()->delete(DataHash::class, 'dh')->getQuery()->execute();
        parent::tearDown();
    }
}
