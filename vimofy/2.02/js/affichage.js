/**
 * Global vars
 */
var vimofy_column_in_resize = false;
var vimofy_column_in_move = false;
var vimofy_size_start = 0;
var cursor_start = 0;
var vimofy_column_resize = 0;
var vimofy_column_move = 0;
var vimofy_id_resize = 0;
var vimofy_id_move = 0;
var click_id_column = '';
var vimofy_body_style = '';

/**
 * Resize a column to is minimum possible size.
 * 
 * @param column id of the column in resize
 * @param vimofy_id Id of the vimofy
 */
function vimofy_mini_size_column(column,vimofy_id)
{
	document.getElementById('th'+column+'_'+vimofy_id).style.width = document.getElementById('span_'+column+'_'+vimofy_id).offsetWidth+'px';
	
	vimofy_column_resize = column;
	vimofy_id_resize = vimofy_id;
	vimofy_column_in_resize = true;

	vimofy_size_start = document.getElementById('th'+column+'_'+vimofy_id).offsetWidth;
	click_id_column = 'th'+(column)+'_'+vimofy_id;
	size_table(vimofy_id);
	vimofy_column_in_resize = false;
}

function addCss(cssCode)
{
	var styleElement = document.createElement("style");
	styleElement.type = "text/css";
	if (styleElement.styleSheet) {
styleElement.styleSheet.cssText = cssCode;
} else {
styleElement.appendChild(document.createTextNode(cssCode));
}
document.getElementsByTagName("head")[0].appendChild(styleElement);
}

/**
 * Called when a checkbox or a line was clicked.
 * 
 * @param line Line of the checkbox
 * @param evt event
 * @param checkbox  null if is a line, otherwise checkbox
 * @param id  Id of the vimofy
 */
function vimofy_checkbox(line,evt,checkbox,id)
{
	try 
	{
		
		vimofy_click(evt,id);
		
		if(checkbox == null)
		{
			// A line was clicked
			if(document.getElementById('chk_l'+line+'_c0_'+id).checked)
			{
				// Unselect
				document.getElementById('l'+line+'_'+id).className = 'lc_'+((line - 1)%eval('Vimofy.'+id+'.qtt_color'))+'_'+id;
				document.getElementById('chk_l'+line+'_c0_'+id).checked = false;
				eval('Vimofy.'+id+'.selected_line.L'+line+'.selected = false;');
				if(count_selected_lines(id) == 0)
				{
					if(document.getElementById('vimofy_td_toolbar_edit_'+id))document.getElementById('vimofy_td_toolbar_edit_'+id).className = 'btn_toolbar grey_el';
					if(document.getElementById('vimofy_td_toolbar_delete_'+id))document.getElementById('vimofy_td_toolbar_delete_'+id).className = 'btn_toolbar toolbar_separator_right grey_el';
				}
			}
			else
			{
				// Select
				if(document.getElementById('vimofy_td_toolbar_edit_'+id))document.getElementById('vimofy_td_toolbar_edit_'+id).className = 'btn_toolbar';
				if(document.getElementById('vimofy_td_toolbar_delete_'+id))document.getElementById('vimofy_td_toolbar_delete_'+id).className = 'btn_toolbar toolbar_separator_right';

				if(eval('Vimofy.'+id+'.return_mode') == __MULTIPLE__)
				{
					document.getElementById('l'+line+'_'+id).className = 'line_selected_color_'+((line - 1)%eval('Vimofy.'+id+'.qtt_color'))+'_'+id;
					document.getElementById('chk_l'+line+'_c0_'+id).checked = true;
					eval('Vimofy.'+id+'.selected_line.L'+line+' = Vimofy.'+id+'.lines.L'+line+';');
					eval('Vimofy.'+id+'.selected_line.L'+line+'.selected = true;');
				}
				else
				{
					if(eval('Vimofy.'+id+'.mode') == __LMOD__)
					{
						// LMOD click, simple return
						vimofy_lmod_click(id,line);
					
						// Stop the event handler
						vimofy_StopEventHandler(evt);
					}
					else
					{
						// A line was clicked, insert the line.
	
						if(line != null)
						{
							/* A line was clicked, insert the line.*/
							var return_value = vimofy_get_innerHTML('div_td_l'+line+'_c'+eval('Vimofy.'+id+'.c_col_return_id')+'_'+id);
							
							//var reg = new RegExp('(\\\\)','g');
							//return_value = return_value.replace(reg,"\\\\");
							return_value = protect_json(return_value);
							eval('Vimofy.'+id+'.col_return_last_value = \''+return_value+'\';');
							vimofy_execute_event(__ON_LMOD_INSERT__,__AFTER__,id);
						}
					}
				}
			}
		}
		else
		{
			// A checkbox was clicked
			var evt = (evt) ? evt : event;
			
			if(evt.shiftKey && eval('Vimofy.'+id+'.last_checked') != 0)
			{
				// Shift case was pressed
				var min = Math.min(line,eval('Vimofy.'+id+'.last_checked'));
				var max = Math.max(line,eval('Vimofy.'+id+'.last_checked'));
				
				if(!document.getElementById('chk_l'+line+'_c0_'+id).checked)
				{
					for(i=min;i<=max;i++)
					{
						// Unselect
						document.getElementById('chk_l'+i+'_c0_'+id).checked = false;
						document.getElementById('l'+i+'_'+id).className = 'lc_'+((i - 1)%eval('Vimofy.'+id+'.qtt_color'))+'_'+id;
						eval('Vimofy.'+id+'.selected_line.L'+i+'.selected = false;');
					}
					if(count_selected_lines(id) == 0)
					{
						if(document.getElementById('vimofy_td_toolbar_edit_'+id))document.getElementById('vimofy_td_toolbar_edit_'+id).className = 'btn_toolbar grey_el';
						if(document.getElementById('vimofy_td_toolbar_delete_'+id))document.getElementById('vimofy_td_toolbar_delete_'+id).className = 'btn_toolbar toolbar_separator_right grey_el';
					}
				}
				else
				{
					// Select
					if(document.getElementById('vimofy_td_toolbar_edit_'+id))document.getElementById('vimofy_td_toolbar_edit_'+id).className = 'btn_toolbar';
					if(document.getElementById('vimofy_td_toolbar_delete_'+id))document.getElementById('vimofy_td_toolbar_delete_'+id).className = 'btn_toolbar toolbar_separator_right';
					for(i=min;i<=max;i++)
					{
						document.getElementById('chk_l'+i+'_c0_'+id).checked = true;
						document.getElementById('l'+i+'_'+id).className = 'line_selected_color_'+((i - 1)%eval('Vimofy.'+id+'.qtt_color'))+'_'+id;
						eval('Vimofy.'+id+'.selected_line.L'+i+' = Vimofy.'+id+'.lines.L'+i+';');
						eval('Vimofy.'+id+'.selected_line.L'+i+'.selected = true;');
					}
				}
			}
			else
			{
				if(!document.getElementById('chk_l'+line+'_c0_'+id).checked)
				{
					// Unselect
					document.getElementById('l'+line+'_'+id).className = 'lc_'+((line - 1)%eval('Vimofy.'+id+'.qtt_color'))+'_'+id;
					eval('Vimofy.'+id+'.selected_line.L'+line+'.selected = false;');
					if(count_selected_lines(id) == 0)
					{
						if(document.getElementById('vimofy_td_toolbar_edit_'+id))document.getElementById('vimofy_td_toolbar_edit_'+id).className = 'btn_toolbar grey_el';
						if(document.getElementById('vimofy_td_toolbar_delete_'+id))document.getElementById('vimofy_td_toolbar_delete_'+id).className = 'btn_toolbar toolbar_separator_right grey_el';
					}
				}
				else
				{
					// Select
					if(document.getElementById('vimofy_td_toolbar_edit_'+id))document.getElementById('vimofy_td_toolbar_edit_'+id).className = 'btn_toolbar';
					if(document.getElementById('vimofy_td_toolbar_delete_'+id))document.getElementById('vimofy_td_toolbar_delete_'+id).className = 'btn_toolbar toolbar_separator_right';
					document.getElementById('l'+line+'_'+id).className = 'line_selected_color_'+((line - 1)%eval('Vimofy.'+id+'.qtt_color'))+'_'+id;
					eval('Vimofy.'+id+'.selected_line.L'+line+' = Vimofy.'+id+'.lines.L'+line+';');
					eval('Vimofy.'+id+'.selected_line.L'+line+'.selected = true;');
				}
			}
		}
		
		eval('Vimofy.'+id+'.last_checked = '+line);
		if(evt != null)
		{
			vimofy_StopEventHandler(evt);
		}
	}
	catch(e) 
	{
		vimofy_display_error(id,e,'aff1');
	}
}

/**
 * Open a vimofy to load a filter
 * @param vimofy_id  Id of the vimofy
 * @param vimofy_type Type of internal vimofy
 * @param ajax_return return ajax
 */
function vimofy_load_filter_lov(vimofy_id,vimofy_type,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		vimofy_cover_with_filter(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/vimofy/internal_vimofy.php';
		conf['delai_tentative'] = 5000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&vimofy_type='+vimofy_type;
		conf['fonction_a_executer_reponse'] = 'vimofy_load_filter_lov';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+vimofy_type+"'";
		
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			// Get the ajax return in json format
			var json = get_json(ajax_return);
			
			// Display the internal vimofy
			document.getElementById('internal_vimofy_'+vimofy_id).style.display = 'block';
			
			// Init the opened child flag
			eval('Vimofy.'+vimofy_id+'.vimofy_child_opened = 1;');
			
			// Update the json object
			eval(decodeURIComponent(json.vimofy.json));
			
			// Set the content of the Vimofy
			vimofy_set_innerHTML('internal_vimofy_'+vimofy_id,decodeURIComponent(json.vimofy.content));
			
			// Hide the wait div
			document.getElementById('internal_vimofy_'+vimofy_id).style.width = '500px';
			document.getElementById('internal_vimofy_'+vimofy_id).style.top = '24px';
			if(document.getElementById('liste_'+vimofy_id).offsetHeight < 422)
			{
				document.getElementById('internal_vimofy_'+vimofy_id).style.height = document.getElementById('liste_'+vimofy_id).offsetHeight+22+'px';
				document.getElementById('vim__'+eval('Vimofy.'+vimofy_id+'.theme')+'__vimofy_table_'+vimofy_id+'_child__').style.height = document.getElementById('liste_'+vimofy_id).offsetHeight+'px';
			}
			else
			{
				document.getElementById('internal_vimofy_'+vimofy_id).style.height = '322px';
			}
			
			size_table(vimofy_id+'_child');
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e,'aff2');
		}
	}
}

/**
 * Open a vimofy to load a filter
 * @param vimofy_id  Id of the vimofy
 * @param vimofy_type Type of internal vimofy
 * @param ajax_return return ajax
 */
function vimofy_hide_display_col_lov(vimofy_id,vimofy_type,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		vimofy_cover_with_filter(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/vimofy/internal_vimofy.php';
		conf['delai_tentative'] = 6000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&vimofy_type='+vimofy_type;
		conf['fonction_a_executer_reponse'] = 'vimofy_load_filter_lov';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+vimofy_type+"'";
		
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			var json = get_json(ajax_return);
			document.getElementById('internal_vimofy_'+vimofy_id).style.display = 'block';
			
			eval('Vimofy.'+vimofy_id+'.vimofy_child_opened = 1;');
			
			// Update the json object
			eval(decodeURIComponent(json.vimofy.json));
			
			// Set the content of the Vimofy
			vimofy_set_innerHTML('internal_vimofy_'+vimofy_id,decodeURIComponent(json.vimofy.content));
			
			// Hide the wait div
			document.getElementById('internal_vimofy_'+vimofy_id).style.width = '500px';
			if(document.getElementById('liste_'+vimofy_id).offsetHeight < 422)
			{
				document.getElementById('internal_vimofy_'+vimofy_id).style.height = document.getElementById('liste_'+vimofy_id).offsetHeight+22+'px';
				document.getElementById('vim__'+eval('Vimofy.'+vimofy_id+'.theme')+'__vimofy_table_'+vimofy_id+'_child__').style.height = document.getElementById('liste_'+vimofy_id).offsetHeight+'px';
			}
			else
			{
				document.getElementById('internal_vimofy_'+vimofy_id).style.height = '422px';
			}
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e,'aff3');
		}
	}
}

function vimofy_load_filter(vimofy_id,filter_name,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		var filter_name = vimofy_get_innerHTML(filter_name);
		
		vimofy_display_wait(vimofy_id);
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 6000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;									// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=17&filter_name='+filter_name;
		conf['fonction_a_executer_reponse'] = 'vimofy_load_filter';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+filter_name+"'";
		
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
		}
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e,'aff4');
		}
	}
}

/**
 * Display the detail of an element
 * 
 * @param id_lib Id of the libelle
 * @param id_help Id of the help page
 * @param vimofy_id  Id of the vimofy
 */
function vimofy_lib_hover(id_lib,id_help,vimofy_id)
{
	if(!vimofy_column_in_resize)
	{
		vimofy_set_innerHTML('vim__vimofy_help_hover_'+vimofy_id+'__',vim_lib[id_lib]);
	}
}

/**
 * Hide the detail of an element
 * 
 * @param vimofy_id Id of the vimofy
 */
function vimofy_lib_out(vimofy_id)
{
	if(!vimofy_column_in_resize)
	{
		vimofy_set_innerHTML('vim__vimofy_help_hover_'+vimofy_id+'__','');
	}
}


/**
 * Resize a column, call when begin the resize
 * 
 * @param column id of the column in resize
 * @param id Id of the vimofy
 */
function vimofy_resize_column_start(column,id)
{
	document.getElementById('header_'+id).className += ' __body_no_select';
	if(document.getElementById('vimofy_header_page_selection_'+id))
	document.getElementById('vimofy_header_page_selection_'+id).className += ' __body_no_select';
	
	vimofy_column_resize = column;
	vimofy_id_resize = id;

	vimofy_column_in_resize = true;
	vimofy_size_start = document.getElementById('th'+column+'_'+id).offsetWidth;
	click_id_column = 'th'+(column)+'_'+id;
	
	vimofy_body_style = document.body.className;
	document.body.className += ' __body_no_select';
	
	// IE
	if(typeof(document.body.onselectstart) != "undefined") 
	{
		document.body.onselectstart = function(){return false;};
	}
}

/**
 * Resize a column, call when stop the resize
 */
function vimofy_resize_column_stop()
{
	vimofy_set_innerHTML('vim__vimofy_help_hover_'+vimofy_id_resize+'__','');
	document.getElementById('header_'+vimofy_id_resize).className = '__'+eval('Vimofy.'+vimofy_id_resize+'.theme')+'__vimofy_header__';
	if(document.getElementById('vimofy_header_page_selection_'+vimofy_id_resize))
	document.getElementById('vimofy_header_page_selection_'+vimofy_id_resize).className = '__'+eval('Vimofy.'+vimofy_id_resize+'.theme')+'_vimofy_header_page_selection';
	
	cursor_start = 0;
	// Resize the column
	size_table(vimofy_id_resize);
	vimofy_column_in_resize = false;
	document.body.className = vimofy_body_style;
	
	// IE
	if(typeof(document.body.onselectstart) != "undefined") 
	{
		document.body.onselectstart = null;
	}
}


function vimofy_hide_container_click(vimofy_id)
{
	if(eval('Vimofy.'+vimofy_id+'.vimofy_child_opened') != false)
	{
		 vimofy_child_cancel(vimofy_id,eval('Vimofy.'+vimofy_id+'.vimofy_child_opened'));
	}
}

/**
 * Set a content to a vimofy
 * @param vimofy_id id of the vimofy
 * @param content content to set
 */
function vimofy_set_content(vimofy_id,content)
{
	// Get the position of the scrollbar
	var scroll_before = document.getElementById('liste_'+vimofy_id).scrollLeft;
	
	// Set the content to the vimofy
	vimofy_set_innerHTML('vimofy_ajax_return_'+vimofy_id,content);
	
	/**==================================================================
	 * Size the columns of the vimofy
	 ====================================================================*/	
	size_table(vimofy_id);
	/**==================================================================*/
	
	/**==================================================================
	 * Re-Activate the vimofy horizontal scroll
	 ====================================================================*/	
	document.getElementById('liste_'+vimofy_id).onscroll = function(){vimofy_horizontal_scroll(vimofy_id);};
	document.getElementById('liste_'+vimofy_id).scrollLeft = scroll_before;
	/**==================================================================*/
}

/**
 * Resize the column
 * @param e event
 */
function vimofy_resize_column(e)
{
	if(cursor_start == 0)
	{
		cursor_start = e.clientX;
	}
	
	// Resize only if the elements fit into the cell
	if(((vimofy_size_start+(e.clientX - cursor_start)) > document.getElementById('span_'+vimofy_column_resize+'_'+vimofy_id_resize).offsetWidth))
	{
		//vimofy_set_innerHTML('vim__vimofy_help_hover_'+vimofy_id_resize+'__',vimofy_size_start+(e.clientX - cursor_start)+" pixels");
		document.getElementById(click_id_column).style.width =  vimofy_size_start+(e.clientX - cursor_start)+"px";
	}
}

/**
 * Handler when the cursor move
 * 
 * @param e event
 */
function vimofy_move_cur(e)
{
	if(vimofy_column_in_resize)
	{
		vimofy_resize_column(e);
	}
	else
	{
		if(vimofy_column_in_move)
		{
			vimofy_move_column(e);
		}
	}
}

/**
 * Handler when mouseup
 * 
 * @param e event
 */
function vimofy_mouseup(e)
{
	if(vimofy_column_in_resize)
	{
		vimofy_resize_column_stop();
	}
	else
	{
		if(vimofy_column_in_move)
		{
			vimofy_move_column_stop();
		}
	}
}

/**
 * Stop the event handler
 * 
 * @param e event
 */
function vimofy_StopEventHandler(evt)
{
	var evt = (evt)?evt : event;
	
    if(typeof(window.event) != 'undefined')
    {
    	window.event.cancelBubble = true;
    }
    else
    {
    	evt.stopPropagation();
    }
}

/**
 * Get the position of an element in reference to the body.
 * 
 * @param id Id of the element
 * @returns {Array} 0 => left, 1 => top
 */
function vimofy_getPosition(id)
{
	var left = 0;
	var top = 0;
	
	/* Get element */
	var element = document.getElementById(id);

	/* While the element have a parent */
	while(element.offsetParent != undefined && element.offsetParent != null)
	{
		/* Add the position of the parent element */
		if(element.clientLeft != null)
		{
			left += element.offsetLeft + element.clientLeft;
		}
		
		if(element.clientTop != null)
		{
			top += element.offsetTop + element.clientTop;
		}
		
		element = element.offsetParent;
	}
	
	top += element.offsetTop;
	
	var tab = new Array(left,top);
	
	return tab;
}


/**
 * Toggle the column menu
 * 
 * @param id Id of the vimofy
 * @param column  Column of the menu
 */
function vimofy_toggle_header_menu(id,column)
{
	eval('Vimofy.'+id+'.stop_click_event = true;');
	
	var div_menu = document.getElementById('vim_column_header_menu_'+id);
	
	if(div_menu.style.display == 'none' || div_menu.style.display == '')
	{
		// Display the menu icon
		document.getElementById('th_menu_'+column+'__'+id).className = '__'+eval('Vimofy.'+id+'.theme')+'_menu_header_click';
		
		// Enable the active menu
		eval('Vimofy.'+id+'.menu_opened_col = '+column+';');
		
		/**==================================================================
		 * Prepare order submenu
		 ====================================================================*/	
		var obj_order = new vimofy_menu(id); 
		if(eval('Vimofy.'+id+'.columns.c'+column+'.order') == '')
		{
			obj_order.add_line(vim_lib[18],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort-ascend','vimofy_column_order(\''+id+'\',__ASC__,'+column+',__NEW__)',true);
			obj_order.add_line(vim_lib[19],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort-descend','vimofy_column_order(\''+id+'\',__DESC__,'+column+',__NEW__)',true);
			obj_order.add_line(vim_lib[42],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort_delete',null,false);
		}
		else if(eval('Vimofy.'+id+'.columns.c'+column+'.order') == 'DESC') 
		{
			// DESC
			obj_order.add_line(vim_lib[18],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort-ascend','vimofy_column_order(\''+id+'\',__ASC__,'+column+',__NEW__)',true);
			obj_order.add_line(vim_lib[19],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort-descend',null,false);
			obj_order.add_line(vim_lib[42],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort_delete','vimofy_column_order(\''+id+'\',__NONE__,'+column+',__NEW__)',true);
		}
		else
		{
			// ASC
			obj_order.add_line(vim_lib[18],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort-ascend',null,false);
			obj_order.add_line(vim_lib[19],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort-descend','vimofy_column_order(\''+id+'\',__DESC__,'+column+',__NEW__)',true);
			obj_order.add_line(vim_lib[42],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort_delete','vimofy_column_order(\''+id+'\',__NONE__,'+column+',__NEW__)',true);
		}
		
		/**==================================================================*/
		
		/**==================================================================
		 * Prepare search mode submenu
		 ====================================================================*/	
		var obj_search_mode = new vimofy_menu(id); 
		
		if(eval('Vimofy.'+id+'.columns.c'+column+'.search_mode') == __PERCENT__)
		{
			// PERCENT
			obj_search_mode.add_line(vim_lib[35],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_radio_off','vimofy_change_search_mode(\''+id+'\',__EXACT__,'+column+');',true);
			obj_search_mode.add_line(vim_lib[36],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_radio_on',null,false);
		}
		else
		{
			// STRICT
			obj_search_mode.add_line(vim_lib[35],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_radio_on',null,false);
			obj_search_mode.add_line(vim_lib[36],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_radio_off','vimofy_change_search_mode(\''+id+'\',__PERCENT__,'+column+');',true);
		}
		/**==================================================================*/
		
		/**==================================================================
		 * Prepare alignment submenu
		 ====================================================================*/	
		var obj_alignment = new vimofy_menu(id); 
		
		switch(eval('Vimofy.'+id+'.columns.c'+column+'.alignment'))
		{
		case __LEFT__:
			obj_alignment.add_line(vim_lib[47],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_left',null,false);
			obj_alignment.add_line(vim_lib[48],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_center','vimofy_change_col_alignment(\''+id+'\',__CENTER__,'+column+');',true);
			obj_alignment.add_line(vim_lib[49],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_right','vimofy_change_col_alignment(\''+id+'\',__RIGHT__,'+column+');',true);
			break;
		case __CENTER__:
			obj_alignment.add_line(vim_lib[47],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_left','vimofy_change_col_alignment(\''+id+'\',__LEFT__,'+column+');',true);
			obj_alignment.add_line(vim_lib[48],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_center',null,false);
			obj_alignment.add_line(vim_lib[49],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_right','vimofy_change_col_alignment(\''+id+'\',__RIGHT__,'+column+');',true);
			break;
		case __RIGHT__:
			obj_alignment.add_line(vim_lib[47],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_left','vimofy_change_col_alignment(\''+id+'\',__LEFT__,'+column+');',true);
			obj_alignment.add_line(vim_lib[48],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_center','vimofy_change_col_alignment(\''+id+'\',__CENTER__,'+column+');',true);
			obj_alignment.add_line(vim_lib[49],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_right',null,false);
			break;
		}
		/**==================================================================*/
		

		/**==================================================================
		 * Prepare the principal menu
		 ====================================================================*/	
		var obj = new vimofy_menu(id); 
		
		/**==================================================================
		 * Prepare calendar button
		 ====================================================================*/	
		
		
		if(eval('Vimofy.'+id+'.columns.c'+column+'.data_type') == 'date')
		{
			/*obj.add_sep();*/
			obj.add_line(vim_lib[66],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_calendar','vimofy_generate_calendar(\''+id+'\','+column+');',true);
			obj.add_sep();
		}
		/**==================================================================*/
		
		obj.add_line(vim_lib[39],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort-ascend',null,true,obj_order);
		obj.add_line(vim_lib[34],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_search_mode',null,true,obj_search_mode);
		obj.add_line(vim_lib[46],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_left',null,true,obj_alignment);
		
		
		if(eval('Vimofy.'+id+'.mode') != __CMOD__)
		{
			obj.add_sep();
			obj.add_line(vim_lib[41],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_filter','/*vimofy_display_internal_vim(\''+id+'\',__ADV_FILTER__,'+column+');*/',false);

			var is_lovable = eval('Vimofy.'+id+'.columns.c'+column+'.is_lovable');
			
			if(is_lovable != undefined && is_lovable == true)
			{
				var line_enable = true;
			}
			else
			{
				var line_enable = false;
			}
			
			if(eval('Vimofy.'+id+'.columns.c'+column+'.lov_title') == eval('undefined'))
			{
				obj.add_line(vim_lib[44],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_values','vimofy_display_internal_vim(\''+id+'\',__POSSIBLE_VALUES__,'+column+');',line_enable);
			}
			else
			{
				obj.add_line(eval('Vimofy.'+id+'.columns.c'+column+'.lov_title'),'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_values','vimofy_display_internal_vim(\''+id+'\',__POSSIBLE_VALUES__,'+column+');',line_enable);
			}
			
		
			// Hide button
			//if(eval('Vimofy.'+id+'.c_col_return_id') != column)
			//{
			//	obj.add_sep();
			//	obj.add_line(vim_lib[20],'__'+eval('Vimofy.'+id+'.theme')+'_ico __'+eval('Vimofy.'+id+'.theme')+'_ico_sort-hide','vimofy_toggle_column(\''+id+'\','+column+');/*msgbox(\''+id+'\',vim_lib[20],vim_lib[20]);*/',true);
			//}
		}
		/**==================================================================*/
		
		
		
		// Display the menu
		div_menu.style.display = 'block';
		div_menu.innerHTML = obj.display_menu();
		
		eval('Vimofy.'+id+'.obj_menu = obj');

		// Positions the menu
		vimofy_position_menu(id,column);
	}
	else
	{
		// Hide the menu icon
		var is_lovable = eval('Vimofy.'+id+'.columns.c'+column+'.is_lovable');
		var lov_perso = eval('Vimofy.'+id+'.columns.c'+column+'.lov_perso');
		
		if(lov_perso != undefined && is_lovable != undefined && is_lovable == true)
		{
			document.getElementById('th_menu_'+eval('Vimofy.'+id+'.columns.c'+column+'.id')+'__'+id).className = '__'+eval('Vimofy.'+id+'.theme')+'_menu_header_lovable __'+eval('Vimofy.'+id+'.theme')+'_men_head';
		}
		else
		{
			if(lov_perso != undefined)
			{
				document.getElementById('th_menu_'+eval('Vimofy.'+id+'.columns.c'+column+'.id')+'__'+id).className = '__'+eval('Vimofy.'+id+'.theme')+'_menu_header_no_icon __'+eval('Vimofy.'+id+'.theme')+'_men_head';
			}
			else
			{
				document.getElementById('th_menu_'+eval('Vimofy.'+id+'.columns.c'+column+'.id')+'__'+id).className = '__'+eval('Vimofy.'+id+'.theme')+'_menu_header __'+eval('Vimofy.'+id+'.theme')+'_men_head';
			}
		}
		
		// Disable the active menu
		eval('Vimofy.'+id+'.menu_opened_col = false;');
		
		// Clear the content of the menu
		div_menu.innerHTML = '';
		
		// Hide the menu
		div_menu.style.display = 'none';
	}
}

function vimofy_generate_calendar(vimofy_id,column,ajax_return)
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
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=18&column='+column;
		conf['fonction_a_executer_reponse'] = 'vimofy_generate_calendar';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"',"+column;
		
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			// Set the content to the calendar
			vimofy_set_innerHTML('vim_column_calendar_'+vimofy_id, ajax_return);

			//Display the calendar
			vimofy_set_style_display('vim_column_calendar_'+vimofy_id, 'block');
			
			var pos = vimofy_getPosition('th_menu_'+column+'__'+vimofy_id);
			
			var pos_th = document.getElementById('th_menu_'+column+'__'+vimofy_id).offsetLeft - document.getElementById('liste_'+vimofy_id).scrollLeft;
			document.getElementById('vim_column_calendar_'+vimofy_id).style.left =  pos_th+'px';
			
			// Hide the wait div
			vimofy_display_wait(vimofy_id);
			
		}
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e,'aff5');
		}
	}
}

/**
 * Place the column menu correctly on the vimofy
 * 
 * @param id Id of the vimofy
 * @param column Column of the menu
 */
function vimofy_position_menu(id,column)
{
	/**==================================================================
	 * Vertical placement
	 *====================================================================*/	
	var top_container = document.getElementById('conteneur_menu_'+id).offsetTop;
	document.getElementById('vim_column_header_menu_'+id).style.top = top_container+'px';
	/** ================================================================== */	
	
	/**==================================================================
	 * Horizontal placement
	 *===================================================================*/	
	var pos_th = document.getElementById('th_menu_'+column+'__'+id).offsetLeft - document.getElementById('liste_'+id).scrollLeft;
	document.getElementById('vim_column_header_menu_'+id).style.left = pos_th-5+'px';
	/** ================================================================== */

	/**================================================================== 
	 * Test the position of the menu
	 *====================================================================*/	
	// Get the position of the menu
	var pos_menu = vimofy_getPosition('vim_column_header_menu_'+id); 
	
	// Get the position of the vimofy
	var pos_vimofy = vimofy_getPosition('vim__'+eval('Vimofy.'+id+'.theme')+'__vimofy_table_'+id+'__'); 

	// Test if the menu is not out of the right corner of the vimofy
	if((pos_menu[0]+document.getElementById('menu_1_l1').offsetWidth) > (pos_vimofy[0]+document.getElementById('vim__'+eval('Vimofy.'+id+'.theme')+'__vimofy_table_'+id+'__').offsetWidth))
	{
		document.getElementById('vim_column_header_menu_'+id).style.left = '';
		document.getElementById('vim_column_header_menu_'+id).style.right = 0+'px';
	}	
	/**==================================================================*/
	
	eval('Vimofy.'+id+'.menu_left = '+document.getElementById('vim_column_header_menu_'+id).offsetLeft+';');
}

/**
 * Test if the vimofy has a vertical scrollbar
 * @param id Id of the vimofy
 * @returns {Boolean} True if vertical scrollbar present, false in other case.
 */
function vimofy_has_vertical_scrollbar(id)
{
	/**================================================================== 
	 * Test if the Vimofy has vertical scrollbar
	 * ====================================================================*/	
	if(document.getElementById('liste_'+id).clientHeight < document.getElementById('liste_'+id).scrollHeight)
	{
		return true;		// Vertical scrollbar present
	}
	else
	{
		return false;		// No vertical scrollbar
	}
	/**==================================================================*/
}


/**
 * Handler when a the vimofy is scrolled horizontaly
 * @param id_vimofy Id of the vimofy
 */
function vimofy_horizontal_scroll(id_vimofy)
{
	/**================================================================== 
	 * Move the header columns
	 *==================================================================*/	
	document.getElementById('header_'+id_vimofy).scrollLeft = document.getElementById('liste_'+id_vimofy).scrollLeft;
	/**==================================================================*/	

	/**================================================================== 
	 * Move the opened menu
	 * =================================================================*/	
	if(eval('Vimofy.'+id_vimofy+'.menu_opened_col') != false)
	{
		var pos_th = vimofy_getPosition('th_menu_'+eval('Vimofy.'+id_vimofy+'.menu_opened_col')+'__'+id_vimofy);
		
		var pos_menu = vimofy_getPosition('vim_column_header_menu_'+id_vimofy); // div_menu.offsetLeft;
		var pos_vimofy = document.getElementById('vim__'+eval('Vimofy.'+id_vimofy+'.theme')+'__vimofy_table_'+id_vimofy+'__').offsetLeft;
		if(pos_vimofy >= pos_menu[0])
		{
			document.getElementById('vim_column_header_menu_'+id_vimofy).style.left = eval('Vimofy.'+id_vimofy+'.menu_left')-document.getElementById('liste_'+id_vimofy).scrollLeft+'px';
		}
		else
		{
			 /**==================================================================
			 * Horizontal placement
			 *====================================================================*/
			var pos_th = document.getElementById('th_menu_'+eval('Vimofy.'+id_vimofy+'.menu_opened_col')+'__'+id_vimofy).offsetLeft - document.getElementById('liste_'+id_vimofy).scrollLeft;
			document.getElementById('vim_column_header_menu_'+id_vimofy).style.left = pos_th-document.getElementById('vim_column_header_menu_'+id_vimofy).offsetWidth+19+'px';
			/**==================================================================*/
		}
	}
	else if(eval('Vimofy.'+id_vimofy+'.menu_quick_search') != false)
	{
		var pos_th = vimofy_getPosition('th_menu_'+eval('Vimofy.'+id_vimofy+'.menu_quick_search_col')+'__'+id_vimofy);
		var pos_menu = vimofy_getPosition('vim_column_header_menu_'+id_vimofy); // div_menu.offsetLeft;
		var pos_vimofy = document.getElementById('vim__'+eval('Vimofy.'+id_vimofy+'.theme')+'__vimofy_table_'+id_vimofy+'__').offsetLeft;
		if(pos_vimofy >= pos_menu[0])
		{
			document.getElementById('vim_column_header_menu_'+id_vimofy).style.left = eval('Vimofy.'+id_vimofy+'.menu_left')-document.getElementById('liste_'+id_vimofy).scrollLeft+'px';
		}
		else
		{
			 /** ==================================================================
			 * Horizontal placement
			 * ====================================================================*/
			var pos_th = document.getElementById('th_1_c'+eval('Vimofy.'+id_vimofy+'.menu_quick_search_col')+'_'+id_vimofy).offsetLeft - document.getElementById('liste_'+id_vimofy).scrollLeft;
			document.getElementById('vim_column_header_menu_'+id_vimofy).style.left = pos_th+'px';
			/** ================================================================== */
		}
	}
	else if(eval('Vimofy.'+id_vimofy+'.vimofy_child_opened') != false)
	{
		var pos_th = vimofy_getPosition('th_menu_'+eval('Vimofy.'+id_vimofy+'.vimofy_child_opened')+'__'+id_vimofy);
		var pos_menu = vimofy_getPosition('vim_column_header_menu_'+id_vimofy); // div_menu.offsetLeft;
		var pos_vimofy = document.getElementById('vim__'+eval('Vimofy.'+id_vimofy+'.theme')+'__vimofy_table_'+id_vimofy+'__').offsetLeft;
		if(pos_vimofy >= pos_menu[0])
		{
			document.getElementById('internal_vimofy_'+id_vimofy).style.left = eval('Vimofy.'+id_vimofy+'.menu_left')-document.getElementById('liste_'+id_vimofy).scrollLeft+'px';
		}
		else
		{
			 /** ==================================================================
			 * Horizontal placement
			 * ====================================================================*/
			var pos_th = document.getElementById('th_1_c'+eval('Vimofy.'+id_vimofy+'.menu_quick_search_col')+'_'+id_vimofy).offsetLeft - document.getElementById('liste_'+id_vimofy).scrollLeft;
			document.getElementById('internal_vimofy_'+id).style.left = pos_th+19+'px';
			/** ================================================================== */
		}
	}
	/** ==================================================================*/	
}


function vimofy_click(evt,vimofy_id)
{
	if(eval('Vimofy.'+vimofy_id) != eval('null'))
	{
		if(eval('Vimofy.'+vimofy_id+'.stop_click_event') == eval('false'))
		{
			if(eval('Vimofy.'+vimofy_id+'.menu_opened_col') != false)
			{
				// The vimofy was clicked
				vimofy_toggle_header_menu(vimofy_id, eval('Vimofy.'+vimofy_id+'.menu_opened_col'));
			}
			else if(eval('Vimofy.'+vimofy_id+'.menu_quick_search') == true)
			{
				close_input_result(vimofy_id);
			}
		}
		else
		{
			eval('Vimofy.'+vimofy_id+'.stop_click_event = false;');
		}
	}
}

/**
 * Handler when mousedown
 * @param e event
 */
function vimofy_mousedown(evt)
{
	var evt = (evt)?evt : event;
	
	for(var iterable_element in Vimofy) 
	{
		// Only if it is a vimofy in lmod
		if(eval('Vimofy.'+iterable_element+'.mode') == 'lmod')
		{
			var id = 'vim__'+eval('Vimofy.'+iterable_element+'.theme')+'__vimofy_table_'+iterable_element+'__';
			if(document.getElementById(id) != null)
			{
				// Get the position of the vimofy
				//var pos = vimofy_getPosition(id);
				var pos = getElementCoords(document.getElementById(id));
				
				var width = document.getElementById(id).offsetWidth;
				var height = document.getElementById(id).offsetHeight;
				
				if((evt.clientY < pos.top || evt.clientY > (pos.top+height)) || (evt.clientX < pos.left || evt.clientX > (pos.left+width)))
				{
					// The cursor is out of the vimofy, close the vimofy
					if(eval('Vimofy.'+iterable_element+'.lmod_opened') == true)
					{
						vimofy_lmod_click(iterable_element);
					}
					
				}
			}
		}
	}	
}


/**
 * Display or hide a prompt in the vimofy
 * @param id_vimofy Id of the vimofy
 * @param title Title of the prompt
 * @param txt text of the prompt
 */
function vimofy_prompt(id_vimofy,title,txt,button)
{
	vimofy_cover_with_filter(id_vimofy);
	var theme = eval('Vimofy.'+id_vimofy+'.theme');
	if(document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.display == '' || document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.display == 'none')
	{
		document.getElementById('vim_msgbox_conteneur_'+id_vimofy).style.display = 'none';
		vimofy_set_innerHTML('vim_msgbox_conteneur_'+id_vimofy,'');
	}
	else
	{
		document.getElementById('vim_msgbox_conteneur_'+id_vimofy).style.display = '';
		if(isNaN(title))
		{
			vimofy_generer_msgbox(id_vimofy,title,txt,'','prompt',button);
		}
		else
		{
			vimofy_generer_msgbox(id_vimofy,vim_lib[title],vim_lib[txt],'','prompt',button);
		}

	}
}

/**
 * Cover the vimofy with a filter (toggle function)
 * @param id_vimofy Id of the vimofy
 */
function vimofy_cover_with_filter(id_vimofy)
{
	var theme = eval('Vimofy.'+id_vimofy+'.theme');
	
	if(document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.display == '' || document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.display == 'none')
	{
		document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.top = document.getElementById('vimofy_toolbar_'+id_vimofy).offsetTop+'px';
		document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.width = document.getElementById('liste_'+id_vimofy).offsetWidth+'px';
		document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.height = document.getElementById('vimofy_footer_page_selection_'+id_vimofy).offsetTop-document.getElementById('vimofy_ajax_return_'+id_vimofy).offsetTop+50+'px';
		document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.display = 'block';
	}
	else
	{
		// Hide the cover div
		document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.display = 'none';
		
		// Erase the content of the wait div
		vimofy_set_innerHTML('vim__'+theme+'__wait_'+id_vimofy+'__','');
		
		// Erase the content of the msgbox div
		vimofy_set_innerHTML('vim_msgbox_conteneur_'+id_vimofy,'');
	}
}

function vimofy_display_prompt_create_filter(id_vimofy)
{
	var prompt_btn = new Array([vim_lib[31],vim_lib[32]],["vimofy_save_filter('"+id_vimofy+"');","vimofy_cover_with_filter('"+id_vimofy+"');"]);
	vimofy_prompt(id_vimofy,3,30,prompt_btn);
}

function vimofy_save_filter(vimofy_id,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * Get name of the filter
		 ====================================================================*/	
		var input_value = encodeURIComponent(document.getElementById('vimofy_'+vimofy_id+'_msgbox_prompt_value').value);
		vimofy_cover_with_filter(vimofy_id);
		/**==================================================================*/
		
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=7&name='+input_value;
		conf['fonction_a_executer_reponse'] = 'vimofy_save_filter';
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
			if(decodeURIComponent(json.vimofy.error) == 'true')
			{
				// An error occured
				msgbox(vimofy_id,decodeURIComponent(json.vimofy.title),decodeURIComponent(json.vimofy.message));
			}
		
		}
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e,'aff6');
		}
	}
}

function vimofy_display_wait(id_vimofy)
{
	vimofy_cover_with_filter(id_vimofy);
	var theme = eval('Vimofy.'+id_vimofy+'.theme');
	if(document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.display == '' || document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').style.display == 'none')
	{
		document.getElementById('vim__'+theme+'__wait_'+id_vimofy+'__').style.display = 'none';
	}
	else
	{
		document.getElementById('vim__'+theme+'__wait_'+id_vimofy+'__').style.display = '';
		document.getElementById('vim__'+theme+'__wait_'+id_vimofy+'__').style.margin = ((document.getElementById('vim__'+theme+'__hide_container_'+id_vimofy+'__').offsetHeight-document.getElementById('vim__'+theme+'__wait_'+id_vimofy+'__').offsetHeight)/2)+'px 0 0 '+((document.getElementById('liste_'+id_vimofy).offsetWidth-document.getElementById('vim__'+theme+'__wait_'+id_vimofy+'__').offsetWidth)/2)+'px';
	}
}

function vimofy_hide_wait(vimofy_id)
{
	var theme = eval('Vimofy.'+vimofy_id+'.theme');

	// Hide the cover div
	document.getElementById('vim__'+theme+'__hide_container_'+vimofy_id+'__').style.display = 'none';
	
	// Erase the content of the wait div
	vimofy_set_innerHTML('vim__'+theme+'__wait_'+vimofy_id+'__','');
	
	// Erase the content of the msgbox div
	vimofy_set_innerHTML('vim_msgbox_conteneur_'+vimofy_id,'');
	
	document.getElementById('vim__'+theme+'__wait_'+vimofy_id+'__').style.display = 'none';
}

function vimofy_display_error(id_vimofy,e,more)
{
	var title = e.message;
	if(typeof(more) == undefined)
	{
		more = '';
	}
	if(e.sourceURL)
	{
		var file = e.sourceURL;
	}
	else
	{
		var file = e.fileName;
	}
	
	if(e.line)
	{
		var line = e.line;
	}
	else
	{
		var line = e.lineNumber;
	}

	if(document.getElementById('vim__'+eval('Vimofy.'+id_vimofy+'.theme')+'__hide_container_'+id_vimofy+'__').style.display == '' || document.getElementById('vim__'+eval('Vimofy.'+id_vimofy+'.theme')+'__hide_container_'+id_vimofy+'__').style.display == 'none')
	{
		vimofy_cover_with_filter(id_vimofy);
	}
	var prompt_btn = new Array([vim_lib[31]],["vimofy_cover_with_filter('"+id_vimofy+"');"]);
	
	document.getElementById('vim_msgbox_conteneur_'+id_vimofy).style.display = '';
	vimofy_generer_msgbox(id_vimofy,vim_lib[74],vim_lib[75]+' <b>'+line+'</b> - <b>'+file+'</b><br />'+title+'<br />'+more,'erreur','msg',prompt_btn);
	document.getElementById('vim__'+eval('Vimofy.'+id_vimofy+'.theme')+'__wait_'+id_vimofy+'__').style.display = 'none';
}

function msgbox(id_vimofy,title,text)
{
	vimofy_cover_with_filter(id_vimofy);
	var prompt_btn = new Array([vim_lib[31]],["vimofy_cover_with_filter('"+id_vimofy+"');"]);
	
	document.getElementById('vim_msgbox_conteneur_'+id_vimofy).style.display = '';
	vimofy_generer_msgbox(id_vimofy,title,text,'info','msg',prompt_btn);
}