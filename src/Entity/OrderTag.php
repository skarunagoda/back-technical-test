<?php

namespace App\Entity;

use App\Repository\OrderLineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=OrderLineRepository::class)
 * @UniqueEntity("tag")
 */
class OrderTag
{
    const TAG_HEAVY = "heavy";
    const TAG_FOREIGN_WAREHOUSE = "ForeignWarehouse";
    const TAG_HAS_ISSUES = "hasIssues";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $tag;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="tags")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order): self
    {
        $this->order = $order;

        return $this;
    }
}
