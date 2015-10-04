<?php
// src/Core/CoreBundle/Menu/Builder.php
namespace ne0shad0w\ZephyrAdminCoreBundle\ZephyrAdminCoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\Templating\EngineInterface;
class Builder extends ContainerAware
{
	private $factory;
	private $em;
	protected $container;
     /**
     * @param FactoryInterface $factory
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(FactoryInterface $factory, \Doctrine\ORM\EntityManager $em , Container $container ,TranslatorInterface  $translator  )
    {
        $this->factory = $factory;
		$this->em = $em;
		$this->container = $container;
		$this->translator = $translator;
    }
	public function createAdminMenu()
    {
		
		$menu = $this->factory->createItem('admin' /*, array('navbar' => true)*/ );		
        return $menu;
    }

	public function createUserMenu()
    {
		
		$menu = $this->factory->createItem('user' );
		
		
		    $simpleLien = array();
		foreach ($this->container->getParameter('kernel.bundles') as $b){
			if ( $b !== "JMS\DiExtraBundle\JMSDiExtraBundle") {
				$bundle = new $b ;
				//Vérifie sur le module doit être charger pour les administrateurs.
				if ( method_exists( $bundle , "menu_module" ) && ( $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN') || $bundle->user_module() === true || ( $this->container->get('security.context')->isGranted('ROLE_ADMIN') && $bundle->adm_module() === true)  ) ) {
					$module = array();
					$module = $bundle->menu_module($this->container->getParameter('locale')) ;
					if ( isset( $module['route']) ) {
						if ( $this->container->get('security.context')->isGranted('ROLE_ADMIN') || $module['user'] == true ) {
							array_push($simpleLien, $module );
						}
					} else {
						$menu_module = $menu->addChild($this->translator->trans($module['titre']));
						if ( isset($module['sousmenu'])) {
							foreach($module['sousmenu'] as $t => $v)
								$menu_module->addChild($this->translator->trans($t),array('route' => $v));   
						}
						if ( isset($module['menu'])) {
							$menu_module = $this->createSubMenu($menu_module , $module['menu'] ) ;
						}
					}
					
				} 
			}
		}
		if ( $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			$user = $menu->addChild($this->translator->trans('utilisateurs'));
			$user->addChild($this->translator->trans('listeUtilisateurs'),array('route' => 'adm_list_user'));    
			$user->addChild($this->translator->trans('ajoutUtilisateurs'),array('route' => 'fos_user_registration_register')); 
		}
		foreach ( $simpleLien as $lien ) {
			$menu->addChild($this->translator->trans($lien['titre']),array('route' => $lien['route']));   
		}
		if ( $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			$menu->addChild($this->translator->trans('traducteur'),array('route' => 'lexik_translation_grid'));  
			if ( $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN') )
			$menu->addChild($this->translator->trans('viderCache'),array('route' => 'adm_clear_cache'));
		}
        return $menu;
    }

	public function createSubMenu($menu_module , $submenu  ){
		foreach($submenu as $arr){
				if ( $this->container->get('security.context')->isGranted('ROLE_ADMIN') || $arr['user'] == true ) {
					$route = $arr['route'];
					if ( !is_array($route) ) {
						$menu_module->addChild($this->translator->trans($arr['titre']),array('route' => $arr['route']));  
					} else {
						$newsub = $menu_module->addChild($this->translator->trans($arr['titre']) );
						$newsub = $this->createSubMenu($newsub , $arr['route'] ) ;
					}
									
				}
			}
		return $menu_module	 ;
	}

}