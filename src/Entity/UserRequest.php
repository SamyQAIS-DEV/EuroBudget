<?php

namespace App\Entity;

use App\Repository\UserRequestRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRequestRepository::class)]
class UserRequest
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    // TODO Relations Annotations
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private User $creator;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private User $target;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\Type(Types::BOOLEAN, message: 'The value {{ value }} is not a valid {{ type }}.')]
    private bool $accepted = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\Type(Types::BOOLEAN, message: 'The value {{ value }} is not a valid {{ type }}.')]
    private bool $rejected = false;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $entity = null;

    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable();
        $this->createdAt = new DateTimeImmutable();
    }

    public function isAnswered(): bool
    {
        return $this->accepted !== false || $this->rejected !== false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setCreator(User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getTarget(): User
    {
        return $this->target;
    }

    public function setTarget(User $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function isRejected(): bool
    {
        return $this->rejected;
    }

    public function setRejected(bool $rejected): self
    {
        $this->rejected = $rejected;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}
