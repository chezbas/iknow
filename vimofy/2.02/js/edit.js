function edit_lines(evt,line,vimofy_id,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		if(count_selected_lines(vimofy_id) > 0 || line != null)
		{
			vimofy_StopEventHandler(evt);
			
			if(line != null && eval('Vimofy.'+vimofy_id+'.return_mode') != __SIMPLE__ && !document.getElementById('chk_l'+line+'_c0_'+vimofy_id).checked)
			{
				// Edit button was clicked on the line
				vimofy_checkbox(line,evt,null,vimofy_id);	
			}
			else
			{
				if(line != null)
				{
					document.getElementById('l'+line+'_'+vimofy_id).className = 'line_selected_color_'+((line - 1)%eval('Vimofy.'+vimofy_id+'.qtt_color'))+'_'+vimofy_id;
					document.getElementById('chk_l'+line+'_c0_'+vimofy_id).checked = true;
					eval('Vimofy.'+vimofy_id+'.selected_line.L'+line+' = Vimofy.'+vimofy_id+'.lines.L'+line+';');
					eval('Vimofy.'+vimofy_id+'.selected_line.L'+line+'.selected = true;');
				}
			}
			
			// Display the wait div
			vimofy_display_wait(vimofy_id);

			/**==================================================================
			 * Ajax init
			 ====================================================================*/
			var conf = new Array();	

			conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
			conf['delai_tentative'] = 15000;
			conf['max_tentative'] = 4;
			conf['type_retour'] = false;		// ReponseText
			conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=13&lines='+encodeURIComponent(get_selected_lines(vimofy_id));
			conf['fonction_a_executer_reponse'] = 'edit_lines';
			conf['param_fonction_a_executer_reponse'] = "'"+evt+"',"+line+",'"+vimofy_id+"'";
			
			ajax_call(conf);
			/**==================================================================*/
		}
		else
		{
			msgbox(vimofy_id,vim_lib[53],vim_lib[52]);
		}
	}
	else
	{
		try 
		{
			// Get the ajax return in json format
			var json = get_json(ajax_return);

			// Update the json
			eval(decodeURIComponent(json.vimofy.json));
			
			// Set the content of the vimofy
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			
			// Set the content of the toolbar
			vimofy_set_innerHTML('vimofy_toolbar_'+vimofy_id,decodeURIComponent(json.vimofy.toolbar));
			
			// Set the focus to the default input
			if(eval('Vimofy.'+vimofy_id+'.default_input_focus') != false)
			{
				document.getElementById('th_input_'+eval('Vimofy.'+vimofy_id+'.default_input_focus')+'__'+vimofy_id).focus();
			}

			// Hide the wait div
			vimofy_display_wait(vimofy_id);
			
			document.getElementById('vimofy_table_mask_'+vimofy_id).style.display = 'block';
		} 
		catch(e)
		{
			vimofy_display_error(vimofy_id,e,ajax_return);
		}
	}
}

function vimofy_cancel_edit(vimofy_id,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		// Display the wait div
		vimofy_display_wait(vimofy_id);
		
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 3000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=20';
		conf['fonction_a_executer_reponse'] = 'vimofy_cancel_edit';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"'";

		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try
		{
			// Get the ajax return in json format
			var json = get_json(ajax_return);

			// Update the json
			eval(decodeURIComponent(json.vimofy.json));
			
			// Set the content of the vimofy
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			
			// Set the content of the toolbar
			vimofy_set_innerHTML('vimofy_toolbar_'+vimofy_id,decodeURIComponent(json.vimofy.toolbar));
			

			// Hide the wait div
			vimofy_display_wait(vimofy_id);
		} 
		catch (e)
		{
			
		}
	}
}
function delete_lines(vimofy_id,confirm,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		if(count_selected_lines(vimofy_id) > 0)
		{
			if(!confirm)
			{
				vimofy_cover_with_filter(vimofy_id);
				var prompt_btn = new Array([vim_lib[31],vim_lib[32]],["delete_lines('"+vimofy_id+"',true)","vimofy_cover_with_filter('"+vimofy_id+"');"]);
				
				document.getElementById('vim_msgbox_conteneur_'+vimofy_id).style.display = '';
				vimofy_generer_msgbox(vimofy_id,vim_lib[54],vim_lib[55].replace('$x',count_selected_lines(vimofy_id)),'info','msg',prompt_btn);
			}
			else
			{
				vimofy_execute_event(__ON_DELETE__,__BEFORE__,vimofy_id);
				
				// Hide the promptbox
				vimofy_cover_with_filter(vimofy_id);
				
				// Display the wait div
				vimofy_display_wait(vimofy_id);
				
				/**==================================================================
				 * Ajax init
				 ====================================================================*/	
				var conf = new Array();	
	
				conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
				conf['delai_tentative'] = 15000;
				conf['max_tentative'] = 4;
				conf['type_retour'] = false;		// ReponseText
				conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=15&lines='+encodeURIComponent(get_selected_lines(vimofy_id));
				conf['fonction_a_executer_reponse'] = 'delete_lines';
				conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"',true";
		
				ajax_call(conf);
				/**==================================================================*/
			}
		}
		else
		{
			msgbox(vimofy_id,vim_lib[51],vim_lib[52]);
		}
	}
	else
	{
		try 
		{
			// Get the ajax return in json format
			var json = get_json(ajax_return);
			
			// Update the json
			eval(decodeURIComponent(json.vimofy.json));
			
			// Set the content of the Vimofy
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			
			vimofy_set_innerHTML('vimofy_toolbar_'+vimofy_id,decodeURIComponent(json.vimofy.toolbar));

			// Hide the cover div
			var theme = eval('Vimofy.'+vimofy_id+'.theme');
			document.getElementById('vim__'+theme+'__hide_container_'+vimofy_id+'__').style.display = 'none';
			
			// Erase the content of the wait div
			vimofy_set_innerHTML('vim__'+theme+'__wait_'+vimofy_id+'__','');
			
			// Erase the content of the msgbox div
			vimofy_set_innerHTML('vim_msgbox_conteneur_'+vimofy_id,'');
			
			vimofy_execute_event(__ON_DELETE__,__AFTER__,vimofy_id);
		} 	
		catch(e)
		{
			vimofy_display_error(vimofy_id,e);
			alert(ajax_return);
		}
	}
}

function add_line(vimofy_id,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		vimofy_execute_event(__ON_ADD__,__BEFORE__,vimofy_id);
		
		// Display the wait div
		vimofy_display_wait(vimofy_id);
		
		/**==================================================================
		 * Get the value of the columns, only change column (with checked checkbox)
		 ====================================================================*/	
		var val = new Object();
		for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns')) 
		{
			var i = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id');
			if(i != undefined)
			{
				eval('val.'+iterable_element+' = new Object();');
				eval('val.'+iterable_element+'.value = \''+protect_json(document.getElementById('th_input_'+i+'__'+vimofy_id).value)+'\';');
				eval('val.'+iterable_element+'.id = '+i+';');
			}
		}
		/**==================================================================*/
		
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=16&val_json='+encodeURIComponent(JSON.stringify(val));
		conf['fonction_a_executer_reponse'] = 'add_line';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"'";

		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			// Hide the wait div
			vimofy_display_wait(vimofy_id);
			
			// Get the ajax return in json format
			var json = get_json(ajax_return);

			if(json.vimofy.error == 'false')
			{
				// An error has occured
				eval('var test = '+decodeURIComponent(json.vimofy.error_col));
				for(var iterable_element in test) 
				{
					if(eval('test.'+iterable_element+'.status') == __FORBIDEN__)
					{
						// Forbiden
						document.getElementById('th_input_'+eval('test.'+iterable_element+'.id')+'__'+vimofy_id).value = '';
						document.getElementById('th_input_'+eval('test.'+iterable_element+'.id')+'__'+vimofy_id).disabled = 'true';
					}
					else
					{
						// Required
						document.getElementById('th_input_'+eval('test.'+iterable_element+'.id')+'__'+vimofy_id).style.backgroundColor = '#FFD4D4';
					}
				}
				
				vimofy_cover_with_filter(vimofy_id);
				prompt_btn = new Array([vim_lib[31]],["vimofy_cover_with_filter('"+vimofy_id+"');"]);
				
				document.getElementById('vim_msgbox_conteneur_'+vimofy_id).style.display = '';
				vimofy_generer_msgbox(vimofy_id,vim_lib[56],decodeURIComponent(json.vimofy.error_str),'erreur','msg',prompt_btn);
			}
			else
			{
				// No error
				
				// Update the json
				eval(decodeURIComponent(json.vimofy.json));
	
				// Set the content of the Vimofy
				vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
				
				vimofy_set_innerHTML('vimofy_toolbar_'+vimofy_id,decodeURIComponent(json.vimofy.toolbar));
			}
			
			document.getElementById('wait_input_'+vimofy_id).style.display = 'none';
			
			vimofy_execute_event(__ON_ADD__,__AFTER__,vimofy_id);
			
		} 
		catch(e) 
		{
			alert(ajax_return);
			vimofy_display_error(vimofy_id,e);
		}
	}
}

function save_lines(vimofy_id,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		// Display the wait div
		vimofy_display_wait(vimofy_id);
		
		vimofy_execute_event(__ON_UPDATE__,__BEFORE__,vimofy_id);
		
		/**==================================================================
		 * Get the value of the columns, only change column (with checked checkbox)
		 ====================================================================*/	
		var val = new Object();
		for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns')) 
		{
			var i = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id');
			if(i != undefined)
			{
				if(document.getElementById('chk_edit_c'+i+'_'+vimofy_id) && document.getElementById('chk_edit_c'+i+'_'+vimofy_id).checked == true)
				{
					eval('val.'+iterable_element+' = new Object();');
					eval('val.'+iterable_element+'.value = \''+protect_json(document.getElementById('th_input_'+i+'__'+vimofy_id).value)+'\';');
					eval('val.'+iterable_element+'.id = '+i+';');
				}
			}
		}
		/**==================================================================*/

		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();

		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=14&val_json='+encodeURIComponent(JSON.stringify(val));
		conf['fonction_a_executer_reponse'] = 'save_lines';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"'";

		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			// Get the ajax return in json format
			var json = get_json(ajax_return);
			
			// Update the json
			eval(decodeURIComponent(json.vimofy.json));

			// Set the content of the Vimofy
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			
			vimofy_set_innerHTML('vimofy_toolbar_'+vimofy_id,decodeURIComponent(json.vimofy.toolbar));
			
			document.getElementById('wait_input_'+vimofy_id).style.display = 'none';
			
			vimofy_execute_event(__ON_UPDATE__,__AFTER__,vimofy_id);
		} 
		catch(e)
		{
			alert(ajax_return);
			vimofy_display_error(vimofy_id,e);
		}
		
		// Hide the wait div
		vimofy_display_wait(vimofy_id);
	}
}

function protect_json(p_value)
{
	p_value = p_value.replace(new RegExp('\\\\','g'),"\\\\");
	p_value = p_value.replace(new RegExp("'",'g'),"\\'");
	return p_value;
}