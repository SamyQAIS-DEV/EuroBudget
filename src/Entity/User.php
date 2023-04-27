<?php

namespace App\Entity;

use App\Attribute\Encrypted;
use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'])]
class User implements UserInterface
{
    use PremiumTrait;
    use SocialLoggableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 100)]
    #[Assert\Email]
    #[Encrypted]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    private string $lastname;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    private string $firstname;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $emailVerified = false;

    #[Vich\UploadableField(mapping: "avatars", fileNameProperty: "avatarName")]
    private ?File $avatarFile = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $avatarName = null;

    #[ORM\Column(type: Types::STRING, length: 2)]
    private string $country = 'fr';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private string $lastLoginIp;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private DateTimeImmutable $lastLoginAt;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private DepositAccount $favoriteDepositAccount;

    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function isEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(bool $emailVerified): self
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarFile(?File $avatarFile): self
    {
        $this->avatarFile = $avatarFile;

        return $this;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(?string $avatarName): self
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLastLoginIp(): string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(string $lastLoginIp): self
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    public function getLastLoginAt(): DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(DateTimeImmutable $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->email,
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->id,
            $this->email,
        ] = $data;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword()
    {
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFavoriteDepositAccount(): DepositAccount
    {
        return $this->favoriteDepositAccount;
    }

    public function setFavoriteDepositAccount(DepositAccount $favoriteDepositAccount): self
    {
        $this->favoriteDepositAccount = $favoriteDepositAccount;
        if (!$favoriteDepositAccount->getUsers()->contains($this)) {
            $favoriteDepositAccount->addUser($this);
        }

        return $this;
    }
}
