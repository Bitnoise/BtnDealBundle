<?php

namespace Btn\DealBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Btn\DealBundle\Entity\Deal
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="deal")
 * @ORM\Entity(repositoryClass="Btn\DealBundle\Repository\DealRepository")
 */
class Deal
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
     * @var text $buyer
     *
     * @ORM\Column(name="buyer", type="text", nullable=true)
     */
    private $buyer;

    /**
     * @var text $seller
     *
     * @ORM\Column(name="seller", type="text", nullable=true)
     */
    private $seller;

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
     * @var decimal $taxValue
     *
     * @ORM\Column(name="tax_value", type="decimal", nullable=true)
     */
    private $taxValue;

    /**
     * @var integer $number
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var datetime $issueDate
     *
     * @ORM\Column(name="issue_date", type="datetime", nullable=true)
     */
    private $issueDate;

    /**
     * @var datetime $dueDate
     *
     * @ORM\Column(name="due_date", type="datetime", nullable=true)
     */
    private $dueDate;

    /**
     * @ORM\OneToMany(targetEntity="Btn\DealBundle\Entity\DealItem", mappedBy="deal", cascade={"persist"})
     **/
    private $items;

    /**
     * @var string $file
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @var integer $ownerId
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=true)
     */
    private $ownerId;

    /**
     * @var string $dealNb
     *
     * @ORM\Column(name="deal_nb", type="string", length=255, nullable=true)
     */
    private $dealNb;

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
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set buyer
     *
     * @param text $buyer
     * @return Invoice
     */
    public function setBuyer($buyer)
    {
        $this->buyer = json_encode($buyer);
        return $this;
    }

    /**
     * Get buyer
     *
     * @return text
     */
    public function getBuyer()
    {
        return json_decode($this->buyer, true);
    }

    /**
     * Set seller
     *
     * @param text $seller
     * @return Invoice
     */
    public function setSeller($seller)
    {
        $this->seller = json_encode($seller);
        return $this;
    }

    /**
     * Get seller
     *
     * @return text
     */
    public function getSeller()
    {
        return json_decode($this->seller, true);
    }

    /**
     * Set netto
     *
     * @param decimal $netto
     * @return Invoice
     */
    public function setNetto($netto)
    {
        $this->netto = $netto;
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
     * Set gross
     *
     * @param decimal $gross
     * @return Invoice
     */
    public function setGross($gross)
    {
        $this->gross = $gross;
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
     * Set tax_value
     *
     * @param decimal $taxValue
     * @return Invoice
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
     * Set number
     *
     * @param integer $number
     * @return Invoice
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     * @return Invoice
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add items
     *
     * @param Btn\DealBundle\Entity\DealItem $items
     * @return Invoice
     */
    public function addItem(\Btn\DealBundle\Entity\DealItem $items)
    {
        $this->items[] = $items;
        return $this;
    }

    /**
     * Remove items
     *
     * @param Btn\DealBundle\Entity\DealItem $items
     */
    public function removeItem(\Btn\DealBundle\Entity\DealItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * update gross, netto, and tax value from items
     *
     * @return void
     * @author
     **/
    public function update()
    {
        $this->taxValue = 0;
        $this->netto     = 0;
        $this->gross     = 0;

        //for each item get values * quantity and set to invoice
        foreach ($this->items as $item) {
            $this->taxValue += $item->getQuantity() * $item->getTaxValue();
            $this->netto     += $item->getQuantity() * $item->getNetto();
            $this->gross     += $item->getQuantity() * $item->getGross();
        }

    }

    /**
     * Set file
     *
     * @param string $file
     * @return Invoice
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
    /**
     * return path to download file
     *
     *  @return $string
     *
     */
    public function getFilePath()
    {
        return $this->getDestinationDir() . $this->file;
    }

    /**
     * destination dir
     *
     * @return string
     * @author
     **/
    public function getDestinationDir()
    {
        $path = __DIR__.'/../../../../web/uploads/invoices/';
        //check directory, and created if not exist
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }

        return 'uploads/invoices/';
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     * @return Invoice
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    /**
     * Get ownerId
     *
     * @return integer
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * Set dueDate
     *
     * @param datetime $dueDate
     * @return Invoice
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    /**
     * Get dueDate
     *
     * @return datetime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
    * @ORM\PrePersist
    **/
    public function doPrePersist()
    {
        if ($this->createdAt == null) {
            $this->createdAt = new \DateTime();
        }

        if ($this->dueDate == null) {
            $this->dueDate = new \DateTime("+14 days");
        }

        if ($this->issueDate == null) {
            $this->issueDate = new \DateTime();
        }
    }

    /**
     * get day from $issueDate
     *
     * @return integer
     **/
    public function getDay($type = 'd')
    {
        return $this->getIssueDate()->format($type);
    }

    /**
     * get month from $issueDate
     *
     * @return integer
     **/
    public function getMonth($type = 'm')
    {
        return $this->getIssueDate()->format($type);
    }

    /**
     * get year from $issueDate
     *
     * @return integer
     **/
    public function getYear($type = 'Y')
    {
        return $this->getIssueDate()->format($type);
    }

    /**
     * Set issueDate
     *
     * @param datetime $issueDate
     * @return Invoice
     */
    public function setIssueDate($issueDate)
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    /**
     * Get issueDate
     *
     * @return datetime
     */
    public function getIssueDate()
    {
        return $this->issueDate;
    }

    /**
     * return number in format {number}/{month}/{year}
     *
     * @return string
     * @author
     **/
    public function getNumberInFormat($format = '%d/%m/%f')
    {
        //@TODO: implement sprintf here
        return $this->getNumber() . '/' . $this->getMonth() . '/' . $this->getYear();
    }

    /**
     * Set dealNb
     *
     * @param string $dealNb
     * @return Deal
     */
    public function setDealNb($dealNb)
    {
        $this->dealNb = $dealNb;

        return $this;
    }

    /**
     * Get dealNb
     *
     * @return string
     */
    public function getDealNb()
    {
        return $this->dealNb;
    }

    /**
     * Set options
     *
     * @param string $options
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
     * @param integer $type
     * @return Deal
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

    /**
     * Set status
     *
     * @param integer $status
     * @return Deal
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
}