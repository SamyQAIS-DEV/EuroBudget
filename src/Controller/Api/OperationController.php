<?php

namespace App\Controller\Api;

use App\Entity\Operation;
use App\Enum\AlertEnum;
use App\Exception\OperationUnauthorizedException;
use App\Repository\OperationRepository;
use App\Security\Voter\OperationVoter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/operations', name: 'operations_')]
class OperationController extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route(path: '/current-month', name: 'current_month', methods: ['GET'])]
    public function currentMonth(): JsonResponse
    {
        $now = new DateTime();
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $operations = $this->operationRepository->findForYearAndMonth($favoriteDepositAccount->getId(), (int) $now->format('Y'), (int) $now->format('m'));

        return $this->json(data: $operations, context: ['groups' => ['read']]);
    }

    #[Route(path: '/years-months', name: 'years_months', methods: ['GET'])]
    public function yearsMonths(): JsonResponse
    {
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $yearsMonths = $this->operationRepository->findYearsMonths($favoriteDepositAccount->getId());
        $currentMonth = (new DateTime())->format('Y/m');
        if (count($yearsMonths) === 0 || !$this->monthInArray($currentMonth, $yearsMonths)) {
            $yearsMonths = [['path' => $currentMonth, 'count' => 0], ...$yearsMonths];
        }

        return $this->json(data: $yearsMonths, context: ['groups' => ['read']]);
    }

    #[Route(path: '/{year}/{month}', name: 'for_month', methods: ['GET'])]
    public function forMonth(int $year, int $month): JsonResponse
    {
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $operations = $this->operationRepository->findForYearAndMonth($favoriteDepositAccount->getId(), $year, $month);

        return $this->json(data: $operations, context: ['groups' => ['read']]);
    }

    #[Route(path: '', name: 'post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        if (!$this->isGranted(OperationVoter::POST)) {
            $this->addAlert(AlertEnum::WARNING, 'Vous ne pouvez pas ajouter plus d\'opération ce mois-ci sans être premium.');

            return new JsonResponse(['redirect' => $this->generateUrl('premium')], Response::HTTP_MOVED_PERMANENTLY);
        }
        /** @var Operation $operation */
        $operation = $this->serializer->deserialize($request->getContent(), Operation::class, 'json', [AbstractNormalizer::GROUPS => ['write']]);
        // TODO Service
        $operation->setCreator($this->getUser())->setDepositAccount($this->getUser()->getFavoriteDepositAccount());
        $errors = $this->validator->validate($operation);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->entityManager->persist($operation);
        $this->entityManager->flush();

        return $this->json(data: $operation, context: ['groups' => ['read']]);
    }

    #[Route(path: '/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Operation $operation): JsonResponse
    {
        if (!$this->isGranted(OperationVoter::UPDATE, $operation)) {
            return new JsonResponse(['title' => 'Vous ne pouvez pas modifier cette opération.'], Response::HTTP_FORBIDDEN);
        }
        $operation = $this->serializer->deserialize($request->getContent(), Operation::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $operation]);
        // TODO Validator
        $this->entityManager->persist($operation);
        $this->entityManager->flush();

        return $this->json(data: $operation, context: ['groups' => ['read']]);
    }

    #[Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Operation $operation): JsonResponse
    {
        if (!$this->isGranted(OperationVoter::UPDATE, $operation)) {
            return new JsonResponse(['title' => 'Vous ne pouvez pas supprimer cette opération.'], Response::HTTP_FORBIDDEN);
        }
        // TODO Validator
        $this->entityManager->remove($operation);
        $this->entityManager->flush();

        return $this->json(null);
    }

    private function monthInArray(string $month, array $yearsMonths): bool
    {
        foreach ($yearsMonths as $yearsMonth) {
            if (in_array($month, $yearsMonth, true)) {
                return true;
            }
        }

        return false;
    }
}