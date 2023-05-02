<?php

namespace App\Tests;

class FakeEntity
{
    public function __construct(private readonly int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
