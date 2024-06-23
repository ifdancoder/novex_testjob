<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/api/user', name: 'create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(UserType::class);

        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userService->createUser($data);
            return $this->json($user, Response::HTTP_CREATED);
        }

        return $this->json($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    #[Route('api/user/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $user = $this->userService->getUser($id);
        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user, Response::HTTP_OK);
    }

    #[Route('api/user/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): Response
    {
        $user = $this->userService->getUser($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedUser = $this->userService->updateUser($user, $data);
            return $this->json($updatedUser);
        }

        return $this->json($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    #[Route('api/user/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $user = $this->userService->getUser($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->userService->deleteUser($user);

        return $this->json(['message' => 'User deleted'], Response::HTTP_NO_CONTENT);
    }
}
