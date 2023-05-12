<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Salesreport
 *
 * @ORM\Table(name="salesreport", indexes={@ORM\Index(name="ProductID", columns={"ProductID"})})
 * @ORM\Entity
 */
class Salesreport
{
    /**
     * @var int
     *
     * @ORM\Column(name="salesReport_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $salesreportId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ProductID", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $productid = NULL;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TotalSales", type="decimal", precision=10, scale=2, nullable=true, options={"default"="NULL"})
     */
    private $totalsales = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="AverageSalesPerDay", type="decimal", precision=10, scale=2, nullable=true, options={"default"="NULL"})
     */
    private $averagesalesperday = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="Date", type="date", nullable=true, options={"default"="NULL"})
     */
    private $date = 'NULL';

    public function getSalesreportId(): ?int
    {
        return $this->salesreportId;
    }

    public function getProductid(): ?int
    {
        return $this->productid;
    }

    public function setProductid(?int $productid): self
    {
        $this->productid = $productid;

        return $this;
    }

    public function getTotalsales(): ?string
    {
        return $this->totalsales;
    }

    public function setTotalsales(?string $totalsales): self
    {
        $this->totalsales = $totalsales;

        return $this;
    }

    public function getAveragesalesperday(): ?string
    {
        return $this->averagesalesperday;
    }

    public function setAveragesalesperday(?string $averagesalesperday): self
    {
        $this->averagesalesperday = $averagesalesperday;

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


}
