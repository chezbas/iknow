var maj_pole = false;

function reduire_agrandir_barre_infomations()
{
	
	if(document.getElementById('info').style.display == 'none')
	{
		// Afficher
		document.getElementById('info').style.float = 'right';
		document.getElementById('info').style.display = '';
		document.getElementById('deplier').style.backgroundPosition = '0 -60px';
	}
	else
	{
		// Masquer
		document.getElementById('info').style.display = 'none';	
		document.getElementById('deplier').style.backgroundPosition = '0 -75px';
	}		
}

function bloquer_icode()
{
	aff_btn = new Array([get_lib(182),get_lib(181)],["controler_icode(true,true);","close_msgbox();"]);
	generer_msgbox(get_lib(79),get_lib(80),'question','msg',aff_btn);
}

//************************************************************************************************************
//TEXTAREA
//************************************************************************************************************

function inserer_tab_textarea(champ, e){
	
	
	if(e.keyCode == 9){
		
		//alert('tab');
		
	}
		
}

//************************************************************************************************************




//************************************************************************************************************
// POPUP VOLANTE
//************************************************************************************************************

function ietruebody(){
	return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
}

//************************************************************************************************************



// Affiche la liste des raccouris clavier en visu et modif
function lst_rac()
{
	
	message = '<b>Echap</b> - Déplier/Replier la barre d\'informations<br />';
	message += '<b>Ctrl + flèche du haut</b> - Déplacement sur l\'onglet à droite<br />';
	message += '<b>Ctrl + flèche du bas</b> - Déplacement sur l\'onglet à gauche<br />';
	message += '<b>Ctrl + F1</b> - Aide<br />';
	
	iknow_panel_set_cts(decodeURIComponent(get_lib(70)));
	iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
}
