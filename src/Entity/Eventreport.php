<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: "eventreport",options: [
    'indexes' => [
        'eventID' => ['columns' => ['eventID']]
    ]
])]
#[ORM\Entity]
class Eventreport
{
    
     #[ORM\Column(name: "reportID", type: "integer", nullable: false)]
     #[ORM\Id]
     #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $reportid;

    #[Assert\NotBlank]
    #[ORM\Column(name: "attendance", type: "integer",nullable: false)]
    private $attendance;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false,options:["default"=>"current_timestamp()"])]
    private $createdAt;


    #[ORM\ManyToOne(targetEntity: "Event")]
    #[ORM\JoinColumn(name: "eventID", referencedColumnName: "eventID")]
    #[Assert\NotBlank]
    private $event;

    public function __construct()
    {
        $this->createdAt= new \DateTime();
    }

public function getReportid(): ?int
    {
        return $this->reportid;
    }

    public function getAttendance(): ?int
    {
        return $this->attendance;
    }

    public function setAttendance(int $attendance): self
    {
        $this->attendance = $attendance;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
