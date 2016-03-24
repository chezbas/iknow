var maj_vimofy_param = false;

function type_gestion_date()
{
	if(document.getElementById('statut').value < decodeURIComponent(conf[32]))
	{
		document.getElementById('lib_gestion_date').innerHTML = get_lib(354);
	}
	else
	{
		document.getElementById('lib_gestion_date').innerHTML = get_lib(355);
	}
}

//Annule les vimofy des liens
function cancel_vimofy_lien()
{
	emplacement_vimofy_cartouche_param = false;		// Contient l'id de l'élément en cours ou il y a une vimofy
	emplacement_cartouche_param = false;			// Contient l'id de l'élément en cours ou il y a la liste des paramètres d'entrée	
	emplacement_vimofy_cartouche_infos = false;
	emplacement_cartouche_infos = false;
}
/**
 * Annule les modifications d'une fiche
 * @param url : page de redirection après annulation des modifs
 * @return
 */
function cancel_modif(url,reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		aff_btn = new Array([get_lib(182),get_lib(181)],["cancel_modif('"+url+"',true);","close_msgbox();"]);
    	generer_msgbox(get_lib(122),get_lib(136),'question','msg',aff_btn); 
	}
	else
	{
    	fiche_sauvegardee = true;
    	window.location.replace(url);
	}
}




function end_load_ajax(reponse,cacher)
{
	if(application == 1)
	{
		if(typeof(cacher) == 'undefined')
		{
			// Affiche la barre d'outils
			document.getElementById('barre_outils').style.visibility = 'visible';
		}
		else
		{
			// Masquage la barre d'outils
			document.getElementById('barre_outils').style.visibility = 'hidden';
		}
	}
	// Masquage de la fenêtre d'attente
	close_msgbox();
}


/**
 * Masque les boutons des étapes sauf ceux de id_etape
 * 
 * @param id_etape : Identifiant de l'étape active
 * @param boutons : Boutons à mettre dans la barre d'outils de l'étape active
 * @param type_visu : true -> visu en ligne, false -> visu par étape
 */
function masquer_boutons_etapes(id_etape,boutons,type_visu)
{
	if(application == 1)
	{
		/**==================================================================
		 * MASQUAGE DE LA BARRE D'OUTILS DE LA FICHE
		 ====================================================================*/	
		document.getElementById('barre_outils').style.visibility = 'hidden';
		/**==================================================================*/	
	
		/**==================================================================
		 * GESTION DE L'ETAPE ACTIVE
		 ====================================================================*/	
		
			/**==================================================================
			 * GESTION DES OUTILS DE GAUCHE (editer, copier...)
			 ====================================================================*/	
			// Récupération de la div des outils de l'étape
			if(typeof(type_visu) == 'undefined' || type_visu == true)
			{
				outils_etape_active = document.getElementById('outils_step'+id_etape);
			}
			else
			{
				outils_etape_active = document.getElementById('outils_step'+id_etape+'l');
			}
			/**==================================================================*/	
			
			/**==================================================================
			 * MISE EN PLACE DES BOUTONS
			 ====================================================================*/	
			outils_etape_active.innerHTML = boutons;
			/**==================================================================*/	
		
			/**==================================================================
			 * GESTION DES OUTILS DE GAUCHE (Déplacer et supprimer)
			 ====================================================================*/	
			// Bouton déplacer
			if(document.getElementById('deplace_etape_num'+id_etape))
			{
				bouton_deplace_etape_active = document.getElementById('deplace_etape_num'+id_etape);
				bouton_deplace_etape_active.style.display = 'none';
			}
			
			// Bouton supprimer
			if(document.getElementById('del_step'+id_etape))
			{
				bouton_del_step_active = document.getElementById('del_step'+id_etape);
				bouton_del_step_active.style.display = 'none';
			}	
			/**==================================================================*/		
				
			/**==================================================================
			* GESTION DU BOUTON D'AJOUT D'UNE ETAPE
			====================================================================*/				
			// Suppression du bouton d'ajout d'étape
			boutons_ajout = document.getElementById('ajouter'+id_etape);
			boutons_ajout.style.display = "none" ;
			/**==================================================================*/		
		/**==================================================================*/			
			
		/**==================================================================
		* GESTION DES AUTRES ETAPES
		====================================================================*/				
		var etape_existe = true;	// Flag de test de l'existance d'une étape
		var i = 1;					// Identifiant de l'étape en cours de parcours
		// Parcours de toute les étapes
		while(etape_existe == true)
		{
			
			// On ne le fait pas pour notre étape
			if(i != id_etape)
			{	
				
				if(!document.getElementById('outils_step'+i))
				{
					etape_existe = false;		// Il n'y a plus d'étapes
				}
				else
				{
					// Masquage de la barre d'outils de droite
					document.getElementById('div_outils_step'+i).style.display = 'none';	

					// Masquage du bouton déplacer une étape
					document.getElementById('deplace_etape_num'+i).style.display = 'none';
					
					// Masquage du bouton supprimer une étape
					document.getElementById('del_step'+i).style.display = 'none';
					
					// Masquage du bouton ajouter une étape
					document.getElementById('ajouter'+i).style.display = "none" ;				
				}
			}
			i++;
		}
	
		// Masquage du bouton d'ajout d'étape du haut
		boutons_ajout = document.getElementById('ajouter'+(i - 1));
		boutons_ajout.style.display = "none" ;
		/**==================================================================*/	
	
		/**==================================================================
		 * DEPLACEMENT SUR L'ETAPE EN COURS D'EDITION
		 ====================================================================*/	
		window.location = '#'+id_etape;	
		/**==================================================================*/	
	}
	else
	{
		/**==================================================================
		 * GESTION DES OUTILS DE DROITE
		 ====================================================================*/		
		// Récupération de la div des outils de l'étape
		if(typeof(type_visu) == 'undefined' || type_visu == true)
		{
			outils_etape_active = document.getElementById('outils_step'+id_etape);
		}
		else
		{
			outils_etape_active = document.getElementById('outils_step'+id_etape+'l');
		}	
			/**==================================================================
			 * MISE EN PLACE DES BOUTONS
			 ====================================================================*/	
			outils_etape_active.innerHTML = boutons;
			/**==================================================================*/	
			
		/**==================================================================*/	
		
		/**==================================================================
		* GESTION DES AUTRES ETAPES
		====================================================================*/				
		var etape_existe = true;	// Flag de test de l'existance d'une étape
		var i = 1;					// Identifiant de l'étape en cours de parcours
		
		// Parcours de toute les étapes
		while(etape_existe == true)
		{
			// On ne le fait pas pour notre étape
			if(i != id_etape)
			{	
				if(!document.getElementById('outils_step'+i))
				{
					etape_existe = false;		// Il n'y a plus d'étapes
				}
				else
				{
					// Masquage de la barre d'outils de droite
					document.getElementById('div_outils_step'+i).style.display = 'none';	
					document.getElementById('div_outils_step'+i+'l').style.display = 'none';	
				}
			}
			else
			{
				// Etape en cours
				if(type_visu)
				{
					// Visu en ligne => effacement du bouton par étape
					document.getElementById('div_outils_step'+i+'l').style.display = 'none';
				}
				else
				{
					// Visu par étape => effacement du bouton en ligne
					document.getElementById('div_outils_step'+i).style.display = 'none';
				}
			}
			i++;
		}
		/**==================================================================*/	
	}
}


/**
 * Ré-affiche les boutons des étapes
 * 
 * @param id_etape : Identifiant de l'étape active
 * @param boutons : Boutons à mettre dans la barre d'outils de l'étape active
 * @param type_visu : true -> visu en ligne, false -> visu par étape
 */
function afficher_boutons_etapes(id_etape,type_visu)
{
	if(application == 1)
	{
			
		/**==================================================================
		* GESTION DES AUTRES ETAPES
		====================================================================*/				
		var etape_existe = true;	// Flag de test de l'existance d'une étape
		var i = 1;					// Identifiant de l'étape en cours de parcours
		
		// Parcours de toute les étapes
		while(etape_existe == true)
		{
			// On ne le fait pas pour notre étape
			if(i != id_etape)
			{	
				if(!document.getElementById('outils_step'+i))
				{
					etape_existe = false;		// Il n'y a plus d'étapes
				}
				else
				{
					// Affichage de la barre d'outils de droite
					outils_autre_active = document.getElementById('div_outils_step'+i).style.display = '';
					
					// Affichage du bouton déplacer une étape
					bouton_deplace_etape_autre = document.getElementById('deplace_etape_num'+i).style.display = '';
					
					// Affichage du bouton supprimer une étape
					bouton_del_step_autre = document.getElementById('del_step'+i).style.display = '';
					
					// Affichage du bouton ajouter une étape
					boutons_ajout = document.getElementById('ajouter'+i).style.display = "";
				}
			}
			i++;
		}
	
		/**==================================================================*/	
		
		/**==================================================================
		* GESTION DU BOUTON D'AJOUT D'UNE ETAPE
		====================================================================*/				
		// Affichage du bouton d'ajout d'étape du haut
		document.getElementById('ajouter1').style.display = "" ;

		// Affichage du bouton d'ajout d'étape du bas
		document.getElementById('ajouter'+(i - 1)).style.display = "" ;
		/**==================================================================*/	
		
		/**==================================================================
		 * DEPLACEMENT SUR L'ETAPE EN COURS D'EDITION
		 ====================================================================*/	
		window.location = '#'+id_etape;	
		/**==================================================================*/	
	}
	else
	{
		
		/**==================================================================
		* GESTION DES AUTRES ETAPES
		====================================================================*/				
		var etape_existe = true;	// Flag de test de l'existance d'une étape
		var i = 1;					// Identifiant de l'étape en cours de parcours
		
		// Parcours de toute les étapes
		while(etape_existe == true)
		{
			// On ne le fait pas pour notre étape
			if(i != id_etape)
			{	
				if(!document.getElementById('outils_step'+i))
				{
					etape_existe = false;		// Il n'y a plus d'étapes
				}
				else
				{
					// Masquage de la barre d'outils de droite
					outils_autre_active = document.getElementById('div_outils_step'+i).style.display = '';	
					outils_autre_active = document.getElementById('div_outils_step'+i+'l').style.display = '';	
				}
			}
			else
			{
				// Etape en cours
				if(type_visu)
				{
					// Visu en ligne => effacement du bouton par étape
					outils_autre_active = document.getElementById('div_outils_step'+i+'l').style.display = '';	
				}
				else
				{
					// Visu par étape => effacement du bouton en ligne
					outils_autre_active = document.getElementById('div_outils_step'+i).style.display = '';
				}
			}
			i++;
		}
		/**==================================================================*/	
	}
}


function retourne_tab_entete_actif()
{
	if(typeof( head_tabbar ) != "undefined") 
	{
		return head_tabbar.getActiveTab();
	}
	else
	{
		return '';
	}
}

function retourne_tab_etape_actif()
{
	if(typeof( tabbar_step ) != "undefined") 
	{
		return tabbar_step.getActiveTab();
	}
	else
	{
		return '';
	}
}

function retourne_tab_etape_ligne_actif()
{
	if(typeof( step_tabbar_sep ) != "undefined") 
	{
		return step_tabbar_sep.getActiveTab();
	}
	else
	{
		return '';
	}
}


function ietruebody()
{
	return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
}

function ddrivetip(thetext, theinter, thewidth)
{
	if(theinter=='img')
	{
		thetext = '<img src="'+thetext+'" width="'+width+'" height="'+height+'"/>';
	}

	param2 = thetext;
	param3 = param2.replace("#_123_QUOTE#","'");

	while(param3 != param2)
	{
		param2 = param2.replace("#_123_QUOTE#","'");
		param3 = param2.replace("#_123_QUOTE#","'");
	}

	thetext = param2;

	if(ns6||ie)
	{
		if(typeof(thewidth) != "undefined")
		{
			tipobj.style.width=thewidth+"px";
		}
		tipobj.innerHTML=thetext;
		if(typeof(theinter) != "undefined" && theinter!="")
		{
			image = document.getElementById("tipimage");
			while(image.width>screen.width*0.4||image.height>screen.height*0.4)
			{
				image.width=image.width*0.95;
				image.height=image.height*0.95;
			}	
			tipobj.style.width=image.width+"px";
		}
		enabletip=true;
		return false;
	}
}
function ddrivetipimgligne(thetext,width,height,thewidth)
{

}


function ddrivetipimg(thetext,width,height,thewidth)
{
	theinter = 'img';
	thetext = '<img src="'+thetext+'" width="'+width+'" height="'+height+'"/>';

	param2 = thetext;
	param3 = param2.replace("#_123_QUOTE#","'");

	while(param3 != param2)
	{
		param2 = param2.replace("#_123_QUOTE#","'");
		param3 = param2.replace("#_123_QUOTE#","'");
	}
	thetext = param2;

	if(ns6||ie)
	{
		if(typeof(thewidth)!="undefined")
		{
			tipobj.style.width=thewidth+"px";
		}
		tipobj.innerHTML=thetext;
		if(typeof(theinter)!="undefined" && theinter!="")
		{
			tipobj.style.width=width+"px";
		}
		enabletip=true;
		return false;
	}
}



function positiontip(e)
{
	if(enabletip)
	{
		var curX=(ns6)?e.pageX : event.clientX+ietruebody().scrollLeft;
		var curY=(ns6)?e.pageY : event.clientY+ietruebody().scrollTop;
		var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-10;
		var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-10;
		var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000;

		if(rightedge<tipobj.offsetWidth)
		{
			tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px";
		}
		else if(curX<leftedge)
		{
			tipobj.style.left="5px";
		}
		else
		{
			tipobj.style.left=curX+offsetxpoint+"px";
		}

		if(bottomedge<tipobj.offsetHeight)
		{
			tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px";
		}
		else
		{
			tipobj.style.top=curY+offsetypoint+"px";
		}
		tipobj.style.visibility="visible";
	}
}

function hideddrivetip()
{
	if(ns6||ie)
	{
		enabletip=false;
		tipobj.style.visibility="hidden";
	}		
}



//Affiche la liste des raccourcis clavier en visu et modif
function lst_rac()
{
	iknow_panel_set_cts(get_lib(159));
	iknow_toggle_control();
}

function rac_deplacer_sur_etape(force)
{
	if(application != 1 && tabbar_step.getActiveTab() == 'tab-level2_2')
	{
		if(typeof(force) == 'undefined')
		{		
			aff_btn = new Array([get_lib(182),get_lib(181)],["rac_deplacer_sur_etape(document.getElementById('iknow_msgbox_prompt_value').value);close_msgbox();","close_msgbox();"]);
			generer_msgbox(get_lib(157),get_lib(158),'question','prompt',aff_btn);	
		}
		else
		{
			a_tabbar.setTabActive('tab-level2');
			tabbar_step.setTabActive('tab-level2_2');
			step_tabbar_sep.setTabActive('tab-level2_2_'+force);
		}
	}
	else
	{
		if(typeof(force) == 'undefined')
		{		
			aff_btn = new Array([get_lib(182),get_lib(181)],["rac_deplacer_sur_etape(document.getElementById('iknow_msgbox_prompt_value').value);close_msgbox();","close_msgbox();"]);
			generer_msgbox(get_lib(157),get_lib(158),'question','prompt',aff_btn);	
		}
		else
		{
			a_tabbar.setTabActive('tab-level2');
			window.location='#'+force;
		}
	}
}

function toggle_div(div)
{
	if(document.getElementById(div).style.display == 'none')
	{
		// Afficher
		document.getElementById(div).style.display = '';
	}
	else
	{
		// Masquer
		document.getElementById(div).style.display = 'none';
	}
}

function Isentier(sText)
{
	var ValidChars = "0123456789";
	var IsNumber=true;
	var Char;

	for (i = 0; i < sText.length && IsNumber == true; i++) 
	{ 
		Char = sText.charAt(i);
		if (ValidChars.indexOf(Char) == -1)
		{
			return false;
		}
	}
	return IsNumber;
}