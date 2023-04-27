<?php

namespace App\Entity;

interface CategorizableInterface
{
    public function getDepositAccount(): DepositAccount;

    public function getCategory(): ?Category;
}