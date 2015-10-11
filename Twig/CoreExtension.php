<?php 
namespace ne0shad0w\ZephyrAdminCoreBundle\ZephyrAdminCoreBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;
use Doctrine\CouchDB\View\Query;
use A2lix\I18nDoctrineBundle\Annotation\I18nDoctrine;

class CoreExtension extends \Twig_Extension
{
	public function __construct(Router $generator , TranslatorInterface $translator , $convertir , $em)
  	{
    	$this->generator = $generator;
		$this->translator = $translator;
		$this->convertir = $convertir;
		$this->em = $em ;
  	}   
	
	public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction ('chemin' , array( $this , 'chemin') ),
            new \Twig_SimpleFunction ('breadcrumb' , array( $this , 'breadcrumb') ),
			new \Twig_SimpleFunction ('genereLien' , array( $this , 'genereLien') )
        );
    }

    public function breadcrumb($current , $params)
    {
		$routes=array();
		$coll = $this->generator->getRouteCollection();
		foreach ( $coll as $name => $route ) {
			$name = $this->clearName($name);
			if ( $this->clearRoutes($name) ) $routes[$name] = $route ;
		}
		$path = $this->creerPath($path="" , $current , $routes , $params );
		if ( $current == "zephyr_page")
			$path = $this->creerPathPage($path , $current , $params);
			
		if ( $path != ""){ 
			if ( $current == "zephyr_page")
				return $path . " >> " . $this->translator->trans($params['name']);
			else
       	 		return $path . " >> " . $this->translator->trans($current);
		} else 
		 	return "";
    }

	public function creerPathPage($path , $current , $params){
		$page = $this->em->getRepository('PageBundle:PgPage')->findOneByNomPage($params);
		if ($page) {
			if (  $page->getPageparent() != NULL ){
				$parent = $page->getPageparent();
				$params['name'] = $parent->getNomPage();
				if ( $parent->getPageparent() != NULL ) 
					$path = " >> " . $this->genereLien($current, $params , $parent->getNomPage() ) . $path ;
				else
					$path = $this->genereLien($current, $params , $parent->getNomPage() ) . $path ;
					
				
				return $this->creerPathPage($path , $current, $params ) ;
			}
		}
		return $path ;	
	}

    public function chemin($current , $params)
    {
		if ( $current == "fos_user_security_login" || $current == "" ) return "";
        $first = $this->genereLien('user_accueil') ;
		$routes=array();
		$coll = $this->generator->getRouteCollection();
		foreach ( $coll as $name => $route ) {
			$name = $this->clearName($name);
			if ( $this->clearRoutes($name) ) $routes[$name] = $route ;
		}
		$path = $this->creerPath($path="" , $current , $routes , $params );
		
        return $first . $path . " >> " . $this->translator->trans($current);
    }
	
	 /*
	 * @I18nDoctrine
	 */
	public function genereLien($route , $params = array() , $nom = NULL , $titre="" , $class="" , $style="" , $target="_self" , $autre="")
	{ 
		if ($nom != NULL) { $name = $this->translator->trans($nom); } else { $name = $this->translator->trans($route); }
		return "<a href='".$this->generator->generate($route,$params)."' class='$class' style='$style' target='$target' $autre >". $name ."</a>"; 
	}
    public function getName()
    {
        return 'app_extension';
    }
	
	private function creerPath($path, $current , $routes , $paramCurrent = array()) 
	{
		$params= array();
		if ( isset($routes[$current]) ) {
			if ( $routes[$current]->getDefaults() )
				$options = $routes[$current]->getDefaults();
			if ( isset(	$options['routeparent']) ){
				$exp = explode('||',$options['routeparent']);
				if ( count($exp) > 1 ) {
					for($i=1; $i<=count($exp)-1; $i++){
						$t = explode(",",$exp[$i]);
						$type = explode('=>',$t[0]);
						$regle['type'] = $type[1];
						if ( $regle['type'] != "variable") {
							$cible = explode('=>',$t[1]);
							$dest = explode('=>',$t[2]);
							
							$regle['entity'] = $cible[0];
							$regle['entityFind'] = $cible[1];
							$regle[$cible[1]] = $paramCurrent[$cible[1]];
							$regle['findVariable'] = $dest[0];
							if ( count($dest) >= 3 ) $regle['findVariableClass'] = $dest[2];
							$regle['variableName'] = $dest[1];
						}
						switch($regle['type']){
							case 'class':
								$params = array_merge($params , $this->findByClass($regle) ) ;
								break;	
							case 'variable':
								$params = $paramCurrent ;
								break;
						}
					}
				}
				$path = " >> " . $this->genereLien($exp[0], $params) . $path ;
				return $this->creerPath($path , $exp[0] , $routes ,$paramCurrent) ;
			}
		}
		return $path ;
	}
	
	private function findByClass($regle)
	{
		$class = $this->em->getRepository($regle['entity'])->find($regle[$regle['entityFind']] );
		if ( $class ) {
			$data2 = (array) $class;
			foreach( $data2 as $d=>$v){
				$d = str_replace("*",$regle['entity'],$d);
				$data[$d]=$v;
			}
			if ( isset( $regle['findVariableClass'] ) ) {
				$c = (array) $data["\x00".$regle['entity']."\x00".$regle['findVariable'] ] ;
				$p = array($regle['variableName'] => $c[ str_replace("]","\x00",str_replace("[","\x00",$regle['findVariableClass']) ) ] );
			} else{
				$p = array($regle['variableName'] => $data["\x00".$regle['entity']."\x00".$regle['findVariable'] ] );
			}
			return $p;
		}
		return array();
	}
	
	private function clearRoutes($var)
	{
		if ( substr($var, 0 , 4) == "ajax" ) return false; 
		if ( substr($var, 0 , 1) == "_" ) return false; 
		
		return true ;
	}
	
	// Function pour fixer un probleme avec le bundle multilangue JSMroutingBundle
	private function clearName($name)
	{
		if ( strpos($name, "__RG__") !== false  ) 
			$name = substr($name, 8 )  ;  
		return $name ;
	}

}