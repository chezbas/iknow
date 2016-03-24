/**
 * Start the control of child
 */
function browse_child_start()
{
	browse_child_ok = true;
	type_object = get_checked();
	id_iobject = document.getElementById('input_ctrl').value;
	document.getElementById('header').style.height = '130px';
	document.getElementById('progress-bar-container').style.display = 'block';
	document.getElementById('vimofy').style.display = 'none';
	document.getElementById('button_search').style.display = 'none';
	document.getElementById('button_stop').style.display = '';
	document.getElementById('rd_icode').disabled = true;
	document.getElementById('rd_ifiche').disabled = true;
	document.getElementById('input_ctrl').disabled = true;
	document.getElementById('tot').style.display = 'block';
	document.getElementById('ctrl_status').innerHTML = decodeURIComponent(libelle[531]);
	browse_child();
}

function browse_child_start_auto(p_type_object,p_id_iobject)
{
	id_iobject = p_id_iobject;
	type_object = p_type_object;
	browse_child_ok = true;
	document.getElementById('header').style.height = '155px';
	document.getElementById('progress-bar-container').style.display = 'block';
	document.getElementById('vimofy').style.display = 'none';
	document.getElementById('button_search').style.display = 'none';
	document.getElementById('button_stop').style.display = '';
	document.getElementById('input_ctrl').disabled = true;
	document.getElementById('tot').style.display = 'block';
	document.getElementById('ctrl_status').innerHTML = decodeURIComponent(libelle[531]);
	browse_child();
}


/**
 * Stop (cancel) the control of child
 */
function browse_child_stop()
{
	browse_child_ok = false;
	document.getElementById('header').style.height = '85px';
	document.getElementById('progress-bar-container').style.display = 'none';
	document.getElementById('tot').style.display = 'none';
	document.getElementById('button_search').style.display = '';
	document.getElementById('button_stop').style.display = 'none';
	document.getElementById('rd_icode').disabled = false;
	document.getElementById('rd_ifiche').disabled = false;
	document.getElementById('input_ctrl').disabled = false;
	document.getElementById('ctrl_status').innerHTML = decodeURIComponent(libelle[533]);
	
	var configuration = new Array();	
	
	configuration['page'] = 'ajax_cancel.php';
	configuration['delai_tentative'] = 180000;	// 180 secondes
	configuration['max_tentative'] = 3;
	configuration['type_retour'] = false;		// ReponseText
	configuration['param'] = "ssid="+ssid;
	
	ajax_call(configuration);
}


/**
 * End of the control
 */
function browse_child_end(qtt_err)
{
	document.getElementById('ctrl_status').innerHTML = decodeURIComponent(libelle[532]);
	document.getElementById('button_search').style.display = '';
	document.getElementById('button_stop').style.display = 'none';
	document.getElementById('rd_icode').disabled = false;
	document.getElementById('rd_ifiche').disabled = false;
	document.getElementById('input_ctrl').disabled = false;
}

function change_page(qtt_err)
{
	if(redirect != null && qtt_err > 0)
	{
		window.location.replace(redirect);
	}
}

var browse_child_ok = true;
var type_object;
var id_iobject;
var redirect = null;
function browse_child(ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'ajax_ctrl.php';
		configuration['delai_tentative'] = 180000;	// 180 secondes
		configuration['max_tentative'] = 3;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "ssid="+ssid+'&id='+id_iobject+'&object='+type_object;
		configuration['fonction_a_executer_reponse'] = 'browse_child';
		
		ajax_call(configuration);
		/**==================================================================*/
	}
	else
	{
		// Get JSON
		eval(ajax_return);
		
		if(ajax_json.internal_error)
		{
			browse_child_stop();
			alert(decodeURIComponent(libelle[546]));
		}
		else
		{
			if(ajax_json.total == 0)
			{
				// Set the width of the progress bar
				document.getElementById('progress-bar').style.width = '300px';
				
				// Percentage
				document.getElementById('progress-bar').innerHTML = '100 %';
				document.getElementById('tot').innerHTML = decodeURIComponent(libelle[544]); 
				
				browse_child_end();
				change_page(0);
			}
			else
			{
				// Set the width of the progress bar
				document.getElementById('progress-bar').style.width = Math.round((eval((300 / ajax_json.total ) * ajax_json.cursor)))+'px';
				
				// Percentage
				document.getElementById('progress-bar').innerHTML = Math.round((ajax_json.cursor / ajax_json.total) * 100);
				document.getElementById('progress-bar').innerHTML += ' %';
				document.getElementById('tot').innerHTML = ajax_json.cursor+'/'+ajax_json.total; 
				if(ajax_json.cursor < ajax_json.total)
				{
					if(browse_child_ok)
					{
						browse_child();
					}
				}
				else
				{
					if(ajax_json.qtt_error > 0)
					{
						document.getElementById('vimofy').style.display = 'block';
						vimofy_refresh('vimofy');
						browse_child_end();
						
						if(ajax_json.qtt_error == 1)
						{
							document.getElementById('tot').innerHTML = decodeURIComponent(libelle[534])+ajax_json.qtt_error+' '+decodeURIComponent(libelle[535]); 
						}
						else
						{
							document.getElementById('tot').innerHTML = decodeURIComponent(libelle[534])+ajax_json.qtt_error+' '+decodeURIComponent(libelle[536]); 
						}
					}
					else
					{
						change_page(ajax_json.qtt_error);
						browse_child_end();
						document.getElementById('tot').innerHTML = decodeURIComponent(libelle[545]); 
					}
				}
			}
		}
	}
}

function input_focus()
{
	if(!Isentier(document.getElementById('input_ctrl').value))
	{
		document.getElementById('input_ctrl').value = '';
		document.getElementById('input_ctrl').className = 'gradient';
	}
}

function get_checked()
{
	if(document.getElementsByName('iobject')[1].checked)
	{
		return '__IFICHE__';
	}
	else
	{
		return '__ICODE__';
	}
}

function Isentier(sText)
{
	var ValidChars = "0123456789";
	var IsNumber=true;
	var Char;

	for (i = 0; i < sText.length && IsNumber == true; i++) 
	{ 
		Char = sText.charAt(i);
		if(ValidChars.indexOf(Char) == -1)
		{
			return false;
		}
	}
	return IsNumber;
}