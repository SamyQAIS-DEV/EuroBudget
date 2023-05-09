<?php

namespace App\Controller\Admin;

interface CrudDataInterface
{
    public function getEntity(): object;

    public function getFormClass(): string;

    public function hydrate(): void;
}
