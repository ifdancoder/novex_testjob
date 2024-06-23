<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Symfony\Component\String\ByteString;

class UserTest extends ApiTestCase
{
    private $entityManager;
    private $userRepository;


    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->clearDatabase();
    }

    private function clearDatabase()
    {
        $connection = $this->entityManager->getConnection();
        $schemaManager = $connection->createSchemaManager();

        $tables = $schemaManager->listTableNames();

        foreach ($tables as $table) {
            $connection->executeStatement('TRUNCATE TABLE ' . $table . ' CASCADE');
        }
    }

    public function testCreateUser(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $rand_email = ByteString::fromRandom(32)->toString() . '@example.com';

        $client->request('POST', '/api/user', [
            'json' => [
                'email' => $rand_email,
                'name' => 'Test User',
                'age' => 30,
                'sex' => 'male',
                'birthday' => '1990-01-01',
                'phone' => '1234567890',
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'email' => $rand_email,
            'name' => 'Test User',
            'age' => 30,
            'sex' => 'male',
            'birthday' => '1990-01-01T00:00:00+00:00',
            'phone' => '1234567890',
        ]);
    }

    public function testGetUser(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $rand_email = ByteString::fromRandom(32)->toString() . '@example2.com';

        $user = new User();
        $user->setEmail($rand_email)
            ->setName('Test User 2')
            ->setAge(25)
            ->setSex('female')
            ->setBirthday(new \DateTime('1995-05-15'))
            ->setPhone('0987654321')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $response = $client->request('GET', '/api/user/' . $user->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'email' => $rand_email,
            'name' => 'Test User 2',
            'age' => 25,
            'sex' => 'female',
            'birthday' => '1995-05-15T00:00:00+00:00',
            'phone' => '0987654321',
        ]);
    }

    public function testUpdateUser(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $rand_email = ByteString::fromRandom(32)->toString() . '@example2.com';

        $user = new User();
        $user->setEmail($rand_email)
            ->setName('Test User 3')
            ->setAge(45)
            ->setSex('female')
            ->setBirthday(new \DateTime('1975-03-03'))
            ->setPhone('6677889900')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $rand_email2 = ByteString::fromRandom(32)->toString() . '@example2.com';

        $client->request('PUT', '/api/user/' . $user->getId(), [
            'json' => [
                'email' => $rand_email2,
                'name' => 'Updated User',
                'age' => 40,
                'sex' => 'male',
                'birthday' => '1980-10-10',
                'phone' => '5566778899',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'email' => $rand_email2,
            'name' => 'Updated User',
            'age' => 40,
            'sex' => 'male',
            'birthday' => '1980-10-10T00:00:00+00:00',
            'phone' => '5566778899',
        ]);
    }

    public function testDeleteUser(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $user = new User();
        $user->setEmail('test4@example.com')
            ->setName('Test User 4')
            ->setAge(45)
            ->setSex('female')
            ->setBirthday(new \DateTime('1975-03-03'))
            ->setPhone('6677889900')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $client->request('DELETE', '/api/user/' . $user->getId());

        $this->assertResponseStatusCodeSame(204);
    }
}
