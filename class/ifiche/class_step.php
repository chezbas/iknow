<?php

class step_alone
{
	
	// --------------------------------------------------- DECLARATION DES ATTRIBUTS -----------------------------------------------------------//
	public $logo_tag;					// Contient l'image du tag pour chaque étape
	public $tag_hover;					// Contient l'image du tag pour chaque étape
	public $contenu;					// contient les id d'etape, les contenu...
	public $html_temp;					// Contient le rendu en affichage de l'étape
	public $numero;     				// Contient le numero de l'etape
	public $lien_iobjet_etape;			// Tableau qui contient le code html des liens vers des iobjets (0 => lien trigger du cartouche,1 => Id de l'objet appelé,2 => version exacte de l'objet appelé,3 => valorié si la version est précisée dans le lien d'appel,4 => Type d'objet(ifiche.php/icode.php/idossier.php),5 => version max de l'iobjet)
	public $tab_tag;					// Tableau qui contient les tags des étapes 
	
	public function __construct()
	{
		$this->tab_tag = 0;
	}
}

?>