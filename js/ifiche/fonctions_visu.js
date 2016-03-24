/**
 * Lorsque la touche entrée est pressé pour le changement d'etape en visu.
 */
function goto_step_input(evt,nbr_etapes)
{
	var key = (window.Event) ? evt.which : evt.keyCode;

	if (key == 13)
	{
		if(Isentier(document.getElementById('etape_goto').value))
		{
			if(document.getElementById('etape_goto').value > nbr_etapes)
			{
				document.getElementById('etape_goto').value = nbr_etapes;
				step_tabbar_sep.setTabActive('tab-level1_1_'+nbr_etapes);
			}
			else
			{
				step_tabbar_sep.setTabActive('tab-level2_2_'+document.getElementById('etape_goto').value);
			}
		}
		else
		{
			document.getElementById('etape_goto').value = nbr_etapes;
			tabbar_step.setTabActive('tab-level2_2');
			step_tabbar_sep.setTabActive('tab-level2_2_'+nbr_etapes);
		}
	}
}

/**
 * Changement de tab lors de l'appuis sur la fleche de gauche ou de droite
 * 
 */
function fleches(evt)
{
	var key = (window.Event) ? evt.which : evt.keyCode;
	if(key == 37 || key == 39){
		//if(tabbar_step.getActiveTab() == 'tab-level2_2' && tabbar.getActiveTab() == 'tab-level2' ){
			if(key == 37)
			{
				// Touche de gauche
				input_step_set_val(step_tabbar_sep.previous()+1);
				//input_step_dec();
			}
			else
			{
				// Touche de droite
				input_step_set_val(step_tabbar_sep.next()+1);
				//input_step_inc();
			}
		//}
	}
}

function input_step_set_val(val)
{
	document.getElementById('etape_goto').value = val;
}

function input_step_inc()
{
	var step_goto = eval('eval(document.getElementById(\'etape_goto\').value) + 1');
	if(step_goto <= qtt_step)
	{
		document.getElementById('etape_goto').value = step_goto;
	}
}

function input_step_dec()
{
	var step_goto = eval('eval(document.getElementById(\'etape_goto\').value) - 1');
	if(step_goto > 0)
	{
		document.getElementById('etape_goto').value = step_goto;
	}
}

function maj_input_etapes(id)
{
	regexp = new RegExp("tab-level[0-9]_[0-9]_([0-9]*)");
	result = regexp.exec(id);
	
	document.getElementById('etape_goto').value = result[1];
	
}