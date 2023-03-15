<?php

namespace App\Entity;

use App\Repository\LoginLinkTokenRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginLinkTokenRepository::class)]
class LoginLinkToken
{
    final public const EXPIRE_IN = 30;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $token;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;

    public function isExpired(): bool
    {
        $expirationDate = new DateTime('-' . self::EXPIRE_IN . ' minutes');

        return $this->getCreatedAt() < $expirationDate;
    }

    public function getExpiresAt(): DateTime
    {
        $expirationDate = clone $this->createdAt;
        $expirationDate->modify('+' . self::EXPIRE_IN . ' minutes');

        return $expirationDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
