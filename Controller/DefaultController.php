<?php

namespace Btn\DealBundle\Controller;

use Btn\BaseBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController
{
    protected function download($id, $fileName = null) {
        $entity = $this->findEntity('BtnDealBundle:Deal', $id);

        $filePath = $entity->getFilePath();
        $fileName = $fileName ? $fileName : basename($filePath);

        // Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filePath));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Content-length', filesize($filePath));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(readfile($filePath));

        return $responce;
    }
}
