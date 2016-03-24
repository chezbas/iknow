	var g_timer_increment=0;
	
	// Y = Ln(X^3)/12
	var a_points = new Array(0.000,0.154,0.327,0.428,0.500,0.556,0.602,0.640,0.674,0.703,0.729,0.753,0.775,0.795,0.814,0.831,0.847,0.862,0.876,0.890,0.903,0.915,0.927,0.938,0.948,0.959,0.968,0.978,0.987,0.996,1);
	
	/**
	* Change l'état du panel (plier/déplier)
	*/
	function iknow_toggle_el(el_container,el_content)
	{
		if(document.getElementById(el_container).style.display != 'block')
		{
			//timer = 'g_timer_'+el_container+'_toggle';
			// Afficher
			if(eval('g_timer_'+el_container+'_toggle') == null)iknow_ellapse_el(el_container,el_content);
			
		}
		else
		{
			// Masquer
			//alert('masquer 1');
			if(eval('g_timer_'+el_container+'_toggle') == null)iknow_collapse_el(el_container,el_content); 
		}	
	}
	
	/**
	* Change l'état du panel (plier/déplier)
	*/
	function iknow_ellapse_el(el_container,el_content)
	{
		if(eval('g_timer_'+el_container+'_toggle') != null) return true;
		eval('g_'+el_container+'_timer_increment = 0;');
		eval('g_timer_'+el_container+'_toggle = new iknow_timer(20,\'iknow_ellapse_el_event(\\\''+el_container+'\\\',\\\''+el_content+'\\\')\');');
		eval('g_timer_'+el_container+'_toggle.start();');
		var info = document.getElementById(el_container).style;
		
		// Afficher
		document.getElementById(el_container).style.height='0px';
		info.display = 'block';
		
		eval('g_height_'+el_container+' = '+document.getElementById(el_content).offsetHeight);
		if(eval('g_height_'+el_container) > 200)
		{	
			eval('g_height_'+el_container+'_max = 200;');
		}
		else
		{
			eval('g_height_'+el_container+'_max = g_height_'+el_container);
		}
	}
	
	
	
	function iknow_collapse_el(el_container,el_content)
	{
		if(eval('g_timer_'+el_container+'_toggle') != null) return true;
		eval('g_height_'+el_container+'_max = '+document.getElementById(el_container).offsetHeight);
		eval('g_timer_'+el_container+'_toggle = new iknow_timer(20,\'iknow_collapse_el_event(\\\''+el_container+'\\\',\\\''+el_content+'\\\')\');');
		eval('g_timer_'+el_container+'_toggle.start();');
		eval('g_'+el_container+'_timer_increment = '+a_points.length+';');
	}
	
	
	function iknow_ellapse_el_event(el_container,el_content)
	{
		if(eval('g_'+el_container+'_timer_increment') == a_points.length)
		{
			eval('g_timer_'+el_container+'_toggle.stop();');
			document.getElementById(el_container).style.height=eval('g_height_'+el_container)+'px';
			eval('g_timer_'+el_container+'_toggle = null');
			return true;
		}
		document.getElementById(el_container).style.height = (eval('g_height_'+el_container+'_max')*a_points[eval('g_'+el_container+'_timer_increment')])+'px';
		eval('g_'+el_container+'_timer_increment += 1;');
	}
	
	function iknow_collapse_el_event(el_container,el_content)
	{
		if(eval('g_'+el_container+'_timer_increment') == 0)
		{
			eval('g_timer_'+el_container+'_toggle.stop();');
			document.getElementById(el_container).style.height='0px';
			document.getElementById(el_container).style.display = 'none';
			eval('g_timer_'+el_container+'_toggle = null;');
			return true;
		}
		eval('g_'+el_container+'_timer_increment = g_'+el_container+'_timer_increment - 1;');
		document.getElementById(el_container).style.height = eval('(g_height_'+el_container+'_max) * (a_points[g_'+el_container+'_timer_increment])')+'px';
		
	}