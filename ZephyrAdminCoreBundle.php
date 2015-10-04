<?php 

namespace ne0shad0w\ZephyrAdminCoreBundle\ZephyrAdminCoreBundle ;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZephyrAdminCoreBundle extends Bundle
{
	public function load_module(){
		return array( array("nom"=>"gestionUtilisateurs" , "route"=>"adm_list_user" , "icone"=>"glyphicon-user"),
					  array("nom"=>"gestionFichier" , "route"=>"adm_gestion_fichier" , "icone"=>"glyphicon-folder-open"),
					  array("nom"=>"gestionDocument" , "route"=>"adm_document" , "icone"=>"glyphicon-folder-open") 
					 );		
	}
	public function adm_module(){	
		return true;
	}
	
}