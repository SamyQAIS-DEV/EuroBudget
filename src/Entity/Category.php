<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Length(min: 3, max: 180)]
    #[Groups(['read'])]
    private string $name;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['read'])]
    private string $slug;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private DepositAccount $depositAccount;

    #[ORM\Column(length: 7)]
    #[Groups(['read'])]
    private string $color = '#4182f5';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDepositAccount(): DepositAccount
    {
        return $this->depositAccount;
    }

    public function setDepositAccount(DepositAccount $depositAccount): self
    {
        $this->depositAccount = $depositAccount;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
