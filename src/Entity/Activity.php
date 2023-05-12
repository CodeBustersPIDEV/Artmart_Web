<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
#[ORM\Table(name: 'activity', options: [
    'indexes' => [
        'event' => ['columns' => ['eventID']]
    ]
])]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "activityID", type: "integer")]
    private $activityid;

    #[Assert\NotBlank]
    #[ORM\Column(name: "date", type: "datetime",nullable: false)]
    private $date;

    // #[Assert\Callback(callback: 'validateDate')]
    // public function validateDate(ExecutionContextInterface $context): void
    // {
    //     if ($this->date > new \DateTime()) {
    //         $context->buildViolation('The date should not be superior to today\'s date')
    //             ->atPath('date')
    //             ->addViolation();
    //     }
    // }

    // #[Assert\Callback(callback: 'validateDate')]
    // public function validateDate(ExecutionContextInterface $context): void
    // {
    //     if ($this->date < $this->event->getStartDate()) {
    //         $context->buildViolation('The activity date must be after or equal to the event start date.')
    //             ->atPath('date')
    //             ->addViolation();
    //     }
    // }

    #[Assert\NotBlank]
    #[ORM\Column(name: "title", type: "string", length: 255)]
    private $title;

    #[Assert\NotBlank]
    #[ORM\Column(name: "host", type: "string", length: 255)]
    private $host;

    #[ORM\ManyToOne(targetEntity: "Event")]
    #[ORM\JoinColumn(name: "eventID", referencedColumnName: "eventID")]
    #[Assert\NotBlank]
    private $event;


    public function getActivityid(): ?int
    {
        return $this->activityid;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }


}
