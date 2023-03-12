<?php

namespace Tests\Helpers;

class FixtureData
{
    public static function fromFixtureFile(string $fixtureFilepath, array $placeholdersData): mixed
    {
        $content = file_get_contents($fixtureFilepath);

        return static::replacePlaceholders($content, $placeholdersData);
    }

    protected static function replacePlaceholders(string $fixture, array $placeholdersData): mixed
    {
        foreach ($placeholdersData as $key => $value) {
            $fixture = str_replace('<'.$key.'>', $value, $fixture);
        }

        return json_decode($fixture);
    }
}
