<?php

namespace Drupal\update_site_info_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Zend\Diactoros\Response\JsonResponse;

class PageJson extends ControllerBase{

    function GenerateJsonResponse($apikey,$node)
    {
        $nodeType = "";
        /*Fetch the site information form entity to access the form field values*/
        $siteInfoForm = \Drupal::formBuilder()->getForm('\Drupal\system\Form\SiteInformationForm');
        $siteAPIkey = $siteInfoForm["site_api_key"]["#default_value"];

        /*Fetch node information based on node id provided in the URL*/
        $nodeInfo = \Drupal\node\Entity\Node::load($node);
        if ($nodeInfo) {
            $nodeType = $nodeInfo->getType();

            /*Add the node information into the array to convert it to JSON Format*/
            $json_array['data'][] = array(
                'type' => $nodeInfo->get('type')->target_id,
                'id' => $nodeInfo->get('nid')->value,
                'attributes' => array(
                    'title' => $nodeInfo->get('title')->value,
                    'content' => $nodeInfo->get('body')->value,
                ),
            );
        }

            /* Check the conditions if node od type Page and API key in the URL is submitted previously */
        if ($nodeType == 'page') {
            if ($apikey == $siteAPIkey) {
                 return new JsonResponse($json_array);
            }
        }

        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();

    }

}