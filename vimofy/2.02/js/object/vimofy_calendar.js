/**
 * Load the calendar with the date
 * @param p_year Year to load
 * @param p_month Month to load
 * @param p_day Day to load
 */
function vimofy_load_date(vimofy_id,column,p_year,p_month,p_day,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		//eval('Vimofy.'+vimofy_id+'.menu_opened_col = '+column+';');
		
		// Display load gif
		document.getElementById('calendar_load_'+vimofy_id).style.display = 'block';
		
		//vimofy_display_wait(vimofy_id);
		
		if(typeof(p_year) == 'undefined' || p_year == null)
		{
			p_year = document.getElementById('vimofy_cal_year_'+vimofy_id).value;
		}
		
		if(typeof(p_month) == 'undefined' || p_month == null)
		{
			p_month = document.getElementById('vimofy_cal_month_'+vimofy_id).value;
		}
		
		if(typeof(p_day) == 'undefined' || p_day == null)
		{
			p_day = document.getElementById('vimofy_cal_day_'+vimofy_id).value;
		}
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = eval('Vimofy.'+vimofy_id+'.dir_obj')+'/ajax/ajax_page.php';
		conf['delai_tentative'] = 10000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;														// ReponseText
		conf['param'] = 'vimofy_id='+vimofy_id+'&ssid='+eval('Vimofy.'+vimofy_id+'.ssid')+'&action=19&column='+column+'&year='+encodeURIComponent(p_year)+'&month='+encodeURIComponent(p_month)+'&day='+encodeURIComponent(p_day);
		conf['fonction_a_executer_reponse'] = 'vimofy_load_date';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+column+"','"+p_year+"','"+p_month+"','"+p_day+"'";
		
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			// Set the content to the calendar
			vimofy_set_innerHTML('vim_column_calendar_'+vimofy_id, ajax_return);

			// Hide load gif
			document.getElementById('calendar_load_'+vimofy_id).style.display = 'none';
		}
		catch(e) 
		{
			vimofy_display_error(vimofy_id,e);
		}
	}
}


/**
 * Insert the date on the column filter & search
 */
function vimofy_insert_date(vimofy_id,column,p_day)
{
	// Display load gif
	document.getElementById('calendar_load_'+vimofy_id).style.display = 'block';
	
	/**==================================================================
	 * Get date values
	 ====================================================================*/	
	// Year
	p_year = document.getElementById('vimofy_cal_year_'+vimofy_id).value;
	
	// Month
	if(document.getElementById('vimofy_cal_month_'+vimofy_id).value.length < 2)
	{
		p_month = '0'+document.getElementById('vimofy_cal_month_'+vimofy_id).value;
	}
	else
	{
		p_month = document.getElementById('vimofy_cal_month_'+vimofy_id).value;
	}

	// Day

	if(p_day.length < 2)
	{
		p_day = '0'+p_day;
	}
	else
	{
		p_day = p_day;
	}
	
	/**==================================================================*/

	/**==================================================================
	 * Set date values to the correct format
	 ====================================================================*/	
	format = eval('Vimofy.'+vimofy_id+'.columns.c'+column+'.date_format');
	date_formated = format.replace('YYYY',p_year);
	date_formated = date_formated.replace('MM',p_month);
	date_formated = date_formated.replace('DD',p_day);
	/**==================================================================*/
	
	// Close the calendar
	vimofy_close_calendar(vimofy_id);
	
	// Set the value into the search input
	document.getElementById('th_input_'+column+'__'+vimofy_id).value = date_formated;
	
	// Set the focus into the search bloc of the parent column
	document.getElementById('th_input_'+column+'__'+vimofy_id).focus();
	
	// Search
	//vimofy_define_filter(vimofy_id,encodeURIComponent(document.getElementById('th_input_'+column+'__'+vimofy_id).value),column,true);
	
}



/**
 * Close the vimofy calendar
 */
function vimofy_close_calendar(vimofy_id)
{
	document.getElementById('vim_column_calendar_'+vimofy_id).style.display = 'none';
	document.getElementById('vim_column_calendar_'+vimofy_id).innerHTML = '';
}