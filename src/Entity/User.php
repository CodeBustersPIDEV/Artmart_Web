<?php

namespace App\Entity;

use App\Twig\HtmlExtension;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email','username'], message: "There is already an account with this username or email")]
class User 

{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'user_ID', type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message:"This field must not be empty")]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: false, unique: true)]
    #[Assert\NotBlank(message:"This field must not be empty")]
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
    #[Assert\NotBlank(message:"This field must not be empty")]
    private string $phonenumber;

    #[ORM\Column(type: 'string', length: 30, nullable: false)]
    #[Assert\NotBlank]
    private string $role;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message:"This field must not be empty")]
    private string $username;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message:"This field must not be empty")]
    #[Assert\Length(min: 8)]
    #[Regex(pattern: "/^(?=.*[A-Z])(?=.*\d).{8,}$/", message:"Must contain at least one Uppercase and One special caracter or number and must be of minimum length of 8 characters.")]
    private string $password;

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

    #[ORM\Column(name: 'LastLogin', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $LastLogin;



    public function __construct()
    {
        $this->dateofcreation = new \DateTime();
        $this->birthday = new \DateTime();
        $this->LastLogin = new \DateTime();

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
    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->LastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $LastLogin): self
    {
        $this->LastLogin = $LastLogin;

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

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
 
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
