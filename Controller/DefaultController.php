<?php

namespace ne0shad0w\ZephyrAdminCoreBundle\ZephyrAdminCoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Security\UserProvider;
use symfony\bundle\frameworkbundle\frameworkbundle;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
class DefaultController extends Controller
{
    /**
     * @Route("/admin/dashboard", name="index")
     */
    public function indexAction()
    {
		$module = array();
		$module_tab = array();
		foreach ($this->container->getParameter('kernel.bundles') as $b){
			if ( $b !== "JMS\DiExtraBundle\JMSDiExtraBundle" ){
			$bundle = new $b ;
			//Vérifie sur le module doit être charger pour les administrateurs.
			if ( method_exists( $bundle , "adm_module" )  ) {
				if ( method_exists( $bundle , "load_module" ) && $bundle->adm_module() === true && $this->container->get('security.context')->isGranted('ROLE_ADMIN') ) array_push($module , $bundle->load_module() );
					
			} 
			//Vérifie si le module doit être charger pour les utilisateurs.
			if ( method_exists( $bundle , "user_module" ) ) {
				if ( method_exists( $bundle , "load_module" ) && $bundle->user_module() === true && $this->container->get('security.context')->isGranted('ROLE_USER') ) array_push($module , $bundle->load_module() );
			}
			
			//Vérifie si le module ( tableau de bord ) doit être charger.
			if ( method_exists( $bundle , "user_module" )  ) {
				if ( method_exists( $bundle , "dashboard_module" ) && ( $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN') || $bundle->user_module() === true || ( $this->container->get('security.context')->isGranted('ROLE_ADMIN') && $bundle->adm_module() === true)  )  ) array_push($module_tab , $bundle->dashboard_module() );
			}
			}
		}
        $response = $this->render('ZephyrAdminCoreBundle:Security:accueil.html.twig' , array('module' => $module , 'dashboard_module'=>$module_tab ) );
	//	$response->setPrivate();
	//	$response->setETag(md5($response->getContent()));
	//	$response->isNotModified($this->getRequest());
        return $response;

    }
    /**
     * @Route("/admin/dashboard/listuser", name="index")
     */
	/**

   * @Security("has_role('ROLE_ADMIN')")

   */
    public function listuserAction()
    {
		$userManager = $this->container->get('fos_user.user_manager');
		
		$users = $userManager->findUsers();
		$user = $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('ZephyrAdminCoreBundle:Security:list_user.html.twig',array('users'=>$users , 'id_current'=> $user->getId() ));
    }
	/**

   * @Security("has_role('ROLE_ADMIN')")

   */
  public function cacheClearAction(Request $request) {
		$command = $this->container->get('ZephyrAdminCoreBundle.cache.clear');
		$input = new ArgvInput(array('--env=' . $this->container->getParameter('kernel.environment')));
		$output = new ConsoleOutput();
		$command->run($input, $output);	
		$this->get('session')->getFlashBag()->add('notice', $this->get('translator')->trans('cacheClear'));
		return $this->redirect($this->generateUrl('adm_accueil'));
	
   }


}
