<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eventreport
 *
 * @ORM\Table(name="eventreport", indexes={@ORM\Index(name="eventID", columns={"eventID"})})
 * @ORM\Entity
 */
class Eventreport
{
    /**
     * @var int
     *
     * @ORM\Column(name="reportID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $reportid;

    /**
     * @var int
     *
     * @ORM\Column(name="attendance", type="integer", nullable=false)
     */
    private $attendance;

    /**
     * @var \Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="eventID", referencedColumnName="eventID")
     * })
     */
    private $eventid;

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

    public function getEventid(): ?Event
    {
        return $this->eventid;
    }

    public function setEventid(?Event $eventid): self
    {
        $this->eventid = $eventid;

        return $this;
    }


}
