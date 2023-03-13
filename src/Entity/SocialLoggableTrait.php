<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SocialLoggableTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $githubId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $discordId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $facebookId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $linkedinId = null;



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

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getLinkedinId(): ?string
    {
        return $this->linkedinId;
    }

    public function setLinkedinId(?string $linkedinId): self
    {
        $this->linkedinId = $linkedinId;

        return $this;
    }

    public function useOauth(): bool
    {
        return $this->githubId !== null || $this->discordId !== null || $this->facebookId !== null || $this->linkedinId !== null;
    }
}
