<?php

namespace Btn\DealBundle\Model;

use Symfony\Component\HttpFoundation\Response;
use TCPDF;

/**
 * PDF manager
 *
 **/
class PdfManager extends TCPDF
{
    /**
     * @var string Path to assets
     */
    private $path;

    /**
     * @var string Author of pdf
     */
    private $author = 'Btn';

    /**
     * @var Title of pdf
     */
    private $title = '';

    /**
     * Constructor.
     *
     */
    public function __construct($webRoot)
    {
        parent::__construct();
        $this->path   = $webRoot.'/tcpdf/';
    }

    /**
     *
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     *
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /*
     * Set PDF page parameters
     */
    public function init()
    {
        // set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor($this->author);
        $this->SetTitle($this->title);

        //disable header and footer
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);

        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $this->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);

        //set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set default font subsetting mode
        $this->setFontSubsetting(true);

        // Set font default font
        $this->SetFont('dejavusans', '', 7, '', true);

        // Add a page
        $this->AddPage();

    }

    /**
     * Return template as PDF file
     *
     */
    public function getPdf($html, $destination, $filename = null)
    {
        $this->init();
        $this->writeHTML($html, true, 0, true, 0);
        if (!$filename) {
            $filename = md5(time());
        }
        $filename .= '.pdf';

        $this->Output($destination . $filename, 'F');
        return $filename;
    }
}
