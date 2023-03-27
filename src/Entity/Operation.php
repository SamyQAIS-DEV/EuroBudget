<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING,length: 255)]
    #[Groups(['read'])]
    private string $label;

    #[ORM\Column(type: Types::FLOAT)]
    #[Groups(['read'])]
    private float $amount;

    #[ORM\Column(type: Types::STRING, length: 1)]
    #[Groups(['read'])]
    private string $type = '-';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $creator;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private DepositAccount $depositAccount;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['read'])]
    private DateTimeImmutable $date;

    #[ORM\Column]
    #[Groups(['read'])]
    private ?bool $past = null;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function isPast(): ?bool
    {
        return $this->past;
    }

    public function setPast(bool $past): self
    {
        $this->past = $past;

        return $this;
    }
}
