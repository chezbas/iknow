function vimofy_child_insert_into_parent(el,parent,parent_column)
{
	eval('Vimofy.'+parent+'.vimofy_child_opened = false;');
	
	// Get the value of the selected line and insert it into the parent vimofy
	if(document.all)
	{ 
		// IE
		document.getElementById('th_input_'+parent_column+'__'+parent).value = vimofy_get_innerHTML(el);
	}
	else
	{
		document.getElementById('th_input_'+parent_column+'__'+parent).value = document.getElementById(el).textContent;
	}

	// Set the focus into the search bloc of the parent column
	document.getElementById('th_input_'+parent_column+'__'+parent).focus();
	
	// Close the child vimofy
	document.getElementById('internal_vimofy_'+parent).style.display = 'none';
	vimofy_set_innerHTML('internal_vimofy_'+parent,'');
	
	// Kill the object of the child vimofy (json)
	eval('delete Vimofy.'+parent+'_child;');
	
	// Search for other lmod openable
	eval('Vimofy.'+parent+'.time_input_search = true;');
	vimofy_input_search(parent,document.getElementById('th_input_'+parent_column+'__'+parent).value,parent_column,false);

	if(document.getElementById('chk_edit_c'+parent_column+'_'+parent))
	{
		document.getElementById('chk_edit_c'+parent_column+'_'+parent).checked = true;
	}
	// Search
	//vimofy_define_filter(parent,encodeURIComponent(document.getElementById('th_input_'+parent_column+'__'+parent).value),parent_column,false);
	
	// Hide the cover
	vimofy_cover_with_filter(parent);
}

function vimofy_child_cancel(parent,parent_column)
{
	eval('Vimofy.'+parent+'.vimofy_child_opened = false;');
	
	// Set the focus into the seach bloc of the parent column
	document.getElementById('th_input_'+parent_column+'__'+parent).focus();
	
	// Close the child vimofy
	document.getElementById('internal_vimofy_'+parent).style.display = 'none';
	vimofy_set_innerHTML('internal_vimofy_'+parent,'');
	
	// Kill the object of the child vimofy (json)
	eval('delete Vimofy.'+parent+'_child;');
	
	vimofy_cover_with_filter(parent);
}

/**
 * @param id_vimofy
 * @param vimofy_type
 */
function vimofy_display_internal_vim(vimofy_id,vimofy_type,column,ajax_return)
{
	var is_lovable = eval('Vimofy.'+vimofy_id+'.columns.c'+column+'.is_lovable');
	
	if(is_lovable != undefined && is_lovable == true)
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
			conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&column='+column+'&vimofy_type='+vimofy_type;
			conf['fonction_a_executer_reponse'] = 'vimofy_display_internal_vim';
			conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+vimofy_type+"',"+column;
			
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
				vimofy_set_style_display('internal_vimofy_'+vimofy_id,'block');
				
				// Set the vimofy opened child flag
				eval('Vimofy.'+vimofy_id+'.vimofy_child_opened = '+column+';');
				
				// Update the json object
				eval(decodeURIComponent(json.vimofy.json));
				
				// Set the content of the Vimofy
				vimofy_set_innerHTML('internal_vimofy_'+vimofy_id,decodeURIComponent(json.vimofy.content));
				
				vimofy_set_el_width('internal_vimofy_'+vimofy_id,500,'px');
				
				if(document.getElementById('liste_'+vimofy_id).offsetHeight < 422)
				{
					document.getElementById('internal_vimofy_'+vimofy_id).style.height = document.getElementById('liste_'+vimofy_id).offsetHeight+22+'px';
					document.getElementById('vim__'+eval('Vimofy.'+vimofy_id+'.theme')+'__vimofy_table_'+vimofy_id+'_child__').style.height = document.getElementById('liste_'+vimofy_id).offsetHeight+'px';
					
				}
				else
				{
					vimofy_set_el_height('internal_vimofy_'+vimofy_id,318,'px');
				}
				
				
				
				
				// Set the size of the internal vimofy
			
				
				
				var pos = vimofy_getPosition('th_menu_'+column+'__'+vimofy_id);
				
				var pos_th = document.getElementById('th_menu_'+column+'__'+vimofy_id).offsetLeft - document.getElementById('liste_'+vimofy_id).scrollLeft;
				document.getElementById('internal_vimofy_'+vimofy_id).style.left =  pos_th-5+'px';
				
				
				/**================================================================== 
				 * Test the position of the child vimofy
				 * ====================================================================*/
				// Get the position of the child vimofy
				var pos_child_vimofy = vimofy_getPosition('internal_vimofy_'+vimofy_id); 
				
				// Get the position of the parent vimofy
				var pos_vimofy = vimofy_getPosition('vim__'+eval('Vimofy.'+vimofy_id+'.theme')+'__vimofy_table_'+vimofy_id+'__'); 
		
				// Test if the menu is not out of the left corner of the vimofy
				if((pos_child_vimofy[0]+500) > (pos_vimofy[0]+vimofy_get_el_offsetWidth('vim__'+eval('Vimofy.'+vimofy_id+'.theme')+'__vimofy_table_'+vimofy_id+'__')))
				{
					document.getElementById('internal_vimofy_'+vimofy_id).style.left = '';
					document.getElementById('internal_vimofy_'+vimofy_id).style.right = 0+'px';
				}
				/** ================================================================== */
				
				eval('Vimofy.'+vimofy_id+'.menu_left = '+document.getElementById('internal_vimofy_'+vimofy_id).offsetLeft+';');
				size_table(vimofy_id+'_child');
			} 
			catch(e) 
			{
				vimofy_display_error(vimofy_id,e);
			}
		}
	}
}