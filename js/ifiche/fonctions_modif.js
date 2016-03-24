function popup_statut()
{
	switch (document.getElementById('statut').value) {
		case '1':
			ddrivetipimg('images/flux_processus_fiches1.jpg',350,120,350);
			break;
		case '2':
			ddrivetipimg('images/flux_processus_fiches2.jpg',350,120,350);
			break;
		case '3':
			ddrivetipimg('images/flux_processus_fiches3.jpg',350,120,350);
			break;
		case '4':
			ddrivetipimg('images/flux_processus_fiches4.jpg',350,120,350);
			break;
		case '5':
			ddrivetipimg('images/flux_processus_fiches5.jpg',350,120,350);
			break;
		case '6':
			ddrivetipimg('images/flux_processus_fiches6.jpg',350,120,350);
			break;
	}
}

function target_statut()
{
	switch (document.getElementById('statut').value) {
	
		case '1':
			window.open('./screenshot/flux_processus_fiches1.jpg');
			break;
		case '2':
			window.open('./screenshot/flux_processus_fiches2.jpg');
			break;
		case '3':
			window.open('./screenshot/flux_processus_fiches3.jpg');
			break;
		case '4':
			window.open('./screenshot/flux_processus_fiches4.jpg');
			break;
		case '5':
			window.open('./screenshot/flux_processus_fiches5.jpg');
			break;
		case '6':
			window.open('./screenshot/flux_processus_fiches6.jpg');
			break;
	}
}

/**
 * Appelée lorsque l'utilisateur saisi du texte dans le champ input du trigramme
 * Si l'utilisateur fait ctrl-s lance une sauvegarde de la fiche
 * @param e
 * @return
 */
function rac_save_sheet(e)
{
	e = e || event; 		// Sets the event variable in IE, because it's dumb...
	//alert(e.keyCode);
	//Detection du CTRL

	
	// Sauvegarde - CTRL+S
    if(e.keyCode == 83 && isCtrl == true)
    {
    	isCtrl = false;
    	save_sheet(false);		// Contrôle et sauvegarde de la fiche
    	return 1;	
    } 
    else
    {
    	if(e.keyCode != 17)
    	{
    		isCtrl = false;
    	}
    }
    
    if(e.keyCode == 17)
	{
	     isCtrl=true;
    }	   
    else
    {
    	isCtrl=false;
    }
	
}

function bloquer_fiche(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		aff_btn = new Array([get_lib(182),get_lib(181)],["bloquer_fiche(true);","close_msgbox();"]);
    	generer_msgbox(get_lib(141),get_lib(142),'question','msg',aff_btn);
	}
	else
	{
		save_sheet(true);
	}
}