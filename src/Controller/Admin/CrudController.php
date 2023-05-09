<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

abstract class CrudController extends AbstractController
{
    protected string $entity = '';
    protected string $templatePath = '';
    protected string $menuItem = '';
    protected string $routePrefix = '';
    protected string $searchField = 'title';
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function crudIndex(QueryBuilder $query = null): Response
    {
        $query = $query ?: $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');
        $rows = $query->getQuery()->getResult();

        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'rows' => $rows,
//            'searchable' => true,
            'menu' => $this->menuItem,
            'prefix' => $this->routePrefix,
        ]);
    }

    public function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository($this->entity);
    }
}
