<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

trait FixturesTrait
{
    private function loadFixtures(array $files): array
    {
        // TODO
        $databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $data = $databaseTool->loadFixtures($files);

//        dd($data);
        return $data;
    }

    public function loadFixtureFiles(array $files): array
    {
        $databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $fixtureFiles = [];
        foreach ($files as $filename) {
            $fixtureFiles[] = dirname(__DIR__) . '/tests/fixtures/' . $filename . '.yaml';
        }
        return $databaseTool->loadAliceFixture($fixtureFiles);
    }
}