	function iknow_msgbox_hover(id)
    {
    	document.getElementById('btn_ok_g'+id).style.backgroundPosition = '0 -63px';
    	document.getElementById('btn_ok_m'+id).style.backgroundPosition = '0 -105px';
    	document.getElementById('btn_ok_d'+id).style.backgroundPosition = '0 -84px';
    }
    
    function iknow_msgbox_out(id)
    {
    	document.getElementById('btn_ok_g'+id).style.backgroundPosition = '0 0px';
    	document.getElementById('btn_ok_m'+id).style.backgroundPosition = '0 -42px';
    	document.getElementById('btn_ok_d'+id).style.backgroundPosition = '0 -21px';
    }
    
    /**
     * 
     * @param titre
     * @param contenu
     * @param icone
     * @param type confirm,prompt,password,wait,msg
     * @return
     */
    function generer_msgbox(titre,contenu,icone,type,aff_btn,empecher_fermeture)
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
    	
    	contenu = '<span id="iknow_msgbox_contenu_texte">'+contenu+'</span>';
    	msgbox_html = '<div class="iknow_msgbox_hg">';
    	msgbox_html += '<div class="iknow_msgbox_hd">';
    	msgbox_html += '<div class="iknow_msgbox_hm">';
    	msgbox_html += '<div class="iknow_msgbox_c">';
    	if(empecher_fermeture == false)
    	{
    		msgbox_html += '<div id="iknow_msgbox_btn_quitter" onclick="close_msgbox();"></div>';
    	}
    	msgbox_html += '<div id="iknow_msgbox_titre">'+titre+'</div>';
    	if(icone == '')
    	{
    		msgbox_html += '<div id="iknow_msgbox_message">';
    	}
    	else
    	{
    		msgbox_html += '<div id="iknow_msgbox_message" class="iknow_msgbox_icon_'+icone+'">';
    	}
    	
    	switch (type) 
    	{
			case 'confirm':
				//aff_btn = true;
				break;
			case 'prompt':
				contenu += '<div id="iknow_msgbox_prompt"><input type="text" id="iknow_msgbox_prompt_value" onkeydown="iknow_msgbox_keypress(event);"/></div>';
				focus = 'iknow_msgbox_prompt_value';
				//aff_btn = new Array(["ok","Annuler"],["f_ok(document.getElementById('iknow_msgbox_prompt_value').value);close_msgbox();","close_msgbox();"]);
				break;
			case 'password':
				contenu += '<div id="iknow_msgbox_prompt"><input type="password" id="iknow_msgbox_prompt_value"/></div>';
				focus = 'iknow_msgbox_prompt_value';
				//aff_btn = new Array(["ok","Annuler"],["f_ok(document.getElementById('iknow_msgbox_prompt_value').value);close_msgbox();","close_msgbox();"]);
				break;
			case 'wait':
				contenu += '<div id="iknow_msgbox_wait"></div>';
				//aff_btn = false;
				break;
			case 'msg':
				//aff_btn = new Array(["ok","Annuler","Valider"],["close_msgbox();","close_msgbox();","alert('Valider');close_msgbox();"]);
				break;
			default:
				//aff_btn = false;
				break;
		}
    	msgbox_html += '<div id="iknow_msgbox_contenu">'+contenu+'</div>';
    	
    	if(aff_btn)
    	{
    		msgbox_html += '<div style="text-align:center;">';
	    	msgbox_html += '<table class="iknow_msgbox_tab_cont_btn">';
	    	msgbox_html += '<tr>';
	    	// Mise en place des boutons
	    	for ( var i = 0; i < aff_btn[0].length; i++) 
	    	{
    			msgbox_html += '<td>';
    	    	msgbox_html += '<table class="iknow_msgbox_tab_btn">';
    	    	msgbox_html += '<tr id="iknow_msgbox_btn'+i+'" onmouseover="iknow_msgbox_hover('+i+');" onmouseout="iknow_msgbox_out('+i+');" onMouseDown="return false;" onclick="javascript:'+aff_btn[1][i]+'">';
    	    	msgbox_html += '<td id="btn_ok_g'+i+'" class="btn_ok_g"></td>';
    	    	msgbox_html += '<td id="btn_ok_m'+i+'" class="btn_ok_m">'+aff_btn[0][i]+'</td>';
    	    	msgbox_html += '<td id="btn_ok_d'+i+'" class="btn_ok_d"></td>';
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
    	msgbox_html += '<div class="iknow_msgbox_content_bg">';				
    	msgbox_html += '<div class="iknow_msgbox_content_bd">';				
    	msgbox_html += '<div class="iknow_msgbox_content_bm"></div>';			
    	msgbox_html += '</div>';		
    	msgbox_html += '</div>';	
    	
    	// Affichage du fond
    	document.getElementById('iknow_msgbox_background').style.display = 'block';
  
    	// Creation de la messagebox
    	document.getElementById('iknow_msgbox_conteneur').innerHTML = msgbox_html;
    	
    	// Affichage de la messagebox
    	document.getElementById('iknow_msgbox_conteneur').style.display = '';
    	
    	// Focus
    	if(focus != null)
    	{	
    		document.getElementById(focus).focus();
    	}

    	if(type == 'wait')
    	{
    		document.getElementById('iknow_msgbox_contenu').style.margin = '0';
    	}
    }
    
    function close_msgbox()
    {
 
    	// Suppression de la messagebox
    	document.getElementById('iknow_msgbox_conteneur').innerHTML = '';
 
    	// Masquage de la messagebox
    	document.getElementById('iknow_msgbox_conteneur').style.display = 'none';
    	
    	// Masquage du fond
    	document.getElementById('iknow_msgbox_background').style.display = 'none';
    	
    }
    
    
    function changer_message_msgbox(message)
    { 	
    	document.getElementById('iknow_msgbox_contenu_texte').innerHTML = message;  	
    }

    function iknow_msgbox_keypress(code_touche)
    {
    	try 
    	{
	    	if(code_touche.keyCode == 13)
			{
	    		eval(document.getElementById('iknow_msgbox_btn0').onclick+';onclick();');
			}
	    	else
	    	{
	    		if(code_touche.keyCode == 27) eval(document.getElementById('iknow_msgbox_btn1').onclick+';onclick();');
	    	}
    	} catch (e) {
			// TODO: handle exception
		}
    }
    

    function collapse_menu(id)
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
