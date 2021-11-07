<?php

namespace App\Entity;

use App\Repository\OrderLineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderLineRepository::class)
 */
class OrderIssue
{
    const ISSUE_EMPTY_EMAIL = "La commande a un email de contact vide.";
    const ISSUE_EXCEEDS_60KG = "La commande fait plus de 60kg.";
    const ISSUE_INVALID_FRENCH_ADDRESS = "La commande est livrée en France mais n'a pas une adresse française valide.";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $issue;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="issues")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIssue(): ?string
    {
        return $this->issue;
    }

    public function setIssue(string $issue): self
    {
        $this->issue = $issue;

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
