/**
 * Go to the previous or next page
 * @param vimofy_id id of the vimofy
 * @param type type of move (__VIMOFY_NEXT__,__VIMOFY_PREVIOUS__,__VIMOFY_FIRST__,__VIMOFY_LAST__,number)
 * @param ajax_return response of ajax call
 */
function vimofy_page_change_ajax(vimofy_id,type,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		vimofy_display_wait(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&type='+type+'&action=1&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_page_change_ajax';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+type+"'";
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			var json = get_json(ajax_return);
			// Set the content of the vimofy
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			
			// Update the json object
			eval('Vimofy.'+vimofy_id+'.total_page = '+json.vimofy.total_page+';');
			eval('Vimofy.'+vimofy_id+'.active_page = '+json.vimofy.active_page+';');
			eval('Vimofy.'+vimofy_id+'.selected_line = new Object;');
			eval(decodeURIComponent(json.vimofy.json_line));
			
			vimofy_display_wait(vimofy_id);
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
	}
}

/**
 * Line per page change
 * @param vimofy_id id of the vimofy
 * @param qtt line per page
 * @param ajax_return response of ajax call
 */
function vimofy_input_line_per_page_change_ajax(vimofy_id,qtt,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		vimofy_display_wait(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&qtt='+qtt+'&action=11&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_input_line_per_page_change_ajax';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+qtt+"'";
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			eval('Vimofy.'+vimofy_id+'.qtt_line = '+qtt);
			var json = get_json(ajax_return);
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			// Update the json object
			eval(decodeURIComponent(json.vimofy.json));
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
		
		vimofy_display_wait(vimofy_id);
	}
}


/**
 * Refresh the current page
 * @param vimofy_id id of the vimofy
 * @param ajax_return response of ajax call
 */
function vimofy_refresh_page_ajax(vimofy_id,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		vimofy_display_wait(vimofy_id);
		
		vimofy_execute_event(__ON_REFRESH__,__BEFORE__,vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=2&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_refresh_page_ajax';
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
			
			// Update the json object
			eval(decodeURIComponent(json.vimofy.json));
			
			// Set the content of the Vimofy
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			
			// Hide the wait div
			vimofy_hide_wait(vimofy_id);
			
			vimofy_execute_event(__ON_REFRESH__,__AFTER__,vimofy_id);
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
	}
}

/**
 * Reset the filter on all column
 * @param vimofy_id id of the vimofy
 * @param ajax_return response of ajax call
 */
function vimofy_reset(vimofy_id,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		vimofy_display_wait(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	

		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=9';
		conf['fonction_a_executer_reponse'] = 'vimofy_reset';
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
			
			// Update the json object
			eval(decodeURIComponent(json.vimofy.json));
			
			// Set the content of the Vimofy
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			if(decodeURIComponent(json.vimofy.edit_mode) == __EDIT_MODE__)
			{
				// Set the content of the toolbar
				vimofy_set_innerHTML('vimofy_toolbar_'+vimofy_id,decodeURIComponent(json.vimofy.toolbar));
				document.getElementById('vimofy_td_toolbar_edit_'+vimofy_id).className = 'btn_toolbar grey_el';
			}
			
			
			
			// Hide the wait div
			vimofy_display_wait(vimofy_id);
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
	}
}

function vimofy_input_page_change(evt,vimofy_id,element)
{
	var evt = (evt)?evt : event;
	
	if(evt.which == 13)
	{
		if(element.value == '') element.value = 1;
		vimofy_page_change_ajax(vimofy_id,element.value);
	}
	else
	{
		if(!isNaN(element.value))
		{
			var max = eval('Vimofy.'+vimofy_id+'.total_page');
			if(element.value > max)
			{
				element.value = max;
			}
			else if (element.value < 1 && element.value != '') 
			{
				element.value = 1;
			}

		}
		else
		{
			element.value = 1;
		}
		
			
	}
}


function vimofy_input_page_change(evt,vimofy_id,element)
{
	var evt = (evt)?evt : event;
	
	if(evt.which == 13)
	{
		if(element.value == '') element.value = 1;
		vimofy_page_change_ajax(vimofy_id,element.value);
	}
	else
	{
		if(!isNaN(element.value) && element.value.indexOf('.') == -1)
		{
			var max = eval('Vimofy.'+vimofy_id+'.total_page');
			if(element.value > max)
			{
				element.value = max;
			}
			else if (element.value < 1 && element.value != '') 
			{
				element.value = 1;
			}

		}
		else
		{
			element.value = 1;
		}
	}
}

function vimofy_input_line_per_page_change(evt,vimofy_id,element)
{
	var evt = (evt)?evt : event;
	
	if(evt.which == 13)
	{
		// Enter key pressed
		if(element.value == '')
		{
			// Force the value to 1
			element.value = 1;
			document.getElementById(vimofy_id+'_line_selection_footer').value = 1;
		}
		
		// Change the number of line per page
		vimofy_input_line_per_page_change_ajax(vimofy_id,element.value);
	}
	else
	{
		if(isNaN(element.value) || element.value.indexOf('.') != -1)
		{
			element.value = eval('Vimofy.'+vimofy_id+'.qtt_line');
			document.getElementById(vimofy_id+'_line_selection_footer').value = eval('Vimofy.'+vimofy_id+'.qtt_line');
		}
		else if(element.value > eval('Vimofy.'+vimofy_id+'.max_line_per_page'))
		{
			element.value = eval('Vimofy.'+vimofy_id+'.max_line_per_page');
			document.getElementById(vimofy_id+'_line_selection_footer').value = eval('Vimofy.'+vimofy_id+'.max_line_per_page');
		}
	}
}


/**
 * Return the selected lines in a json object.
 */
function get_selected_lines(vimofy_id)
{
	return JSON.stringify(eval('Vimofy.'+vimofy_id+'.selected_line'));
}

/**
 * @param vimofy_id
 * @return
 */
function count_selected_lines(vimofy_id)
{
	var i = 0;
	for(var iterable_element in eval('Vimofy.'+vimofy_id+'.selected_line'))
	{
		if(eval('Vimofy.'+vimofy_id+'.selected_line.'+iterable_element+'.selected') == true)
		{
			i++;
		}
	}
	return i;
}