<?php

namespace App\Tests;

/**
 * @template T
 */
class RepositoryTestCase extends WebTestCase
{
    /**
     * @var T
     */
    protected $repository = null;

    /**
     * @var class-string<T>
     */
    protected $repositoryClass = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get($this->repositoryClass);
    }
}
