<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="client", indexes={@ORM\Index(name="user_ID", columns={"user_ID"})})
 * @ORM\Entity
 */
class Client
{
    /**
     * @var int
     *
     * @ORM\Column(name="client_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     */
    private $nbrDemands;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_ID", referencedColumnName="user_ID")
     * })
     */
    private $user;

    public function getClientId(): ?int
    {
        return $this->clientId;
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
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
