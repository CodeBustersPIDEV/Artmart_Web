<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="client", indexes={@ORM\Index(name="user_ID", columns={"user_ID"})})
 * @ORM\Entity
 */
/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client 
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="client_ID", type="integer")
     */
    private $clientId;

    /**
     * @var int
     *
     * @ORM\Column(name="nbr_orders", type="integer", nullable=false)
     */
    private $nbrOrders;

  
    /**
     * @var int
     *
     * @ORM\Column(name="nbr_demands", type="integer", nullable=false)
     *
     */
    private $nbrDemands;

    /**
     * @var \User
     *
     * 
     *  @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_ID", referencedColumnName="user_ID")
     * })
     */
    private $user;

    public function getClientId(): ?int
    {
        return $this->clientId;
    }
    public function setClientId(int $ClientId): self
    {
        $this->clientId = $ClientId;

        return $this;
    }
    public function getUserId(): ?int
    {
        return $this->user->getUserId;
    }
    public function setUserId(int $UserId): self
    {
        $this->user->getUserId = $UserId;

        return $this;
    }

    public function getNbrOrders(): ?int
    {
        return $this->nbrOrders;
    }

    public function setNbrOrders(int $nbrOrders): self
    {
        $this->nbrOrders = $nbrOrders;

        return $this;
    }

    public function getNbrDemands(): ?int
    {
        return $this->nbrDemands;
    }

    public function setNbrDemands(int $nbrDemands): self
    {
        $this->nbrDemands = $nbrDemands;

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
