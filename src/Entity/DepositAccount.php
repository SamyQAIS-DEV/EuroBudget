<?php

namespace App\Entity;

use App\Repository\DepositAccountRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepositAccountRepository::class)]
class DepositAccount
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Length(min: 3, max: 50)]
    #[Assert\NotBlank]
    #[Groups(['read', 'write'])]
    private string $title;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\PositiveOrZero]
    #[Assert\NotNull]
    #[Groups(['read', 'write'])]
    private float $amount = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $creator;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(type: Types::STRING, length: 7)]
    #[Assert\CssColor]
    #[Groups(['read', 'write'])]
    private string $color = '#287bff';

    #[ORM\OneToMany(mappedBy: 'depositAccount', targetEntity: Category::class)]
    private Collection $categories;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->updatedAt = new DateTimeImmutable();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setCreator(User $creator): self
    {
        $this->creator = $creator;
        $this->addUser($creator);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

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

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setDepositAccount($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getDepositAccount() === $this) {
                $category->setDepositAccount(null);
            }
        }

        return $this;
    }
}
