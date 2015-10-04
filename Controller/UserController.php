<?php

namespace ne0shad0w\ZephyrAdminCoreBundle\ZephyrAdminCoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\UserBundle\Security\UserProvider;
use FOS\UserBundle\Util\UserManipulator;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{
    /**
     * @Route("/admin/dashboard/promote_user", name="promote")
     */
	/**

   * @Security("has_role('ROLE_ADMIN')")

   */
    public function promoteAction($username)
    {
		$userManager = $this->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($username);
		if ( $user ) {
			$user->setSuperAdmin(false);
			$user->setRoles(array('ROLE_ADMIN'));
			$userManager->updateUser($user);
			
			$value = $this->container->get('translator')->trans('administration.promotion.reussi' , array( '%s' => $username ) ) ;
			$this->get('session')->getFlashBag()->add('reussi', $value);

		}
		return $this->redirect( $this->generateUrl('adm_list_user') );
    }

//Gestion de la redirection lors de la connection.
    public function dispatchAction()
    {
		
		if ( $this->get('security.context')->isGranted('ROLE_ADMIN')) {
				return $this->redirect( $this->generateUrl('user_accueil') );
		} 
		if ( $this->get('security.context')->isGranted('ROLE_USER')) {
				return $this->redirect( $this->generateUrl('membre_accueil') );
		} 
		return $this->redirect( $this->generateUrl('zephyr_homepage') );
		
    }
	/**

   * @Security("has_role('ROLE_ADMIN')")

   */
	
	public function deleteAction($username)
    {
		$userManager = $this->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($username);
		if ( $user ) {
			
			$userManager->deleteUser($user);
			
			$value = $this->container->get('translator')->trans('administration.delete.reussi' , array( '%s' => $username ) ) ;
			$this->get('session')->getFlashBag()->add('reussi', $value);

		} else {
			
			$value = $this->container->get('translator')->trans('administration.delete.echec' , array( '%s' => $username ) ) ;
			$this->get('session')->getFlashBag()->add('alert', $value);
		
		}
		return $this->redirect( $this->generateUrl('adm_list_user') );
    }

    /**
     * @Route("/admin/dashboard/demote_user", name="demote")
     */
	/**

   * @Security("has_role('ROLE_ADMIN')")

   */
    public function demoteAction($username)
    {
		$userManager = $this->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($username);
		if ( $user ) {
			$user->setSuperAdmin(false);
			$user->setRoles(array('ROLE_USER'));
			$userManager->updateUser($user);
			
			$value = $this->container->get('translator')->trans('administration.demote.reussi' , array( '%s' => $username ) ) ;
			$this->get('session')->getFlashBag()->add('reussi', $value);

		}
		return $this->redirect( $this->generateUrl('adm_list_user') );
    }

    /**
     * @Route("/admin/dashboard/active_user", name="active")
     */
	/**

   * @Security("has_role('ROLE_ADMIN')")

   */
    public function activeAction($username)
    {
		$userManager = $this->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($username);
		if ( $user ) {
			$user->setLocked(true);
			$userManager->updateUser($user);
			
			$value = $this->container->get('translator')->trans('administration.blocked.reussi' , array( '%s' => $username ) ) ;
			$this->get('session')->getFlashBag()->add('reussi', $value);

		}
		return $this->redirect( $this->generateUrl('adm_list_user') );
    }
    /**
     * @Route("/admin/dashboard/desactive_user", name="desactive")
     */
	/**

   * @Security("has_role('ROLE_ADMIN')")

   */
    public function desactiveAction($username)
    {
		$userManager = $this->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($username);
		if ( $user ) {
			$user->setLocked(false);
			$userManager->updateUser($user);
			
			$value = $this->container->get('translator')->trans('administration.unblocked.reussi' , array( '%s' => $username ) ) ;
			$this->get('session')->getFlashBag()->add('reussi', $value);

		}
		return $this->redirect( $this->generateUrl('adm_list_user') );
    }

}
