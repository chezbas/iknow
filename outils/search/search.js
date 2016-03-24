function search_iObject(ajax_return)
{
	if(typeof(ajax_return) == "undefined")
	{
		document.getElementById('content').style.display = 'none';
		/**==================================================================
		 * Récupération de la vimofy
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'search/vimofy_icode_search.php';
		configuration['div_wait'] = 'wait';
		configuration['delai_tentative'] = 90000;		// 90 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ReponseText
		configuration['param'] = "ssid="+ssid+"&id="+document.getElementById('input_search').value+'&iObject='+get_checked();
		configuration['fonction_a_executer_reponse'] = 'search_iObject';

		ajax_call(configuration);
		/**==================================================================*/	
	}
	else
	{
		var reponse_json = get_json(ajax_return); 
		document.getElementById('content').style.display = '';
		document.getElementById('content').innerHTML = decodeURIComponent(reponse_json.parent.vimofy);
		addCss(decodeURIComponent(reponse_json.parent.css));
		eval(decodeURIComponent(reponse_json.parent.json));
	}
}

function input_focus()
{
	if(!Isentier(document.getElementById('input_search').value))
	{
		document.getElementById('input_search').value = '';
		document.getElementById('input_search').className = 'gradient';
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
		if (ValidChars.indexOf(Char) == -1)
		{
			return false;
		}
	}
	return IsNumber;
}