<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', 'update'])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(min: 3, max: 30, minMessage: 'Your first name must be at least 3 characters long.', maxMessage: 'Your first name cannot be longer than 30 characters.')]
    #[Assert\NotBlank(message: 'The phone number is required')]
    #[Groups(['read', 'create', 'update'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(min: 3, max: 30, minMessage: 'Your last name must be at least 3 characters long.', maxMessage: 'Your last name cannot be longer than 30 characters.')]
    #[Assert\NotBlank(message: 'The phone number is required')]
    #[Groups(['read', 'create', 'update'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 10)]
    #[Assert\Regex(
        pattern: '/^0[1-9]([-. \/]?\d{2}){4}$/',
        message: 'The phone number is not valid.',
        match: true)]
    #[Assert\NotBlank(message: 'The phone number is required.')]
    #[Groups(['read', 'create', 'update'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'The email is not valid.')]
    #[Assert\NotBlank(message: 'The phone number is required.')]
    #[Groups(['read', 'create', 'update'])]
    private ?string $email = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Groups(['read', 'create', 'update'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['read', 'create', 'update'])]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(['read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s (%s) - %s, %s [%s]',
            $this->firstName,
            $this->lastName,
            $this->phoneNumber,
            $this->email,
            $this->city ?? 'No city',
            $this->postalCode ?? 'No postal code'
        );
    }
}
