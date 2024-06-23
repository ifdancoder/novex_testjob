<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserService
{
    private $entityManager;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data): User
    {

        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setAge($data['age']);
        $user->setSex($data['sex']);
        $user->setBirthday(new \DateTime($data['birthday']));
        $user->setPhone($data['phone']);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function getUser(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function updateUser(User $user, array $data): User
    {
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setAge($data['age']);
        $user->setSex($data['sex']);
        $user->setBirthday(new \DateTime($data['birthday']));
        $user->setPhone($data['phone']);
        $user->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
