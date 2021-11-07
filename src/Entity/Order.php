<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    public const SHIPPING_COUNTRY_FR = "France";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $contactEmail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $shippingAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $shippingZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $shippingCountry;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected ?int $total = null;

    /**
     * @ORM\OneToMany(targetEntity="OrderLine", mappedBy="order", cascade={"persist", "remove"})
     */
    protected $lines;

    /**
     * @ORM\OneToMany(targetEntity="OrderTag", mappedBy="order", cascade={"persist", "remove"})
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="OrderIssue", mappedBy="order", cascade={"persist", "remove"})
     */
    protected $issues;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->issues = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    public function getShippingAddress(): string
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(string $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }


    public function getShippingZipcode(): string
    {
        return $this->shippingZipcode;
    }

    public function setShippingZipcode(string $shippingZipcode): void
    {
        $this->shippingZipcode = $shippingZipcode;
    }

    public function getShippingCountry(): string
    {
        return $this->shippingCountry;
    }

    public function setShippingCountry(string $shippingCountry): void
    {
        $this->shippingCountry = $shippingCountry;
    }

    /**
     * @return Collection
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @return Collection
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    /**
     * Add tag
     *
     * @param OrderTag $tag
     * @return Order
     */
    public function addTag(OrderTag $tag): self
    {
        if (!$this->getTags()->contains($tag)) {
            $this->getTags()->add($tag);
        }

        return $this;
    }

    /**
     * Add an issue
     *
     * @param OrderIssue $issue
     * @return Order
     */
    public function addIssue(OrderIssue $issue): self
    {
        if (!$this->getIssues()->contains($issue)) {
            $this->getIssues()->add($issue);
        }

        return $this;
    }

    public function getWeight(): int
    {
        $weight = 0;
        $lines = $this->getLines();

        foreach ($lines as $key => $line) {
            $weight += $line->getWeight();
        }

        return $weight;
    }

    public function addHeavyTag(): self
    {
        $this->addTagByValue(OrderTag::TAG_HEAVY);

        return $this;
    }

    public function addForeignWarehouseTag(): self
    {
        $this->addTagByValue(OrderTag::TAG_FOREIGN_WAREHOUSE);

        return $this;
    }

    public function addHasIssuesTag(): self
    {
        $this->addTagByValue(OrderTag::TAG_HAS_ISSUES);

        return $this;
    }

    protected function addTagByValue(string $tagValue): self
    {
        $tag = new OrderTag();
        $tag->setOrder($this);
        $tag->setTag($tagValue);
        $this->addTag($tag);

        return $this;
    }

    public function addEmptyEmailIssue(): self
    {
        $this->addIssueByValue(OrderIssue::ISSUE_EMPTY_EMAIL);

        return $this;
    }

    public function addExceeds60kgIssue(): self
    {
        $this->addIssueByValue(OrderIssue::ISSUE_EXCEEDS_60KG);

        return $this;
    }

    public function addInvalidFrenchAddressIssue(): self
    {
        $this->addIssueByValue(OrderIssue::ISSUE_INVALID_FRENCH_ADDRESS);

        return $this;
    }

    protected function addIssueByValue(string $issueValue): self
    {
        $issue = new OrderIssue();
        $issue->setOrder($this);
        $issue->setIssue($issueValue);
        $this->addIssue($issue);

        return $this;
    }
}
