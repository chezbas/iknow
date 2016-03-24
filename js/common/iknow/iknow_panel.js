	function iknow_panel_generer(id)
	{
		
		var panel_html = null;
		var end_div = '</div>';
		
		panel_html =  '<div class="iknow_panel_header_hg"  onclick="iknow_toggle_el();" onMouseDown="return false;">';
		panel_html += '<div class="iknow_panel_header_hd">';
		panel_html += '<div class="iknow_panel_header_hm">';
		panel_html += '<div class="iknow_panel_header_c">';
		panel_html += '<div id="iknow_ctrl_arrow"></div>';
		panel_html += '<div id="iknow_ctrl_title"></div>';
		panel_html += '<div id="iknow_header_ss_titre"></div>';
		panel_html += end_div+end_div+end_div+end_div;		
		panel_html += '<div id="iknow_panel_content">';
		panel_html += '<div class="iknow_panel_content_hg">';
		panel_html += '<div class="iknow_panel_content_hd">';
		panel_html += '<div id="iknow_ctrl_info_conteneur">';
		panel_html += '<div id="iknow_ctrl_info"></div>';
		panel_html += end_div+end_div+end_div;					
		panel_html += '<div class="iknow_panel_content_bg">';
		panel_html += '<div class="iknow_panel_content_bd">';
		panel_html += '<div class="iknow_panel_content_bm"></div>';
		panel_html += end_div+end_div+end_div;	
	
		document.getElementById(id).innerHTML = panel_html;
	}

	function iknow_panel_get_height()
	{
		//document.getElementById('iknow_ctrl_container').style.display = 'block'; 
		var panel_size = document.getElementById('iknow_ctrl_internal_container').offsetHeight;
		//document.getElementById('iknow_ctrl_container').style.display = 'hidden';
		if(panel_size > 200)
		{
			return 200;
		}
		else
		{
			return panel_size;
		}
			
	}
	/**
	* Changement du contenu, du titre et du sous titre du panel
	*
	* @param {String} contenu Contenu du panel à afficher
	* @param {String} titre Titre du panel à afficher (optionnel)
	* @param {String} sous_titre Sous-titre du panel à afficher (optionnel)
	*/
	function iknow_panel_set_cts(contenu,titre,sous_titre)
	{
		// Mise en place du contenu
		document.getElementById('iknow_ctrl_internal_container').innerHTML = contenu;
	
		if(typeof(titre) != 'undefined')
		{
			// Mise en place du titre
			document.getElementById('iknow_ctrl_title').innerHTML = titre;
		}
		
		if(typeof(sous_titre) != 'undefined')
		{
			// Mise en place du sous-titre
			document.getElementById('iknow_header_ss_titre').innerHTML = sous_titre;
		}
	}
	
	
	function iknow_panel_reduire()
	{
		iknow_collapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
	}
	
	/**
	* Changement du titre du panel
	*
	* @param {String} titre Titre du panel à afficher
	*/
	function iknow_panel_set_titre(titre)
	{
		// Mise en place du titre
		document.getElementById('icode_title').innerHTML = titre;
	}
	
	/**
	* Changement du sous-titre du panel
	*
	* @param {String} sous_titre Sous-titre du panel à afficher
	*/
	function iknow_panel_set_sous_titre(sous_titre)
	{
		// Mise en place du sous-titre
		document.getElementById('iknow_header_ss_titre').innerHTML = sous_titre;
	}
	
	/**
	* Changement de la partie action du panel
	*
	* @param {String} action action en cours à afficher
	*/
	function iknow_panel_set_action(libelle,action)
	{
		// Mise en place du sous-titre
		/*if(document.getElementById('iknow_log_internal_container').innerHTML == '' && libelle != '')
		{
			//document.getElementById('iknow_log_internal_container').style.display = 'block';
		}
		*/
		//document.getElementById('iknow_log_internal_container').innerHTML = '<table><tr>'+action+'</tr></table>';

		date_du_jour = new Date();
		heure = date_du_jour.getHours();
		if(heure < 10) heure = "0" + heure;
		
		minute = date_du_jour.getMinutes();
		if(minute < 10) minute = "0" + minute;
		
		seconde = date_du_jour.getSeconds();
		if(seconde < 10) seconde = "0" + seconde;
		
		document.getElementById('iknow_log_internal_container').innerHTML = '<table id="table_log_footer"><tr><td nowrap=nowrap><b>'+heure+':'+minute+':'+seconde+'</b> -  '+libelle+'</td><td style="width:15px;">&nbsp;</td></tr></table>'+document.getElementById('iknow_log_internal_container').innerHTML;
	}
	
	/**
	* Changement du contenu, du titre et du sous titre du panel
	*
	* @param {String} contenu Contenu du panel à afficher
	* @param {String} titre Titre du panel à afficher (optionnel)
	* @param {String} sous_titre Sous-titre du panel à afficher (optionnel)
	*/
	function iknow_panel_add_more_content(contenu)
	{
		// Mise en place du contenu
		document.getElementById('iknow_ctrl_internal_container').innerHTML += contenu;
	}