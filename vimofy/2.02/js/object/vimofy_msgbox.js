	function vimofy_msgbox_hover(id,vimofy_id)
    {
    	document.getElementById('btn_'+vimofy_id+'_ok_g'+id).style.backgroundPosition = '0 -63px';
    	document.getElementById('btn_'+vimofy_id+'_ok_m'+id).style.backgroundPosition = '0 -105px';
    	document.getElementById('btn_'+vimofy_id+'_ok_d'+id).style.backgroundPosition = '0 -84px';
    }
    
    function vimofy_msgbox_out(id,vimofy_id)
    {
    	document.getElementById('btn_'+vimofy_id+'_ok_g'+id).style.backgroundPosition = '0 0px';
    	document.getElementById('btn_'+vimofy_id+'_ok_m'+id).style.backgroundPosition = '0 -42px';
    	document.getElementById('btn_'+vimofy_id+'_ok_d'+id).style.backgroundPosition = '0 -21px';
    }
    
    /**
     * 
     * @param titre
     * @param contenu
     * @param icone
     * @param type confirm,prompt,password,wait,msg
     * @return
     */
    function vimofy_generer_msgbox(vimofy_id,titre,contenu,icone,type,aff_btn,empecher_fermeture)
    {
    	
    	var msgbox_html = null;
    	var focus = null;
    	
    	if(typeof(aff_btn) == 'undefined')
    	{
    		aff_btn = false;
    	}
    	if(typeof(empecher_fermeture) == 'undefined')
    	{
    		empecher_fermeture = false;
    	}	
    	
    	contenu = '<span id="vimofy_'+vimofy_id+'_msgbox_contenu_texte">'+contenu+'</span>';
    	msgbox_html = '<div class="vimofy_msgbox_hg">';
    	msgbox_html += '<div class="vimofy_msgbox_hd">';
    	msgbox_html += '<div class="vimofy_msgbox_hm">';
    	msgbox_html += '<div class="vimofy_msgbox_c">';
    	if(empecher_fermeture == false)
    	{
    		msgbox_html += '<div class="vimofy_msgbox_btn_quitter" onclick="vimofy_cover_with_filter(\''+vimofy_id+'\');"></div>';
    	}
    	msgbox_html += '<div class="vimofy_msgbox_titre">'+titre+'</div>';
    	if(icone == '')
    	{
    		msgbox_html += '<div class="vimofy_msgbox_message">';
    	}
    	else
    	{
    		msgbox_html += '<div class="vimofy_msgbox_icon_'+icone+' vimofy_msgbox_message">';
    	}
    	
    	switch (type) 
    	{
			case 'confirm':
				//aff_btn = true;
				break;
			case 'prompt':
				contenu += '<div class="vimofy_msgbox_prompt"><input type="text" id="vimofy_'+vimofy_id+'_msgbox_prompt_value" onkeydown="vimofy_msgbox_keypress(event,\''+vimofy_id+'\');"/></div>';
				focus = 'vimofy_'+vimofy_id+'_msgbox_prompt_value';
				break;
			case 'password':
				contenu += '<div class="vimofy_msgbox_prompt"><input type="password" id="vimofy_'+vimofy_id+'_msgbox_prompt_value"/></div>';
				focus = 'vimofy_'+vimofy_id+'_msgbox_prompt_value';
				break;
			case 'wait':
				contenu += '<div class="vimofy_msgbox_wait"></div>';
				break;
			case 'msg':
				break;
			default:
				break;
		}
    	msgbox_html += '<div class="vimofy_msgbox_contenu" id="vimofy_'+vimofy_id+'_msgbox_contenu">'+contenu+'</div>';
    	
    	if(aff_btn)
    	{
    		msgbox_html += '<div style="text-align:center;">';
	    	msgbox_html += '<table class="vimofy_msgbox_tab_cont_btn">';
	    	msgbox_html += '<tr>';
	    	// Mise en place des boutons
	    	for ( var i = 0; i < aff_btn[0].length; i++) 
	    	{
    			msgbox_html += '<td>';
    	    	msgbox_html += '<table class="vimofy_msgbox_tab_btn">';
    	    	msgbox_html += '<tr id="vimofy_'+vimofy_id+'_msgbox_btn'+i+'" onmouseover="vimofy_msgbox_hover('+i+',\''+vimofy_id+'\');" onmouseout="vimofy_msgbox_out('+i+',\''+vimofy_id+'\');" onMouseDown="return false;" onclick="javascript:'+aff_btn[1][i]+'">';
    	    	msgbox_html += '<td id="btn_'+vimofy_id+'_ok_g'+i+'" class="btn_ok_g"></td>';
    	    	msgbox_html += '<td id="btn_'+vimofy_id+'_ok_m'+i+'" class="btn_ok_m">'+aff_btn[0][i]+'</td>';
    	    	msgbox_html += '<td id="btn_'+vimofy_id+'_ok_d'+i+'" class="btn_ok_d"></td>';
    	    	msgbox_html += '</tr>';
    	    	msgbox_html += '</table>';
    	    	msgbox_html += '</td>';
			}
	    	msgbox_html += '</tr>';
	    	msgbox_html += '</table>';
	    	msgbox_html += '</div>	';
    	}
    	msgbox_html += '</div>';
    	msgbox_html += '</div>';	
    	msgbox_html += '</div>';		
    	msgbox_html += '</div>';			
    	msgbox_html += '</div>';				
    	msgbox_html += '<div class="vimofy_msgbox_content_bg">';				
    	msgbox_html += '<div class="vimofy_msgbox_content_bd">';				
    	msgbox_html += '<div class="vimofy_msgbox_content_bm"></div>';			
    	msgbox_html += '</div>';		
    	msgbox_html += '</div>';	
    	
    	// Affichage du fond
    	//document.getElementById('vim_msgbox_background_'+vimofy_id).style.display = 'block';
  
    	// Creation de la messagebox
    	document.getElementById('vim_msgbox_conteneur_'+vimofy_id).innerHTML = msgbox_html;

    	// Affichage de la messagebox
    	document.getElementById('vim_msgbox_conteneur_'+vimofy_id).style.display = '';
    	
    	// Focus
    	if(focus != null)
    	{	
    		document.getElementById(focus).focus();
    	}

    	if(type == 'wait')
    	{
    		document.getElementById('vimofy_'+vimofy_id+'_msgbox_contenu').style.margin = '0';
    	}
    }
    
    function vimofy_close_msgbox(vimofy_id)
    {
 
    	// Suppression de la messagebox
    	document.getElementById('vim_msgbox_conteneur_'+vimofy_id).innerHTML = '';
 
    	// Masquage de la messagebox
    	document.getElementById('vim_msgbox_conteneur_'+vimofy_id).style.display = 'none';
    	
    	// Masquage du fond
    	//document.getElementById('vim_msgbox_background_'+vimofy_id).style.display = 'none';
    	
    }
    
    
    function vimofy_changer_message_msgbox(message,vimofy_id)
    { 	
    	document.getElementById('vimofy_'+vimofy_id+'_msgbox_contenu_texte').innerHTML = message;  	
    }

    function vimofy_msgbox_keypress(code_touche,vimofy_id)
    {
    	if(code_touche.keyCode == 13)
		{
    		// Enter key, valid the msgbox
    		eval(document.getElementById('vimofy_'+vimofy_id+'_msgbox_btn0').onclick+'  onclick();');
		}
    	else if(code_touche.keyCode == 27)
    	{
    		// Esc key, close the msgbox
    		eval(document.getElementById('vimofy_'+vimofy_id+'_msgbox_btn1').onclick+'  onclick();');
    	}
    	else
    	{
    		if(code_touche.keyCode == 27) eval(document.getElementById('vimofy_'+vimofy_id+'_msgbox_btn1').onclick+'onclick();return false;');
    	}
    }
    

    function vimofy_collapse_menu(id)
    {
    	if(document.getElementById(id).style.display == 'block')
    	{
    		document.getElementById(id).style.display = 'none';
    	}
    	else
    	{
    		document.getElementById(id).style.display = 'block';
    	}
    }
