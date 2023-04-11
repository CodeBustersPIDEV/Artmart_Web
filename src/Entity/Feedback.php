<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "feedback",options: [
    'indexes' => [
        'eventID' => ['columns' => ['eventID']],
        'userID' => ['columns' => ['userID']]
    ]
])]
#[ORM\Entity]
class Feedback
{
    #[ORM\Column(name: "feedbackID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $feedbackid;

    #[ORM\Column(name: "rating", type: "integer", nullable: false)]
    private $rating;

    #[ORM\Column(name: "comment", type: "text",  length:65535,nullable: false)]
    private $comment;

    #[ORM\Column(name: "date", type: "datetime", nullable: true,options:["default"=>"current_timestamp()"])]
    private $date = 'current_timestamp()';

    #[ORM\ManyToOne(targetEntity: "Event")]
    #[ORM\JoinColumn(name: "eventID", referencedColumnName: "eventID")]
    private $eventid;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "userID", referencedColumnName: "user_ID")]
    private $userid;

    public function getFeedbackid(): ?int
    {
        return $this->feedbackid;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

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
