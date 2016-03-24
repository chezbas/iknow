function iknow_set_header_v_title()
{
	document.getElementById('version_lib').innerHTML = document.getElementById('lst_vimofy2_vers_pole_lmod').value;
}

function iknow_set_header_act_title()
{
	document.getElementById('version_lib').innerHTML = document.getElementById('lst_vimofy2_vers_pole_lmod').value;
}

function iknow_toggle_histo()
{
	iknow_toggle_el('iknow_log_container','iknow_log_internal_container');
	//document.getElementById('iknow_log_container').style.left = document.getElementById('iknow_log_btn').offsetLeft+document.getElementById('iknow_log_btn').offsetWidth-document.getElementById('iknow_log_container').offsetWidth+10+'px';
	var pos = getPosition('iknow_log_btn');
	document.getElementById('iknow_log_container').style.left = pos[0]+document.getElementById('iknow_log_btn').offsetWidth-document.getElementById('iknow_log_container').offsetWidth+6+'px';
}

/**
 * Get the position of an element in reference to the body.
 * 
 * @param id Id of the element
 * @returns {Array} 0 => left, 1 => top
 */
function getPosition(id)
{
	var left = 0;
	var top = 0;
	
	// Get element
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