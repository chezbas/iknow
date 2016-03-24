function vimofy_lmod_click(vimofy_id,p_line,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		if(typeof(p_line) == 'undefined') p_line = null;
		
		if(document.getElementById('vimofy_lmod_'+vimofy_id).style.display == 'none' || document.getElementById('vimofy_lmod_'+vimofy_id).style.display == '')
		{
			// Load the vimofy
			/**==================================================================
			 * Ajax init
			 ====================================================================*/	
			var conf = new Array();	

			conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_lmod.php';
			conf['delai_tentative'] = 15000;
			conf['max_tentative'] = 4;
			conf['type_retour'] = false;		// ReponseText
			conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=1';
			conf['fonction_a_executer_reponse'] = 'vimofy_lmod_click';
			conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"',"+p_line;
			ajax_call(conf);
			/**==================================================================*/
		}
		else
		{
			// A line was clicked, insert the line.
			
			// Hide the vimofy
			document.getElementById('vimofy_lmod_'+vimofy_id).style.display = 'none';
			document.getElementById('vimofy_lmod_'+vimofy_id).style.left = '';
			document.getElementById('vimofy_lmod_'+vimofy_id).style.right = '';
			document.getElementById('vimofy_lmod_'+vimofy_id).style.top = '';
			
			// Turn off the flag lmod opened
			eval('Vimofy.'+vimofy_id+'.lmod_opened = false;');

			if(p_line != null)
			{
				// A line was clicked, insert the line.
				document.getElementById('lst_'+vimofy_id).value = vimofy_get_innerHTML('div_td_l'+p_line+'_c'+eval('Vimofy.'+vimofy_id+'.c_col_return_id')+'_'+vimofy_id);
                vimofy_execute_event(__ON_LMOD_INSERT__,__AFTER__,vimofy_id);
			}
		}
	}
	else
	{
		try 
		{	
			// Get the ajax return in json format
			var json = get_json(ajax_return);
			
			vimofy_set_innerHTML('lmod_vimofy_container_'+vimofy_id,decodeURIComponent(json.vimofy.content));
			vimofy_lmod_place(vimofy_id);
			
			
			// Update the json object
			eval(decodeURIComponent(json.vimofy.json));
			
			document.getElementById('liste_'+vimofy_id).onscroll = function(){vimofy_horizontal_scroll(vimofy_id);};
			size_table(vimofy_id);
			eval('Vimofy.'+vimofy_id+'.lmod_opened = true;');
			vimofy_execute_event(__ON_LMOD_OPEN__,__AFTER__,vimofy_id);
			
		} 
		catch(e) 
		{
			alert(e.message);
			// vimofy_display_error(vimofy_id,e);
		}
	}
}

function vimofy_lmod_place(vimofy_id)
{
	var pos = vimofy_getPosition('lst_'+vimofy_id);
	document.getElementById('vimofy_lmod_'+vimofy_id).style.display = 'block';
	if(eval('Vimofy.'+vimofy_id+'.c_position_mode') == '__RELATIVE__')
	{
		// __RELATIVE__
		document.getElementById('vimofy_lmod_'+vimofy_id).style.top = pos[1] + document.getElementById('lst_'+vimofy_id).offsetHeight + 'px';
		document.getElementById('vimofy_lmod_'+vimofy_id).style.left = pos[0]-7+'px';
		
		/**================================================================== 
		 * Test the position of the vimofy
		 * ====================================================================*/
		// Get the position of the vimofy
		var pos_vimofy = vimofy_getPosition('vimofy_lmod_'+vimofy_id); 
		// Get the width of the screen
		var body_width = document.body.offsetWidth;
		// Test if the vimofy is not out of the left corner of the screen
		if(pos_vimofy[0]+document.getElementById('vimofy_lmod_'+vimofy_id).offsetWidth > body_width)
		{
			document.getElementById('vimofy_lmod_'+vimofy_id).style.left = '';
			document.getElementById('vimofy_lmod_'+vimofy_id).style.right = 0+'px';
			//alert('trop grand\nvimofy : '+(pos_vimofy[0]+document.getElementById('vimofy_lmod_'+vimofy_id).offsetWidth)+'\nbody : '+body_width);
		}
		/** ================================================================== */
	}
	else
	{
		// __ABSOLUTE__
		document.getElementById('vimofy_lmod_'+vimofy_id).style.top = document.getElementById('lst_'+vimofy_id).offsetTop + document.getElementById('lst_'+vimofy_id).offsetHeight + 'px';
		var pos_lmod = vimofy_getPosition('vimofy_lmod_'+vimofy_id);
		document.getElementById('vimofy_lmod_'+vimofy_id).style.left = pos[0] - pos_lmod[0]-7+'px';
	}
	
}

function vimofy_clear_value(vimofy_id)
{
	try
	{
		document.getElementById(vimofy_id).value = '';
	}
	catch(e)
	{
		alert('ID '+vimofy_id+' doesn\'t exist');
	}
}