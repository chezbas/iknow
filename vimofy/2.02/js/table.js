/**
 * Get the size of the header field
 * @param column column to get header size
 * @param vimofy_id vimofy id
 * @returns {Number}
 */
function vimofy_get_size_header(column,vimofy_id)
{
	/**==================================================================
	 * Get the size of the header
	 ====================================================================*/	
	var PosHeader_th0 = vimofy_getPosition('th_0_'+column+'_'+vimofy_id);
	var PosHeader_th4 = vimofy_getPosition('th_4_'+column+'_'+vimofy_id);
	var size_header = PosHeader_th4[0] - PosHeader_th0[0];
	
	return size_header;
	/**==================================================================*/	
}

/**
 * Get the size of the data field
 * @param column column to get data size
 * @param vimofy_id vimofy id
 * @returns {Number}
 */
function vimofy_get_size_data(column,vimofy_id)
{
	/**==================================================================
	 * Get the size of the data
	 ====================================================================*/	
	var PosData_th0 = vimofy_getPosition('td_0_'+column+'_'+vimofy_id);
	var PosData_th3 = vimofy_getPosition('td_3_'+column+'_'+vimofy_id);
	var size_data = PosData_th3[0] - PosData_th0[0];
	
	return size_data;
	/**==================================================================*/	
}



/**
 * Set the innerHTML of an element
 * @param el element id
 * @param val Value to set
 * @returns
 */
function vimofy_set_innerHTML(el,val)
{
	document.getElementById(el).innerHTML = val;
}

/**
 * Set the display style of an element
 * @param el element id
 * @param val Value to set
 * @returns
 */
function vimofy_set_style_display(el,val)
{
	document.getElementById(el).style.display = val;
}

/**
 * Size the table of the vimofy
 * @param vimofy_id Vimofy ID
 */
function size_table(vimofy_id)
{
	if(eval('Vimofy.'+vimofy_id+'.qtt_line') > 0)
	{
		/**==================================================================
		 * Size the first column (checkbox column)
		 ====================================================================*/	
		if(vimofy_get_el_offsetWidth('div_td_l1_c0_'+vimofy_id) > vimofy_get_el_offsetWidth('th0_'+vimofy_id))
		{
			// Data is larger than header, resize header
			vimofy_set_el_width('th0_'+vimofy_id,vimofy_get_el_offsetWidth('div_td_l1_c0_'+vimofy_id),'px');
		}
		else
		{
			// Header is larger than data, resize data
			vimofy_set_el_width('div_td_l1_c0_'+vimofy_id,vimofy_get_el_offsetWidth('th0_'+vimofy_id),'px');
		}
		/**==================================================================*/	
		
		// Size min of a column (in px)
		var size_min = 100;
		
		/**==================================================================
		 * Browse and size all columns
		 ====================================================================*/	
		for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns')) 
		{
			if(eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id') != undefined)
			{
				element_id = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id');
				/**==================================================================
				 * Get the size of the header
				 ====================================================================*/	
				var size_header = vimofy_get_size_header(iterable_element,vimofy_id);
				/**==================================================================*/	
				
				/**==================================================================
				 * Get the size of the data
				 ====================================================================*/	
				var size_data = vimofy_get_size_data(iterable_element,vimofy_id);
				/**==================================================================*/	
				
				if(size_data < size_min && size_header < size_min)
				{
					// Header and data are too short. Initialise to the size_min value
					
					// Resize the header
					vimofy_set_el_width('th'+element_id+'_'+vimofy_id,vimofy_get_el_offsetWidth('th'+element_id+'_'+vimofy_id)+size_min-size_header,'px');
					
					// Resize the data (-11 is the size of td_0_c1_vimofy_bug + td_1_c1_vimofy_bug padding + td_2_c1_vimofy_bug + td_3_c1_vimofy_bug)
					vimofy_set_el_width('div_td_l1_c'+element_id+'_'+vimofy_id,vimofy_get_size_header(iterable_element,vimofy_id)-11,'px');
				}
				else
				{
					if(size_data >= size_header)
					{
						// Data content is larger than header
						if(vimofy_column_in_resize == true && vimofy_column_resize == element_id)
						{
							/**==================================================================
							 * Column manual resize
							 ====================================================================*/	
							if(size_header < size_min)
							{
								// Header is too short. Initialise to the size_min value
								
								// Resize the header
								vimofy_set_el_width('th'+element_id+'_'+vimofy_id,vimofy_get_el_offsetWidth('th'+element_id+'_'+vimofy_id)+size_min-size_header,'px');
								
								// resize data content
								vimofy_set_el_width('div_td_l1_c'+element_id+'_'+vimofy_id,vimofy_get_size_header(iterable_element,vimofy_id)-11,'px');
							}
							else
							{
								// resize data content
								window.status = size_header-(size_header-vimofy_get_el_offsetWidth('th'+element_id+'_'+vimofy_id));
								vimofy_set_el_width('div_td_l1_c'+element_id+'_'+vimofy_id,vimofy_get_size_header(iterable_element,vimofy_id)-11,'px');
							}
							
							
							// Control the size of the data
							if(vimofy_get_el_offsetWidth('div_td_l1_c'+element_id+'_'+vimofy_id) < vimofy_get_el_offsetWidth('div_td_l2_c'+element_id+'_'+vimofy_id))
							{
								// Resize the header
								vimofy_set_el_width('th'+element_id+'_'+vimofy_id,(vimofy_get_size_data(iterable_element,vimofy_id)-(vimofy_get_size_header(iterable_element,vimofy_id)-vimofy_get_el_offsetWidth('th'+element_id+'_'+vimofy_id))),'px');
								vimofy_set_el_width('div_td_l1_c'+element_id+'_'+vimofy_id,vimofy_get_el_offsetWidth('div_td_l2_c'+element_id+'_'+vimofy_id),'px');
							}
							/**==================================================================*/	
						}
						else
						{
							// Set header to the size of data
							vimofy_set_el_width('th'+element_id+'_'+vimofy_id,vimofy_get_el_offsetWidth('th'+element_id+'_'+vimofy_id)+size_data-size_header,'px');
						}
					}
					else
					{
						// Header content is larger than data, set data to the size of the header
						vimofy_set_el_width('div_td_l1_c'+element_id+'_'+vimofy_id,vimofy_get_size_header(iterable_element,vimofy_id)-11,'px');
					}
				}
			}
		}
		/**==================================================================*/	
	
		/**==================================================================
		 * Control free size
		 ====================================================================*/	
		// Get the free size of the vimofy
		free_size = vimofy_get_el_clientWidth('liste_'+vimofy_id) - vimofy_get_el_clientWidth('table_liste_'+vimofy_id);
	
		if(free_size > 0)
		{
			// Free size available, share free size on all columns
		
			var qtt_column = eval('Vimofy.'+vimofy_id+'.qtt_column');
			
			// Get the size to add on each column
			var size = Math.floor(free_size / qtt_column);
			
			// Get the free size residue
			var residue = free_size - (size * qtt_column);
			
			/**==================================================================
			 * Browse all column
			 ====================================================================*/	
			for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns')) 
			{
				// Get the element id
				element_id = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id');
				
				if(element_id != undefined && (vimofy_get_el_offsetWidth('liste_'+vimofy_id) - (vimofy_get_el_offsetWidth('l1_'+vimofy_id) + size)) >= 0)
				{
					(residue > 1) ? res = 1 : res = 0;
					
					// Set data size
					vimofy_set_el_width('div_td_l1_c'+element_id+'_'+vimofy_id,(vimofy_get_el_offsetWidth('div_td_l1_c'+element_id+'_'+vimofy_id) + size + res),'px');
					
					// Set header size
					vimofy_set_el_width('th'+element_id+'_'+vimofy_id,vimofy_get_el_offsetWidth('th'+element_id+'_'+vimofy_id)+size+res,'px');
					
					residue--;
				}
			}
			/**==================================================================*/	
		}
		/**==================================================================*/	
	
		/**==================================================================
		 * Initialise position flag of the columns
		 ====================================================================*/	
		element_id = 0;
		for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns')) 
		{
			if(eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id') != undefined)
			{
				if(element_id == 0) 
				{
					i_last = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id');
				}
				else
				{
					i_last = element_id;
				}
				
				// Get the element id
				element_id = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id;');
		
				if(element_id == 1)
				{
					eval('Vimofy.'+vimofy_id+'.columns.c'+element_id+'.min = 0;');
				}
				else
				{
					eval('Vimofy.'+vimofy_id+'.columns.c'+element_id+'.min = '+eval('Vimofy.'+vimofy_id+'.columns.c'+(i_last)+'.max +1'));
				}
				
				/**==================================================================
				 * Get the total header width of the column
				 ====================================================================*/	
				var width = vimofy_get_size_header(iterable_element,vimofy_id);
				/**==================================================================*/
				
				// Get the left position of the column
				var left = document.getElementById('th_0_c'+element_id+'_'+vimofy_id).offsetLeft;
				
				eval('Vimofy.'+vimofy_id+'.columns.c'+element_id+'.max = '+((width/2)+left));
				eval('Vimofy.'+vimofy_id+'.columns.c'+element_id+'.arrow = '+left);
			}
		}
		/**==================================================================
		 * Initialise position flag of the last column
		 ====================================================================*/	
		last_col = eval('Vimofy.'+vimofy_id+'.last_column;');
		eval('Vimofy.'+vimofy_id+'.columns.c'+(last_col)+' = new Object();');
		eval('Vimofy.'+vimofy_id+'.columns.c'+(last_col)+'.min = '+eval('Vimofy.'+vimofy_id+'.columns.c'+element_id+'.max +1'));
		eval('Vimofy.'+vimofy_id+'.columns.c'+(last_col)+'.max = '+document.getElementById('th_0_c'+(last_col)+'_'+vimofy_id).offsetLeft);
		eval('Vimofy.'+vimofy_id+'.columns.c'+(last_col)+'.arrow = '+document.getElementById('th_0_c'+(last_col)+'_'+vimofy_id).offsetLeft);
		/**==================================================================*/
	
		/**==================================================================*/
	}
	else
	{
		// No line on the vimofy
		/**==================================================================
		 * Control free size
		 ====================================================================*/	
		free_size = 0;
		
		// Get the free size of the vimofy header
		free_size = vimofy_get_el_clientWidth('liste_'+vimofy_id) - vimofy_get_el_clientWidth('tr_header_input_'+vimofy_id)+203;
		
		if(free_size > 0)
		{
			// Free size available, share free size on all columns
		
			var qtt_column = eval('Vimofy.'+vimofy_id+'.qtt_column');
			
			// Get the size to add on each column
			var size = Math.floor(free_size / qtt_column);
			// Get the free size residue
			var residue = free_size - (size * qtt_column);
			/**==================================================================
			 * Browse all column
			 ====================================================================*/	
			for(var iterable_element in eval('Vimofy.'+vimofy_id+'.columns')) 
			{
				// Get the element id
				element_id = eval('Vimofy.'+vimofy_id+'.columns.'+iterable_element+'.id');

				if(element_id != undefined)
				{
					(residue > 1) ? res = 1 : res = 0;
					// Set header size
					vimofy_set_el_width('th'+element_id+'_'+vimofy_id,vimofy_get_el_offsetWidth('th'+element_id+'_'+vimofy_id)+size+res,'px');
					
					residue--;
				}
			}
			/**==================================================================*/	
		}
		/**==================================================================*/	
	}
}