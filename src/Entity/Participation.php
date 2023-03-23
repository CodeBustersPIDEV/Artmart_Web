<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Participation
 *
 * @ORM\Table(name="participation", uniqueConstraints={@ORM\UniqueConstraint(name="eventID", columns={"eventID", "userID"})}, indexes={@ORM\Index(name="userID", columns={"userID"}), @ORM\Index(name="IDX_AB55E24F10409BA4", columns={"eventID"})})
 * @ORM\Entity
 */
class Participation
{
    /**
     * @var int
     *
     * @ORM\Column(name="participationID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $participationid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="attendanceStatus", type="string", length=255, nullable=true, options={"default"="'Not attending'"})
     */
    private $attendancestatus = '\'Not attending\'';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="registrationDate", type="datetime", nullable=true, options={"default"="current_timestamp()"})
     */
    private $registrationdate = 'current_timestamp()';

    /**
     * @var \Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="eventID", referencedColumnName="eventID")
     * })
     */
    private $eventid;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="user_ID")
     * })
     */
    private $userid;

    public function getParticipationid(): ?int
    {
        return $this->participationid;
    }

    public function getAttendancestatus(): ?string
    {
        return $this->attendancestatus;
    }

    public function setAttendancestatus(?string $attendancestatus): self
    {
        $this->attendancestatus = $attendancestatus;

        return $this;
    }

    public function getRegistrationdate(): ?\DateTimeInterface
    {
        return $this->registrationdate;
    }

    public function setRegistrationdate(?\DateTimeInterface $registrationdate): self
    {
        $this->registrationdate = $registrationdate;

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

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(?User $userid): self
    {
        $this->userid = $userid;

        return $this;
    }


}
