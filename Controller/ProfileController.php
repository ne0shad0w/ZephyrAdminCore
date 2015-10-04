<?php
namespace ne0shad0w\ZephyrAdminCoreBundle\ZephyrAdminCoreBundle\Controller;


use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ProfileController extends Controller
{
    public function showAction($username)
    {
			$userManager = $this->container->get('fos_user.user_manager');		
			$user = $userManager->findUserByUsername($username);
			$message="";
			if (!is_object($user) || !$user instanceof UserInterface) {
				//throw new AccessDeniedException('This user does not have access to this section.');
				//$this->get('session')->getFlashBag()->add('notice', 'L\'utilisateur demandé n\'existe pas.');
				$user = $this->get('security.token_storage')->getToken()->getUser();
				$message = "L'utilisateur recherché n'existe pas.";
			}
	
			return $this->render('FOSUserBundle:Profile:show.html.twig', array(
				'user' => $user , 'message'=>$message
			));
		
        // do custom stuff

         
    }
	
	public function editAction()
    {
			$userManager = $this->container->get('fos_user.user_manager');		
			$user = $this->get('security.token_storage')->getToken()->getUser();
			//$user = $userManager->findUserByUsername($this->get('security.context')->getToken()->getUser()->getUsername());
			$message="";
			if (!is_object($user) || !$user instanceof UserInterface) {
			
				$message = "L'utilisateur recherché n'existe pas.";
			}
			$formFactory = $this->container->get('fos_user.profile.form.factory');
        	$form = $formFactory->createForm();
        	$form->setData($user);
			
			return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
				'form' => $form->createView() 
			));
		
        // do custom stuff

         
    }

}