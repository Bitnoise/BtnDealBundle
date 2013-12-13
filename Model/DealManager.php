<?php

namespace Btn\DealBundle\Model;

use Btn\DealBundle\Entity\Deal;
use Btn\DealBundle\Entity\DealItem;
use Doctrine\ORM\EntityManager;
use Btn\BaseBundle\Model\Manager;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Deal manager
 *
 **/
class DealManager extends Manager
{
    /**
     * $templateName to render deal
     *
     * @var TwigTemplate
     */
    protected $templateName = 'BtnDealBundle:Default:invoice.html.twig';

     /**
     * Deal.
     *
     * @var Btn\DealBundle\Entity\Deal
     */
    protected $deal;

    /**
     * count of items in deal.
     *
     * @var count
     */
    protected $countItems = 0;

    /**
     * quantity in all items
     *
     * @var quantity
     */
    protected $quantityItems = 0;

    /**
     * kernel
     *
     * @var root dir
     */
    protected $kernelRootDir;

    /**
     *  user fields
     *
     * @var array() with fields that will be saved to the database
     *
     */
    protected $userFields = array('id', 'username');

    /**
     * Constructor.
     *
     */
    public function __construct(EntityManager $em, \Twig_Environment $twig = null, PdfManager $pdf, $kernelRootDir, Paginator $paginator, $formFactory, $seller)
    {
        parent::__construct($em, $paginator, $twig, $formFactory);

        $this->deal          = new Deal();
        $this->pdf           = $pdf;
        $this->kernelRootDir = $kernelRootDir;
        $this->repo          = $this->em->getRepository('BtnDealBundle:Deal');

        //set default seller values from config file
        $this->setParam('seller', $seller);
    }

    /**
     * set variables for deal
     *
     * @param $arr array()
     * @return void
     * @author
     **/
    public function setParams(array $arr)
    {
        foreach ($arr as $key => $value) {
            $this->setParam($key, $value);
        }
    }

    /**
     * set value for one field in deal
     *
     * @param $field from database
     * @param $value
     * @return void
     * @author
     **/
    public function setParam($field, $value)
    {
        //if need set user convert entity to array
        if ($field == 'seller' || $field == 'buyer') {
            //normalize from defined user fields
            $value = $this->toArray($value, $this->userFields);
        }

        //set new value
        $func = 'set' . ucfirst($field);
        $this->deal->$func($value);
    }

    /**
     * return param from deal
     *
     * @param $field in deal
     * @return mixed
     * @author
     **/
    public function getParam($field)
    {
        $func = 'get' . ucfirst($field);

        return $this->deal->$func();
    }

    /**
     * set $countItems and $quantityItems
     *
     * @return void
     * @author
     **/
    public function calculateItems()
    {
        //count items
        $this->countItems = $this->getItems()->count();

        //get all items and add quantity from each
        $this->quantityItems = 0;

        foreach ($this->getItems()->getValues() as $item) {
            $this->quantityItems += $item->getQuantity();
        }
    }

    /**
     * get items
     *
     * @return ArrayCollections
     * @author
     **/
    public function getItems()
    {
        return $this->deal->getItems();
    }

    /**
     * convert object to array from param fields
     *
     * @param $object object of Entity
     * @param $fields array()
     * @return array()
     * @author
     **/
    public function toArray($object, array $fields)
    {
        //if $object is array return
        if (is_array($object)) {
            return $object;
        }
        $arr = array();

        //for each field in object get value and rewrite to array
        foreach ($fields as $field) {

            $func = 'get'.ucfirst($field);
            $arr[$field] = $object->$func();

            if ($arr[$field] instanceof \DateTime) {
                $arr[$field] = $arr[$field]->format('Y-m-d');
            }
        }//foreach

        return $arr;
    }

    /**
     * set $userFields
     *
     * @param $arr array()
     * @return void
     * @author
     **/
    public function setUserFields(array $arr)
    {
        $this->userFields = $arr;
    }

    /**
     * Set deal
     *
     * @param  Btn\DealBundle\Entity\Deal $deal
     * @return void
     */
    public function setDeal(\Btn\DealBundle\Entity\Deal $deal = null)
    {
        $this->deal = $deal;
    }

    /**
     * add item to deal
     *
     * @param $item instance of DealItem or array()
     * @return boolean
     * @author
     **/
    public function addItem($item)
    {
        //if item isn't DealItem
        if (is_array($item)) {
            //create empty item
            $obj = new DealItem();

            //get all fields from array and set in object
            foreach ($item as $key => $value) {
                $func = 'set' . ucfirst($key);
                $obj->$func($value);
            }

            //rewrite obj to item
            $item = $obj;
        }

        //if item is instance of deal items added him
        if ($item instanceof DealItem) {
            $item->setDeal($this->deal);
            $this->deal->addItem($item);
            $this->calculateItems();
            $this->updateDeal();

            return true;
        } else {
            return false;
        }
    }


    /**
     * flush deal
     *
     * @return void
     * @author
     **/
    public function flush()
    {
        //get entity manager
        $em = $this->em;

        //always before update in database regenerate pdf with $force is true

        //set new filename
        $this->getDeal()->setFile($this->getPdf(true));

        //if havent number, check the last and add +1
        if ($this->deal->getNumber() == null) {

            $this->deal->setNumber($this->generateNumber());
        }

        //persist deal and items
        $em->persist($this->deal);
        //flush to database
        $em->flush();

        return $this->getDeal();
    }

    /**
     * get last number and generate new
     *
     * @return void
     * @author
     **/
    private function generateNumber()
    {
        $lastItem = $this->em
            ->createQuery("select i from BtnDealBundle:Deal i ORDER BY i.issueDate DESC, i.number DESC")
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;

        if ($lastItem) {
            if (date('Y', time()) == $lastItem->getYear() && date('m', time()) == $lastItem->getMonth()) {
                return $lastItem->getNumber() + 1;
            }
        }

        return 1;
    }

    /**
     * find by array and return deal
     *
     * @param  array $arr
     * @return Deal
     * @author
     **/
    public function findOneBy($arr)
    {
        $this->deal = $this->em->getRepository('BtnDealBundle:Deal')->findOneBy($arr);

        return $this->deal;
    }

    /**
     * find by array and return deals, this function don't set sefl::$deal
     *
     * @param  array      $arr
     * @return Collection of Deals
     * @author
     **/
    public function findAllBy($arr)
    {
        return $this->em->getRepository('BtnDealBundle:Deal')->findBy($arr);;
    }

    /**
     * return deal
     *
     * @return deal
     * @author
     **/
    public function getDeal()
    {
        return $this->deal;
    }

    /**
     * get total gross deal
     *
     * @return Decimal
     * @author
     **/
    public function getTotalGross()
    {
        return $this->deal->getGross();
    }

    /**
     * get total netto deal
     *
     * @return Decimal
     * @author
     **/
    public function getTotalNetto()
    {
        return $this->deal->getNetto();
    }

    /**
     * get total tax value deal
     *
     * @return Decimal
     * @author
     **/
    public function getTotalTaxValue()
    {
        return $this->deal->getTaxValue();
    }

    /**
     * get all quantity of items in deal
     *
     * @return integer
     * @author
     **/
    public function getQuantityItems()
    {
        return $this->quantityItems;
    }

    /**
     * return count of items
     *
     * @return integer
     * @author
     **/
    public function getCountItems()
    {
        return $this->countItems;
    }

    /**
     * update parent deal
     *
     * @return void
     * @author
     **/
    public function updateDeal()
    {
        $this->deal->update();
    }

    /**
     * set $templateName
     *
     * @param $templateName string
     * @return void
     * @author
     **/
    public function setTemplate($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * get $templateName
     *
     * @return string
     * @author
     **/
    public function getTemplate()
    {
        return $this->templateName;
    }

    /**
     * render template
     *
     * @param $templateName string
     * @return HTML
     * @author
     **/
    public function render($templateName = null)
    {
        if ($templateName === null) {
            $templateName = $this->templateName;
        }

        $this->twig->disableStrictVariables();

        return $this->twig->render($templateName, array(
                'deal'    => $this->deal,
                'seller'  => $this->deal->getSeller(),
                'buyer'   => $this->deal->getBuyer(),
            )
        );
    }

    /**
     * return pdf filename
     *
     * @param  boolean $force for generate new pdf always
     * @return string  (filename)
     * @author
     **/
    private function getPdf($force = false)
    {
        $filename = null;

        //if has generated file in deal and force is false return filename
        if ($this->getDeal()->getFile() && $force == false) {
            $filename = $this->getDeal()->getFile();
        } else {
            //get path
            $path = $this->getDestinationPath() . $this->getDeal()->getFile();

            //if have file and force is true unlink file
            if (file_exists($path) && $this->getDeal()->getFile()) {
                unlink($path);
            }

            //else generate file and flush in database
            $filename = $this->pdf->getPdf($this->render(), $this->getDestinationPath());
        }

        return $filename;
    }

    /**
     * get path for save pdf
     *
     * @return string
     * @author
     **/
    public function getDestinationPath()
    {
        return $this->kernelRootDir . '/../web/' . $this->getDeal()->getDestinationDir();
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function download($id, $ownerId = null, $admin = false)
    {
        $this->findOneBy(array('id' => $id));

        if ($this->deal) {
            if (($this->deal->getOwnerId() === $ownerId && $ownerId !== null) || $admin === true) {
                $file = $this->getDestinationPath() .
                    $this->deal->getFile()
                ;

                if (file_exists($file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . $this->deal->getFile());
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    readfile($file);
                    exit;
                }
            }
        }

        return false;
    }
}
