<?php

namespace App\Normalizer;

use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordNormalizer extends Normalizer
{
    /**
     * @param DiscordResourceOwner $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'email' => $object->getEmail(),
            'github_id' => $object->getId(),
            'type' => 'Discord',
            'username' => $object->getUsername(),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof DiscordResourceOwner;
    }
}
