<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use App\Validator\CategoryAccess;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice implements CategorizableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING,length: 255)]
    #[Assert\Length(min: 3, max: 50)]
    #[Assert\NotBlank]
    private string $label;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\Positive]
    #[Assert\NotNull]
    private float $amount;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\Type(Types::BOOLEAN, message: 'The value {{ value }} is not a valid {{ type }}.')]
    private bool $active = true;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $creator;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private DepositAccount $depositAccount;

    #[ORM\ManyToOne]
    #[CategoryAccess]
    private ?Category $category = null;

    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
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

    public function getDepositAccount(): DepositAccount
    {
        return $this->depositAccount;
    }

    public function setDepositAccount(DepositAccount $depositAccount): self
    {
        $this->depositAccount = $depositAccount;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
