<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

trait FixturesTrait
{
    public function loadFixtures(array $fixtures): array
    {
        $databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $data = $databaseTool->loadFixtures($fixtures);

        dd($data);
        return $data;
    }

    public function loadFixtureFiles(array $fixtureFiles): array
    {
        $databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        return $databaseTool->loadAliceFixture($fixtureFiles);
    }
}