<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SocialLoggableTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $githubId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $discordId = null;

    public function getGithubId(): ?string
    {
        return $this->githubId;
    }

    public function setGithubId(?string $githubId): self
    {
        $this->githubId = $githubId;

        return $this;
    }

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(?string $discordId): self
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function useOauth(): bool
    {
        return $this->githubId !== null || $this->discordId !== null;
    }
}
