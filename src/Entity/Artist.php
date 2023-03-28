<?php

namespace App\Entity;

use App\Repository\ArtistRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * Artist
 *
 * @ORM\Table(name="artist", indexes={@ORM\Index(name="user_ID", columns={"user_ID"})})
 * @ORM\Entity
 */
/**
 * @ORM\Entity(repositoryClass=ArtistRepository::class)
 */
class Artist
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="artist_ID", type="integer")
     */
    private $artistId;

    /**
     * @var int
     *
     * @ORM\Column(name="nbr_artwork", type="integer", nullable=false)
     */
    private $nbrArtwork;
/**
     * @var int
     *
     * @ORM\Column(name="user_ID", type="integer", nullable=false)
     */
    private $userId;
    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $bio = 'NULL';

    /**
     * @var \User
     *
     *@ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_ID", referencedColumnName="user_ID")
     * })
     * 
     * 
     */
    private $user;

    public function getArtistId(): ?int
    {
        return $this->artistId;
    }
    public function getNbrArtwork(): ?int
    {
        return $this->nbrArtwork;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setNbrArtwork(int $nbrArtwork): self
    {
        $this->nbrArtwork = $nbrArtwork;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user ?? null;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
