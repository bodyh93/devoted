<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DataHash;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\DataHashRepository;

class MainController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private DataHashRepository $dataHashRepository;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->dataHashRepository = $entityManager->getRepository(DataHash::class);
        $this->validator = $validator;
    }

    #[Route('/', name: 'store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $jsonData = $request->toArray();
        if (!key_exists('data', $jsonData)) {
            return new JsonResponse('Wrong json format!', Response::HTTP_BAD_REQUEST);
        }

        $dataHashObject = new DataHash;
        $data = is_string($jsonData['data']) ? $jsonData['data'] : json_encode($jsonData['data']);
        $dataHashObject->setData($data);
        $dataHashObject->setHashCode(sha1($data));

        $errors = $this->validator->validate($dataHashObject);
        if (count($errors) > 0) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        $notice = null;
        if (null !== $this->dataHashRepository->findOneBy(['hashCode' => $dataHashObject->getHashCode()])) {
            $notice = 'An item with the same hash code already exists, but we are adding the current one regardless.';
        }
        $this->entityManager->persist($dataHashObject);
        $this->entityManager->flush();

        $responseArray = ['hash' => $dataHashObject->getHashCode()];
        if (null !== $notice) {
            $responseArray['notice'] = $notice;
        }

        return new JsonResponse($responseArray);
    }

    #[Route('/hash/{hashCode}', name: 'read', methods: ['GET'])]
    public function read(string $hashCode): Response
    {
        $constraint = new Assert\Regex([
            'pattern' => '/^[0-9a-f]{40}$/i',
            'message' => 'Invalid hash code format.',
        ]);

        $violations = $this->validator->validate($hashCode, $constraint);

        if (count($violations) > 0) {
            return new JsonResponse((string)$violations, Response::HTTP_BAD_REQUEST);
        }

        $dataHashObjects = $this->dataHashRepository->findBy(['hashCode' => $hashCode]);

        if (0 === count($dataHashObjects)) {
            return new JsonResponse('No matching records were found.', Response::HTTP_NOT_FOUND);
        }

        $responseArray = $this->collectItemsForResponse($dataHashObjects);

        return new JsonResponse($responseArray);
    }

    /**
     * @param array<DataHash> $dataHashObjects
     * @return mixed[]
     */
    private function collectItemsForResponse(array $dataHashObjects): array
    {
        $responseArray = ['item' => array_pop($dataHashObjects)->getDataForResponse()];
        if (count($dataHashObjects)) {
            foreach ($dataHashObjects as $dataHashObject) {
                $responseArray['collisions'][] = $dataHashObject->getDataForResponse();
            }
        }

        return $responseArray;
    }
}
