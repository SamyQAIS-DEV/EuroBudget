<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryService
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function create(Category $category, User $user): Category
    {
        $category->setOwner($user);
        $category->setDepositAccount($user->getFavoriteDepositAccount());
        $category->setSlug($this->slugger->slug(strtolower($category->getName())));
        $this->categoryRepository->save($category, true);

        return $category;
    }

    public function update(Category $category): Category
    {
        $category->setSlug($this->slugger->slug(strtolower($category->getName())));
        $this->categoryRepository->save($category, true);

        return $category;
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->remove($category, true);
    }
}