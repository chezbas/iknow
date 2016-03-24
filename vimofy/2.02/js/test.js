/**
 * Handler when keydown on column input search 
 * @param evt event
 * @param el element
 * @param vimofy_id ID of the Vimofy
 * @param column Column of the vimofy
 */
function vimofy_input_keydown(evt,el,vimofy_id,column)
{
	try 
	{	
		if((evt.keyCode != 13 && evt.keyCode != 9 && evt.keyCode != 40 && evt.keyCode != 39 && evt.keyCode != 38 && evt.keyCode != 37 && evt.keyCode != 27 && evt.keyCode != 18 && evt.keyCode != 16 && evt.keyCode != 20))		
		{
			/**==================================================================
			 * A key was pressed (leter,number,backspace or espace)
			 ====================================================================*/	
			// Clear the last timeout
			eval('clearTimeout(Vimofy.'+vimofy_id+'.time_input_search);');
	
			// Set a timeout before send the query
			eval('Vimofy.'+vimofy_id+'.time_input_search = setTimeout(\'vimofy_input_search(\\\''+vimofy_id+'\\\',\\\''+encodeURIComponent(el.value).replace(/\'/g,"\\\\\\\\\\\\\\'")+'\\\','+column+')\', 750)');
			/**==================================================================*/
			
			document.getElementById('liste_'+vimofy_id).scrollLeft = document.getElementById('header_'+vimofy_id).scrollLeft;		
		}
		else
		{
			if(evt.keyCode == 40)
			{
				/**==================================================================
				 * Bottom arrow pressed
				 ====================================================================*/	
				if(eval('Vimofy.'+vimofy_id+'.menu_quick_search') && eval('Vimofy.'+vimofy_id+'.input_search_selected_line') < 6)
				{
					if(eval('Vimofy.'+vimofy_id+'.input_search_selected_line') == 0)
					{
						document.getElementById('vimofy_input_result_1_'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_column_header_input_result';
					}
					
					if(eval('Vimofy.'+vimofy_id+'.input_search_selected_line') < 6)
					{
						if(eval('Vimofy.'+vimofy_id+'.input_search_selected_line') > 0)
							document.getElementById('vimofy_input_result_'+eval('Vimofy.'+vimofy_id+'.input_search_selected_line')+'_'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_column_header_input_result';
						eval('Vimofy.'+vimofy_id+'.input_search_selected_line += 1');
						document.getElementById('vimofy_input_result_'+eval('Vimofy.'+vimofy_id+'.input_search_selected_line')+'_'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_column_header_input_result_hover';
					}
				}
				/**==================================================================*/	
			}
			else
			{
				if(evt.keyCode == 38)
				{
					/**==================================================================
					 * Top arrow pressed
					 ====================================================================*/	
					if(eval('Vimofy.'+vimofy_id+'.input_search_selected_line') > 1)
					{
						document.getElementById('vimofy_input_result_'+eval('Vimofy.'+vimofy_id+'.input_search_selected_line')+'_'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_column_header_input_result';
						eval('Vimofy.'+vimofy_id+'.input_search_selected_line -= 1');
						document.getElementById('vimofy_input_result_'+eval('Vimofy.'+vimofy_id+'.input_search_selected_line')+'_'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_column_header_input_result_hover';
					}
					/**==================================================================*/	
				}
				else
				{
					if(evt.keyCode == 27)
					{
						// esc key
						vimofy_cancel_edit(vimofy_id);
					}
					else
					{
						// Other key was pressed, close the input search result
						eval('clearTimeout(Vimofy.'+vimofy_id+'.time_input_search);');
						if(evt.keyCode == 13)
						{
							//eval('clearTimeout(Vimofy.'+vimofy_id+'.time_input_search);');
							//toggle_wait_input(vimofy_id,column);
							/**==================================================================
							 * Cariage return pressed
							 ====================================================================*/
							if(eval('Vimofy.'+vimofy_id+'.edit_mode') == __EDIT_MODE__)
							{
								//eval('clearTimeout(Vimofy.'+vimofy_id+'.time_input_search);');
								save_lines(vimofy_id);
							}
							else
							{
								if(eval('Vimofy.'+vimofy_id+'.input_search_selected_line') > 0)
								{
									eval('document.getElementById(\''+vimofy_id+'_rapid_search_l'+eval('Vimofy.'+vimofy_id+'.input_search_selected_line')+'\').onclick();');
								}
								else
								{
									if(eval('Vimofy.'+vimofy_id+'.menu_quick_search') && eval('Vimofy.'+vimofy_id+'.input_search_selected_line') == 0)
									{
										vimofy_define_filter(vimofy_id,encodeURIComponent(el.value),column,true);
									}
									else
									{
										vimofy_define_filter(vimofy_id,encodeURIComponent(el.value),column,true);
									}
								}
							}
							/**==================================================================*/	
						}
						else
						{
							if(evt.keyCode != 16)
							{
								close_input_result(vimofy_id);
							}
							else
							{
								//alert("passe");
							}
						}
					}
				}
			}
		}
	} 
	catch(e) 
	{
		vimofy_display_error(vimofy_id,e,'input_col_1');
	}
}

function toggle_wait_input(vimofy_id,column)
{
	if(document.getElementById('wait_input_'+vimofy_id).style.display != 'block')
	{
		// Display
		/**==================================================================
		 * Get the position of the vimofy and the input
		 ====================================================================*/	
		try {
			var pos_input = vimofy_getPosition('th_input_'+column+'__'+vimofy_id); 
		} catch (e) {
			alert('th_input_'+column+'__'+vimofy_id);
		}
		
		var pos_vimofy = vimofy_getPosition('vim__'+eval('Vimofy.'+vimofy_id+'.theme')+'__vimofy_table_'+vimofy_id+'__'); 
		/**==================================================================*/	
		
		/**==================================================================
		 * Set the size of the wait picture
		 ====================================================================*/	
		document.getElementById('wait_input_'+vimofy_id).style.width = document.getElementById('th_input_'+column+'__'+vimofy_id).offsetWidth-4+'px';
		/**==================================================================*/	
		
		/**==================================================================
		 * Vertical placement
		 ====================================================================*/	
		document.getElementById('wait_input_'+vimofy_id).style.top = pos_input[1]-pos_vimofy[1]+document.getElementById('th_input_'+column+'__'+vimofy_id).offsetHeight-6+'px';
		/**==================================================================*/	

		/**==================================================================
		 * Horizontal placement
		 ====================================================================*/	
		document.getElementById('wait_input_'+vimofy_id).style.left = pos_input[0]-pos_vimofy[0]-document.getElementById('liste_'+vimofy_id).scrollLeft+'px';
		/**==================================================================*/
		
		/**==================================================================
		 * Display the wait picture
		 ====================================================================*/	
		document.getElementById('wait_input_'+vimofy_id).style.display = 'block';
		/**==================================================================*/
	}
	else
	{
		/**==================================================================
		 * Hide the wait picture
		 ====================================================================*/	
		document.getElementById('wait_input_'+vimofy_id).style.display = 'none';
		/**==================================================================*/
	}
}


/**
 * Handler when the input of the vimofy change
 * @param vimofy_id ID of the Vimofy
 * @param column Column of the vimofy
 */
function vimofy_col_input_change(vimofy_id,column)
{
	vimofy_define_filter(vimofy_id,document.getElementById(encodeURIComponent('th_input_'+column+'__'+vimofy_id)).value,column,false);
}

 /**
  * Search on the column result
  * @param vimofy_id ID of the Vimofy
  * @param txt text to search
  * @param column Column of the vimofy
  * @param ajax_return response of the server
  */
function vimofy_input_search(vimofy_id,txt,column,quick_search,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
Z		if(typeof(quick_search) == 'undefined') quick_search = true;
		toggle_wait_input(vimofy_id,column);

		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	

		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=5&column='+column+'&txt='+txt+'&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_input_search';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+txt+"',"+column+','+quick_search;
		ajax_call(conf);
		/**==================================================================*/
		
		wait_column_bullet(vimofy_id);
	}
	else
	{
		try 
		{
			toggle_wait_input(vimofy_id,column);

			if(eval('Vimofy.'+vimofy_id+'.time_input_search;') != eval('false'))
			{
				/**==================================================================
				 * Display the query result
				 ====================================================================*/	
				
				// Get the ajax return in json format
				var json = get_json(ajax_return);

				// Update the json object
				eval(decodeURIComponent(json.vimofy.json));
				
				update_column_bullet(vimofy_id);
				
				if(quick_search)
				{
					// Quick search flag
					eval('Vimofy.'+vimofy_id+'.menu_quick_search = true;');
					eval('Vimofy.'+vimofy_id+'.menu_quick_search_col = column;');
					
					var div_menu = document.getElementById('vim_column_header_menu_'+vimofy_id);
	
					if(typeof(json.vimofy.content) != 'object')
					{
						// Prepare the div
						html = '<table class="shadow">';
						html += '<tr>';
						html += '<td class="shadow_l"></td>';
						html += '<td colspan=2 class="shadow">';
						html += decodeURIComponent(json.vimofy.content);
						html += '</td></tr>';
						html += '<tr><td class="shadow_l_b"></td><td class="shadow_b"></td><td class="shadow_r_b"></td></tr></table>';
						 
						// Set the result to the menu
						div_menu.innerHTML = html;
				
						// Display the menu
						div_menu.style.display = 'block';
						
						// Positions the menu
						position_input_result(vimofy_id,column);
						
						// Initialise the selected line
						eval('Vimofy.'+vimofy_id+'.input_search_selected_line = 0');
					}
					else
					{
						if(div_menu.style.display == 'block')
						{
							div_menu.style.display = 'none';
						}
					}
				}
				// Re-Activate the vimofy horizontal scroll
				document.getElementById('liste_'+vimofy_id).onscroll = function(){vimofy_horizontal_scroll(vimofy_id);};
				/**==================================================================*/
			}
		} 
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e,'input_col_2');
		}
	}
}

function update_column_bullet(vimofy_id)
{
	try 
	{
		for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns'))
		{
			if(document.getElementById('th_menu_'+eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id')+'__'+vimofy_id))
			{
				var is_lovable = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.is_lovable');
				var lov_perso = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.lov_perso');
				
				if(lov_perso != undefined && is_lovable != undefined && is_lovable == true)
				{
					document.getElementById('th_menu_'+eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id')+'__'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_menu_header_lovable __'+eval('Vimofy.'+vimofy_id+'.theme')+'_men_head';
				}
				else
				{
					if(lov_perso != undefined)
					{
						document.getElementById('th_menu_'+eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id')+'__'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_men_head';
					}
					else
					{
						
						document.getElementById('th_menu_'+eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id')+'__'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_menu_header __'+eval('Vimofy.'+vimofy_id+'.theme')+'_men_head';
					}
				}
			}
		}
	}
	catch(e) 
	{
		vimofy_display_error(vimofy_id,e,'input_col_3  '+'th_menu_'+eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id'));
	}
}

function wait_column_bullet(vimofy_id)
{
	for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns'))
	{
		var is_lovable = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.is_lovable');
		var lov_perso = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.lov_perso');
		
		if(lov_perso != undefined && is_lovable != undefined && is_lovable == true)
		{
			document.getElementById('th_menu_'+eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id')+'__'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_menu_header_check_is_lovable __'+eval('Vimofy.'+vimofy_id+'.theme')+'_men_head';
		}
		else
		{
			if(lov_perso != undefined)
			{
				document.getElementById('th_menu_'+eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id')+'__'+vimofy_id).className = '__'+eval('Vimofy.'+vimofy_id+'.theme')+'_menu_header_check_is_lovable __'+eval('Vimofy.'+vimofy_id+'.theme')+'_men_head';
			}
		}
	}	
}


/**
 * Search on the column result
 * @param vimofy_id ID of the Vimofy
 * @param txt text to search
 * @param column Column of the vimofy
 * @param ajax_return response of the server
 */
function vimofy_define_filter(vimofy_id,txt,column,display,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * Get all updated filter
		 ====================================================================*/	
		url_filter = '&filter_col='+column+'&filter='+encodeURIComponent(txt);
		/**==================================================================*/
		
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 15000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=6'+url_filter+'&selected_lines='+encodeURIComponent(get_selected_lines(vimofy_id));
		conf['fonction_a_executer_reponse'] = 'vimofy_define_filter';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"',null,"+column+','+display;

		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		// Get the ajax return in json format
		var json = get_json(ajax_return);

		// Update the json object
		try
		{
			if(eval('Vimofy.'+vimofy_id+'.menu_quick_search'))
			{
				var quick_search = true;
				var quick_search_column = eval('Vimofy.'+vimofy_id+'.menu_quick_search_col');
			}
			else
			{
				var quick_search = false;
			}

			if(display)
			{
				eval(decodeURIComponent(json.vimofy.json));
				eval(decodeURIComponent(json.vimofy.json_line));
			}
			
			update_column_bullet(vimofy_id);
			
			if(quick_search)
			{
				eval('Vimofy.'+vimofy_id+'.menu_quick_search = true;');
				eval('Vimofy.'+vimofy_id+'.menu_quick_search_col = '+quick_search_column);
			}
			
			if(display)
			{
				vimofy_refresh_page_ajax(vimofy_id);
			}
		}
		catch(e)
		{
			vimofy_display_error(vimofy_id,e,'input_col_4');
		}
	}
}

/**
 * Handler when a line result was clicked
 * @param vimofy_id ID of the Vimofy
 * @param column Column of the vimofy
 * @param line Line of the result
 */
function vimofy_input_result_click(vimofy_id,column,line,txt)
{
	// Insert the text clicked on the input search
	document.getElementById('th_input_'+column+'__'+vimofy_id).value = txt;
	
	// Close the search result
	close_input_result(vimofy_id);
	
	// Search
	vimofy_define_filter(vimofy_id,encodeURIComponent(document.getElementById('th_input_'+column+'__'+vimofy_id).value),column,true);
}


/**
 * Close the column input result
 * @param vimofy_id ID of the Vimofy
 */
function close_input_result(vimofy_id)
{
	// Unset the data of the menu
	vimofy_set_innerHTML('vim_column_header_menu_'+vimofy_id,'');
	
	// Hide the menu
	document.getElementById('vim_column_header_menu_'+vimofy_id).style.display = 'none';
	
	// Reset the selected line
	eval('Vimofy.'+vimofy_id+'.input_search_selected_line = 0;');
	
	// Quick search flag
	eval('Vimofy.'+vimofy_id+'.menu_quick_search = false;');
	eval('Vimofy.'+vimofy_id+'.menu_quick_search_col = false;');
}


/**
 * Place the input result correctly on the vimofy
 * @param id ID of the Vimofy
 * @param column Column of the menu
 */
function position_input_result(id,column)
{
	/**==================================================================
	 * Vertical placement
	 ====================================================================*/	
	var top_container = document.getElementById('conteneur_menu_'+id).offsetTop;
	document.getElementById('vim_column_header_menu_'+id).style.top = top_container+'px';
	/**==================================================================*/	
	
	/**==================================================================
	 * Horizontal placement
	 ====================================================================*/	
	var pos_th = document.getElementById('th_2_c'+column+'_'+id).offsetLeft - document.getElementById('liste_'+id).scrollLeft;
	document.getElementById('vim_column_header_menu_'+id).style.left = pos_th+'px';
	/**==================================================================*/

	/**==================================================================
	 * Test the position of the menu
	 ====================================================================*/	
	// Get the position of the menu
	var pos_menu = vimofy_getPosition('vim_column_header_menu_'+id); 
	
	// Get the position of the vimofy
	var pos_vimofy = vimofy_getPosition('vim__'+eval('Vimofy.'+id+'.theme')+'__vimofy_table_'+id+'__'); 

	// Test if the menu is not out of the left corner of the vimofy
	if(pos_vimofy[0] > pos_menu[0])
	{
		document.getElementById('vim_column_header_menu_'+id).style.left = 0+'px';
	}	
	/**==================================================================*/
	
	eval('Vimofy.'+id+'.menu_left = '+document.getElementById('vim_column_header_menu_'+id).offsetLeft+';');
}