function deverouiller(id,objet,idnum)
{
	if(objet == 1 || objet == 2)
	{
		var libelle_objet = decodeURIComponent(libelle[21]).replace('&id',idnum);
	}
	else
	{
		var libelle_objet = decodeURIComponent(libelle[22].replace('&id',idnum));
	}
	
	 if(confirm(libelle_objet+' : '+id))
	 {
		/**==================================================================
		 * RECUPERATION CONTENU ETAPE POUR EDITION
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'delete_id.php';
		configuration['delai_tentative'] = 5000;
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = 'id='+id+'&objet='+objet+'&ssid='+ssid;
		configuration['fonction_a_executer_reponse'] = 'retour_test';
		configuration['fonction_a_executer_cas_non_reponse'] = 'retour_test';
		ajax_call(configuration);
		/**==================================================================*/	
     }
}

function retour_test(reponse)
{
	vimofy_refresh('vimofy_lock');
	alert(reponse);
}