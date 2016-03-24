/**
 * Change the order of the column
 * @param vimofy_id id of the vimofy
 * @param ajax_return response of ajax call
 */
function vimofy_column_order(vimofy_id,type_order,column,mode,ajax_return)
{
	
	if(typeof(ajax_return) == 'undefined')
	{
		eval('Vimofy.'+vimofy_id+'.stop_click_event = true;');

		vimofy_display_wait(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=4&column='+column+'&order='+type_order+'&mode='+mode+'&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_column_order';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+type_order+"',"+column+",'"+mode+"'";
		
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			// Change the order status
			// Get the ajax return in json format
			var json = get_json(ajax_return);
		
			// Update the json object
			eval(decodeURIComponent(json.vimofy.json));
			eval(decodeURIComponent(json.vimofy.json_line));
			vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
			
			vimofy_display_wait(vimofy_id);
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
	}
}


/**
 * Change the search mode of the column
 * @param vimofy_id id of the vimofy
 * @param ajax_return response of ajax call
 */
function vimofy_change_search_mode(vimofy_id,type_search,column,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		eval('Vimofy.'+vimofy_id+'.stop_click_event = true;');

		vimofy_display_wait(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=8&column='+column+'&type_search='+type_search+'&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_change_search_mode';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+type_search+"',"+column;
		
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
			vimofy_display_wait(vimofy_id);
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
	}
}

/**
 * Change the alignment mode of the column
 * @param vimofy_id id of the vimofy
 * @param ajax_return response of ajax call
 */
function vimofy_change_col_alignment(vimofy_id,type_alignment,column,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		eval('Vimofy.'+vimofy_id+'.stop_click_event = true;');

		vimofy_display_wait(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=12&column='+column+'&type_alignment='+type_alignment+'&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_change_col_alignment';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+type_alignment+"',"+column;
		
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
			vimofy_display_wait(vimofy_id);
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
	}
}

/**
 * Hide or display a column
 * @param vimofy_id id of the vimofy
 * @param column id of the column
 * @param ajax_return response of ajax call
 */
function vimofy_toggle_column(vimofy_id,column,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		eval('Vimofy.'+vimofy_id+'.stop_click_event = true;');

		vimofy_display_wait(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=10&column='+column+'&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_toggle_column';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"',"+column;
		
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
			vimofy_display_wait(vimofy_id);
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
	}
}

/**
 * Go to the previous or next page
 * @param vimofy_id id of the vimofy
 * @param ajax_return response of ajax call
 */
function move_column_ajax(vimofy_id,ajax_return)
{
	if(eval('Vimofy.'+vimofy_id+'.destination_column;') != eval('undefined'))
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
			conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=3&c_src='+vimofy_column_move+'&c_dst='+eval('Vimofy.'+vimofy_id+'.destination_column;')+'&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
			conf['fonction_a_executer_reponse'] = 'move_column_ajax';
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
				eval(decodeURIComponent(json.vimofy.json_line));
				
				// Set the content of the Vimofy
				vimofy_set_content(vimofy_id,decodeURIComponent(json.vimofy.content));
				
				// Hide the wait div
				vimofy_display_wait(vimofy_id);
				
				eval('Vimofy.'+vimofy_id+'.destination_column = undefined;');
			} 
			catch(e) 
			{
				vimofy_display_error(vimofy_id,e);
			}
			
		}
	}
}

/**
 * Move the clicked column, called when the user move the cursor.
 * @param evt event
 */
function vimofy_move_column(evt)
{
	var evt = (evt)?evt : event;
	
	/**==================================================================
	 * Display the float content
	 ====================================================================*/	
	document.getElementById('vimofy_column_move_div_float_'+vimofy_id_move).style.display = 'block';
	/**==================================================================*/
	
	/**==================================================================
	 * Display the arrow
	 ====================================================================*/	
	document.getElementById('arrow_move_column_top_'+vimofy_id_move).style.display = 'block';
	document.getElementById('arrow_move_column_bottom_'+vimofy_id_move).style.display = 'block';
	/**==================================================================*/
	
	/**==================================================================
	 * Place the float content
	 ====================================================================*/	
	// Get the position of the vimofy
	var pos_el = getElementCoords(document.getElementById('vim__'+eval('Vimofy.'+vimofy_id_move+'.theme')+'__vimofy_table_'+vimofy_id_move+'__'));
	/**==================================================================
	 * Get the position of the cursor	
	 ====================================================================*/	
	var cur_x = evt.clientX - pos_el.left + document.getElementById('liste_'+vimofy_id_move).scrollLeft;
	/**==================================================================*/
	
	// Set the position of the float menu
	document.getElementById('vimofy_column_move_div_float_'+vimofy_id_move).style.top = evt.clientY - pos_el.top+5+'px';
	document.getElementById('vimofy_column_move_div_float_'+vimofy_id_move).style.left = cur_x-document.getElementById('liste_'+vimofy_id_move).scrollLeft+8+'px';
	/**==================================================================*/
	
	var i = 1;
	for(var iterable_element in eval('Vimofy.'+vimofy_id_move+'.columns')) 
	{
		if(cur_x >= eval('Vimofy.'+vimofy_id_move+'.columns.'+iterable_element+'.min') && cur_x <= eval('Vimofy.'+vimofy_id_move+'.columns.'+iterable_element+'.max'))
		{
			document.getElementById('arrow_move_column_top_'+vimofy_id_move).style.left = eval('Vimofy.'+vimofy_id_move+'.columns.'+iterable_element+'.arrow')-5-document.getElementById('liste_'+vimofy_id_move).scrollLeft+'px';
			//document.getElementById('arrow_move_column_top_'+vimofy_id_move).style.left = document.getElementById('right_mark_'+vimofy_column_move+'_'+vimofy_id_move)+'px';
			
			
			document.getElementById('arrow_move_column_bottom_'+vimofy_id_move).style.left = eval('Vimofy.'+vimofy_id_move+'.columns.'+iterable_element+'.arrow')-5-document.getElementById('liste_'+vimofy_id_move).scrollLeft+'px';
			eval('Vimofy.'+vimofy_id_move+'.destination_column = '+iterable_element.replace(/c/g,"")+';');
			// Don't move near the column
			if(i == vimofy_column_move || i == vimofy_column_move + 1)
			{
				document.getElementById('vimofy_column_move_div_float_forbidden_'+vimofy_id_move).className = '__'+eval('Vimofy.'+vimofy_id_move+'.theme')+'__float_forbidden';
			}
			else
			{
				document.getElementById('vimofy_column_move_div_float_forbidden_'+vimofy_id_move).className = '__'+eval('Vimofy.'+vimofy_id_move+'.theme')+'__float_column';
			}
			break;
		}
		i++;
	}	
}

/**
 * Move a column, call when stop the move
 */
function vimofy_move_column_stop()
{
	/**==================================================================
	 * Hide the float content
	 ====================================================================*/	
	document.getElementById('vimofy_column_move_div_float_'+vimofy_id_move).style.display = 'none';
	/**==================================================================*/
	
	/**==================================================================
	 * Initiating global variable
	 ====================================================================*/	
	cursor_start = 0;
	vimofy_column_in_move = false;
	/**==================================================================*/
	
	/**==================================================================
	 * Hide the arrow
	 ====================================================================*/	
	document.getElementById('arrow_move_column_top_'+vimofy_id_move).style.display = 'none';
	document.getElementById('arrow_move_column_bottom_'+vimofy_id_move).style.display = 'none';
	/**==================================================================*/
	document.body.className = vimofy_body_style;
	
	// IE 
	if(typeof(document.body.onselectstart) != "undefined") 
	{
		document.body.onselectstart = null;
	}

	if(vimofy_column_move != eval('Vimofy.'+vimofy_id_move+'.destination_column;') && eval('Vimofy.'+vimofy_id_move+'.destination_column;') != eval('undefined') && vimofy_column_move + 1 != eval('Vimofy.'+vimofy_id_move+'.destination_column;') && eval('Vimofy.'+vimofy_id_move+'.destination_column;') != eval('undefined'))
	{
		move_column_ajax(vimofy_id_move);
	}
	else
	{
		if(eval('Vimofy.'+vimofy_id_move+'.destination_column;') == eval('undefined'))
		{
			click_column_order(vimofy_id_move,vimofy_column_move);
		}
		/**==================================================================*/
	}
}

function click_column_order(vimofy_id,column)
{
	/**==================================================================
	 * Order the column
	 ====================================================================*/	
	if(eval('Vimofy.'+vimofy_id+'.selected_column.ctrl') == true)
	{
		/**==================================================================
		 * Add a new order clause
		 ====================================================================*/	
		if(eval('Vimofy.'+vimofy_id+'.columns.c'+column+'.order') == 'ASC')
		{
			eval('Vimofy.'+vimofy_id+'.columns.c'+column+'.order = "DESC"');
		}
		else
		{
			eval('Vimofy.'+vimofy_id+'.columns.c'+column+'.order = "ASC"');
		}
		
		var mode = __ADD__;
		/**==================================================================*/
	}
	else
	{
		/**==================================================================
		 * Delete other order clause
		 ====================================================================*/	
		var mode = __NEW__;
		for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns')) 
		{
			if(iterable_element == "c"+column)
			{
				if(eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.order') == 'ASC')
				{
					eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.order = "DESC"');
				}
				else
				{
					eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.order = "ASC"');
				}
			}
			else
			{
				eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.order = "";');
			}
		}
		/**==================================================================*/
	}
	
	vimofy_column_order(vimofy_id,eval('Vimofy.'+vimofy_id+'.columns.c'+column+'.order'),column,mode);
}

/**
 * Move a column, call when begin the move
 * @param column id of the column in move
 * @param id Id of the vimofy
 */
function vimofy_move_column_start(evt,column,id)
{
	var evt = (evt)?evt : event;
	/**==================================================================
	 * Initiating global variable
	 ====================================================================*/	
	vimofy_column_move = column;				// The moving column
	vimofy_id_move = id;						// The vimofy
	vimofy_column_in_move = true;				// Flag column move event in progress
	vimofy_body_style = document.body.className;
	/**==================================================================*/
	
	/**==================================================================
	 * Vertical placement of the arrow
	 ====================================================================*/	
	document.getElementById('arrow_move_column_top_'+vimofy_id_move).style.top = document.getElementById('header_'+id).offsetTop-10+'px';
	document.getElementById('arrow_move_column_bottom_'+vimofy_id_move).style.top = document.getElementById('header_'+id).offsetHeight+document.getElementById('header_'+id).offsetTop+'px';
	/**==================================================================*/
	
	/**==================================================================
	 * Display the float content
	 ====================================================================*/	
	// Set the content of the float menu
	vimofy_set_innerHTML('vimofy_column_move_div_float_content_'+vimofy_id_move,vimofy_get_innerHTML('th'+vimofy_column_move+'_'+vimofy_id_move));
	/**==================================================================*/
	
	/**==================================================================
	 * Disable selection
	 ====================================================================*/	
	// Mozilla & Safari
	document.body.className += ' __body_no_select';
	
	// IE 
	if(typeof(document.body.onselectstart) != "undefined") 
	{
		document.body.onselectstart = function(){return false;};
	}
	/**==================================================================*/
	
	eval('Vimofy.'+vimofy_id_move+'.selected_column = new Object();');
	
	if(evt.ctrlKey)
	{
		eval('Vimofy.'+vimofy_id_move+'.selected_column.ctrl = true;');
	}
	else
	{
		eval('Vimofy.'+vimofy_id_move+'.selected_column.ctrl = false;');
	}
}


function getCSSProperty(mixed, sProperty) 
{
   var oNode = (typeof mixed == "object") ?  mixed : document.getElementById(mixed);
    
    if(document.defaultView) {
        return document.defaultView.getComputedStyle(oNode, null).getPropertyValue(sProperty);
    }
    else if(oNode.currentStyle) {
        sProperty = sProperty.replace(/\-(\w)/g, function(m,c){return c.toUpperCase();});
        return oNode.currentStyle[sProperty];
    }
    else {
        return null;
    }
}

/**
 * Retourne les coordonnées d'un élément pour Internet Explorer.
 */
function ieGetCoords(elt) 
{
    var coords = elt.getBoundingClientRect();
    var border = getCSSProperty(document.getElementsByTagName('HTML')[0], 'border-width');
    var border = (border == 'medium') ? 2 : parseInt(border);
    
    elt.left += Math.max(elt.ownerDocument.documentElement.scrollLeft, elt.ownerDocument.body.scrollLeft) - border;
    elt.top  += Math.max(elt.ownerDocument.documentElement.scrollTop, elt.ownerDocument.body.scrollTop) - border;
    
    return coords;
}

/** 
 * Retourne les coordonnées d'un élément sur une page en fonction de tous ses éléments parents.
 * 
 * @param objet element
 * @param objet eltRef (optionnel)
 * @return json coords = {left:x, top:x}
 */
function getElementCoords(element, eltReferant) {
    
    var coords = {left: 0, top: 0};
    
    // IE pour résoudre le problème des marges (IE comptabilise dans offsetLeft la propriété marginLeft).
    if (element.getBoundingClientRect) {
        
        coords = ieGetCoords(element);
        
        if (typeof(eltReferant) == 'object') {
            var coords2 = ieGetCoords(eltReferant);
            
            coords.left -= coords2.left;
            coords.top  -= coords2.top;
            
            coords2 = null;
        }
    }
    // Les autres : récursivité sur offsetParent.
    else {
        
        while (element) {
            
            if (/^table$/i.test(element.tagName) && element.getElementsByTagName('CAPTION').length == 1 && getCSSProperty(element, 'position').toLowerCase() == 'relative') {
                coords.top += element.getElementsByTagName('CAPTION')[0].offsetHeight;
            }
            
            coords.left += element.offsetLeft;
            coords.top  += element.offsetTop;
            element      = element.offsetParent;
            
            if (typeof(eltReferant) == 'object' && element === eltReferant) {
                break;
            }
        }
    }
    
    return coords;
}