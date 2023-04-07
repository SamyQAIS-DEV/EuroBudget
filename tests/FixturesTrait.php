<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

trait FixturesTrait
{
    public function loadFixtures(array $files): array
    {
        $databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $fixtureFiles = [];
        foreach ($files as $filename) {
            $fixtureFiles[] = dirname(__DIR__) . '/tests/fixtures/' . $filename . '.yaml';
        }
        return $databaseTool->loadAliceFixture($fixtureFiles);
    }
}