<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/users', name: 'admin_user_')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class UserController extends CrudController
{
    protected string $templatePath = 'user';
    protected string $menuItem = 'user';
    protected string $entity = User::class;
    protected string $routePrefix = 'admin_user';
    protected string $searchField = 'username';
    protected array $events = [];

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/search', name: 'autocomplete')]
    public function search(Request $request): JsonResponse
    {
        return new JsonResponse([]);
        // TODO
//        /** @var UserRepository $repository */
//        $repository = $this->getRepository();
//        $q = strtolower($request->query->get('q') ?: '');
//        if ('moi' === $q) {
//            return new JsonResponse([[
//                'id' => $this->getUser()->getId(),
//                'username' => $this->getUser()->getUsername(),
//            ]]);
//        }
//        $users = $repository
//            ->createQueryBuilder('u')
//            ->select('u.id', 'u.username')
//            ->where('LOWER(u.username) LIKE :username')
//            ->setParameter('username', "%$q%")
//            ->setMaxResults(25)
//            ->getQuery()
//            ->getResult();
//
//        return new JsonResponse($users);
    }
}
