<?php

namespace Btn\DealBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Btn\DealBundle\Entity\DealItem
 *
 * @ORM\Table(name="deal_item")
 * @ORM\Entity
 */
class DealItem
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var integer $quantity
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var decimal $netto
     *
     * @ORM\Column(name="netto", type="decimal", nullable=true)
     */
    private $netto;

    /**
     * @var decimal $gross
     *
     * @ORM\Column(name="gross", type="decimal", nullable=true)
     */
    private $gross;

    /**
     * @var integer $tax
     *
     * @ORM\Column(name="tax", type="integer", nullable=true)
     */
    private $tax;

    /**
     * @var decimal $taxValue
     *
     * @ORM\Column(name="tax_value", type="decimal", nullable=true)
     */
    private $taxValue;

    /**
     * @var string $sku
     *
     * @ORM\Column(name="sku", type="string", length=255, nullable=true)
     */
    private $sku;

    /**
     * @var Invoice $deal
     * @ORM\ManyToOne(targetEntity="Btn\DealBundle\Entity\Deal", inversedBy="items")
     * @ORM\JoinColumn(name="deal_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $deal;

    /**
     * @var text $options
     *
     * @ORM\Column(name="options", type="text", nullable=true)
     */
    private $options;

    /**
     * @var integer $type
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string      $name
     * @return InvoiceItem
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set quantity
     *
     * @param  integer     $quantity
     * @return InvoiceItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set netto
     *
     * @param  decimal     $netto
     * @return InvoiceItem
     */
    public function setNetto($netto)
    {
        $this->netto = $netto;

        //if haven't tax set defaults
        if ($this->getTax() === null) {
            $this->tax      = 0;
            $this->taxValue = 0;
            $this->gross    = $netto;

        //else if we have tax
        } else {
            $this->gross    = $this->calculateGross();
            $this->taxValue = $this->calculateTaxValue();
        }

        return $this;
    }

    /**
     * Get netto
     *
     * @return decimal
     */
    public function getNetto()
    {
        return $this->netto;
    }

    /**
     * Calculate gross
     *
     * @return decimal
     * @author
     **/
    public function calculateGross()
    {
        return $this->netto * (1 + $this->getTax() / 100);
    }

    /**
     * Calculate netto
     *
     * @return decimal
     * @author
     **/
    public function calculateNetto()
    {
        return $this->gross / (1 + $this->getTax() / 100);
    }

    /**
     * Calculate tax value
     *
     * @return decimal
     * @author
     **/
    public function calculateTaxValue()
    {
        if ($this->getTax()) {
            return $this->netto * ($this->getTax() / 100);
        }

        return 0;
    }

    /**
     * Set gross
     *
     * @param  decimal     $gross
     * @return InvoiceItem
     */
    public function setGross($gross)
    {
        $this->gross = $gross;

        //if haven't tax set defaults
        if ($this->getTax() === null) {
            $this->tax      = 0;
            $this->taxValue = 0;
            $this->netto    = $gross;

        //else if have tax
        } elseif ($this->getTax()) {
            $this->netto     = $this->calculateNetto();
            $this->taxValue = $this->calculateTaxValue();
        }

        return $this;
    }

    /**
     * Get gross
     *
     * @return decimal
     */
    public function getGross()
    {
        return $this->gross;
    }

    /**
     * Set tax
     *
     * @param  integer     $tax
     * @return InvoiceItem
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        //if havent gross and havent netto set defaults
        if ($this->getGross() === null && $this->getNetto() === null) {
            $this->gross = 0;
            $this->netto = 0;

        //if have gross but havent netto calculate netto
        } elseif ($this->getGross() && !$this->getNetto()) {
            $this->netto = $this->calculateNetto();

        //if have netto and havent gross OR have netto and gross calculate gross value
        } elseif ((!$this->getGross() && $this->getNetto()) || ($this->getGross() && $this->getNetto())) {
            $this->gross = $this->calculateGross();
        }

        //set value of tax
        $this->tax_value = $this->calculateTaxValue();

        return $this;
    }

    /**
     * Get tax
     *
     * @return integer
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set tax_value
     *
     * @param  decimal     $taxValue
     * @return InvoiceItem
     */
    public function setTaxValue($taxValue)
    {
        $this->taxValue = $taxValue;

        return $this;
    }

    /**
     * Get tax_value
     *
     * @return decimal
     */
    public function getTaxValue()
    {
        return $this->taxValue;
    }

    /**
     * Set sku
     *
     * @param  string      $sku
     * @return InvoiceItem
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set deal
     *
     * @param  Btn\DealBundle\Entity\Deal $deal
     * @return Deal
     */
    public function setDeal(\Btn\DealBundle\Entity\Deal $deal = null)
    {
        $this->deal = $deal;

        return $this;
    }

    /**
     * Get deal
     *
     * @return Btn\DealBundle\Entity\Deal
     */
    public function getDeal()
    {
        return $this->deal;
    }

    /**
     * Set options
     *
     * @param  string $options
     * @return Deal
     */
    public function setOptions($options)
    {
        $this->options = json_encode($options);

        return $this;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return json_decode($this->options, true);
    }

    /**
     * Set type
     *
     * @param  integer  $type
     * @return DealItem
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }
}
