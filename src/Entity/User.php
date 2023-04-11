<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'user_ID', type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: false, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(message: "The email is not a valid email")]
    private string $email;

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotBlank]
    private \DateTime $birthday;

    #[Assert\Callback(callback: 'validateBirthday')]
    public function validateBirthday(ExecutionContextInterface $context): void
    {
        if ($this->birthday > new \DateTime()) {
            $context->buildViolation('The birthday date should not be superior to today\'s date')
                ->atPath('birthday')
                ->addViolation();
        }
    }

    #[ORM\Column(name: 'phoneNumber', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank]
    private string $phonenumber;

    #[ORM\Column(type: 'string', length: 30, nullable: false)]
    #[Assert\NotBlank]
    private string $role;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank]
    private string $username;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    #[Regex(pattern: "/^(?=.*[A-Z])(?=.*\d).{8,}$/")]
    private string $password;

    
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function validatePassword()
    {
        $regex = "/^(?=.*[A-Z])(?=.*\d).{8,}$/";
        if (!preg_match($regex, $this->password)) {
            throw new \RuntimeException("Invalid password format.");
        }
    }

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['default' => 'NULL'])]
    private ?string $picture = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['default' => 'NULL'])]
    private ?string $token = null;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => 'NULL'])]
    private ?bool $blocked = null;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => 'NULL'])]
    private ?bool $enabled = null;

    #[ORM\Column(name: 'dateOfCreation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $dateofcreation;


    public function __construct()
    {
        $this->dateofcreation = new \DateTime();
    }


    

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPhonenumber(): ?string
    {
        return $this->phonenumber;
    }

    public function setPhonenumber(string $phonenumber): self
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function isBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function setBlocked(?bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getDateofcreation(): ?\DateTimeInterface
    {
        return $this->dateofcreation;
    }

    public function setDateofcreation(?\DateTimeInterface $dateofcreation): self
    {
        $this->dateofcreation = $dateofcreation;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }


    public function __toString(): string
    {
        return $this->getName();
    }
}
