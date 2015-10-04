<?php

namespace ne0shad0w\ZephyrAdminCoreBundle\ZephyrAdminCoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Security\UserProvider;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NavController extends Controller
{
    public function indexAction($pages)
    {
 		
    	$route = $this->getRequest()->get('_route');
        return $this->render('ZephyrAdminCoreBundle:nav:chemin.html.twig',array('chemin'=>$route,'pages'=>$pages));
    }

}
