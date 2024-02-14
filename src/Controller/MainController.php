<?php

namespace App\Controller;

use App\Entity\JsonHash;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MainController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/', name: 'store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $jsonData = $request->get('jsonData');
        $jsonHashObject = new JsonHash;
        $jsonHashObject->setJsonData($jsonData);
        $jsonHashObject->setHashCode(sha1($jsonData));

        $errors = $this->validator->validate($jsonHashObject);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($jsonHashObject);
        $this->entityManager->flush();

        return new Response((string) $jsonHashObject);
    }

    #[Route('/hash/{hashCode}', name: 'read', methods: ['GET'])]
    public function showDataByHashCode(string $hashCode): Response
    {
        $constraint = new Assert\Regex([
            'pattern' => '/^[0-9a-f]{40}$/i',
            'message' => 'Invalid hash code format.',
        ]);

        $violations = $this->validator->validate($hashCode, $constraint);

        if (count($violations) > 0) {
            return new Response((string) $violations, Response::HTTP_BAD_REQUEST);
        }

        $jsonHashRepository = $this->entityManager->getRepository(JsonHash::class);
        $jsonHashObject = $jsonHashRepository->findOneBy(['hashCode' => $hashCode]);

        return new Response((string) $jsonHashObject);
    }
}