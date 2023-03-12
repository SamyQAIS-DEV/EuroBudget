<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

trait FixturesTrait
{
    public function loadFixtures(array $fixtures): void
    {
        $databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures($fixtures);
    }

    public function loadFixtureFiles(array $fixtureFiles): void
    {
        $databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadAliceFixture($fixtureFiles);
    }
}