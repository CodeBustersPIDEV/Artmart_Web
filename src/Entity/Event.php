<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Table(name: "event",options: [
    'indexes' => [
        'user' => ['columns' => ['userID']]
    ]
])]
#[ORM\Entity]
class Event
{
    #[ORM\Column(name: "eventID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $eventid;

    #[Assert\NotBlank]
    #[ORM\Column(name: "name", type: "string",length:255,nullable: false)]
    private $name;

    #[Assert\NotBlank]
    #[ORM\Column(name: "location", type: "string",length:255,nullable: false)]
    private $location;

    #[Assert\NotBlank]
    #[ORM\Column(name: "type", type: "string",length:255,nullable: false)]
    private $type;

    #[Assert\NotBlank]
    #[ORM\Column(name: "description", type: "text",length:65535,nullable: false)]
    private $description;

    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(name: "entryFee", type: "float",precision:10, scale:0, nullable: false)]
    private $entryfee;

    #[Assert\NotNull]
    #[Assert\Positive]
    #[ORM\Column(name: "capacity", type: "integer",nullable: false)]
    private $capacity;

    #[ORM\Column(name: "startDate", type: "datetime",nullable: false)]
    #[Assert\NotBlank]
    private $startdate;

    #[ORM\Column(name: "endDate", type: "datetime",nullable: false)]
    #[Assert\NotBlank]
    private $enddate;

    #[Assert\Callback(callback: 'validateDate')]
    public function validateDate(ExecutionContextInterface $context): void
    {
        if ($this->startdate > $this->enddate) {
            $context->buildViolation('The start date should not be superior to the end date')
            ->atPath('startdate')
            ->addViolation();
        }
    }

    #[ORM\Column(name: "image", type: "string",length:255,nullable: false,options:["default"=>"NULL"])]
    private $image = 'NULL';

    #[ORM\Column(name: "status", type: "string",length:255,nullable: false,options:["default"=>"Scheduled"])]
    private $status = '\'Scheduled\'';

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "userID", referencedColumnName: "user_ID")]
    #[Assert\NotBlank]
    private $user;

    public function getEventid(): ?int
    {
        return $this->eventid;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEntryfee(): ?float
    {
        return $this->entryfee;
    }

    public function setEntryfee(float $entryfee): self
    {
        $this->entryfee = $entryfee;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getStartdate(): ?\DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(\DateTimeInterface $startdate): self
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getEnddate(): ?\DateTimeInterface
    {
        return $this->enddate;
    }

    public function setEnddate(\DateTimeInterface $enddate): self
    {
        $this->enddate = $enddate;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
