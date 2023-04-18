<?php

namespace App\Entity;

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

    #[ORM\Column(name: "attendance", type: "integer",nullable: false)]
    private $attendance;

    #[ORM\ManyToOne(targetEntity: "Event")]
    #[ORM\JoinColumn(name: "eventID", referencedColumnName: "eventID")]
    private $event;

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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEventid(?Event $eventid): self
    {
        $this->event = $eventid;

        return $this;
    }


}
