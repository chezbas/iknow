//==================================================================
//Test function // TODO
//==================================================================
function count_me()
{
	try
	{
		document.getElementById('automatic').innerHTML = MainTimer.c_loop/100;
	}
	catch (e)
	{
		// Not yet here...
	}
	
	if(MainTimer.c_loop >= 500)
	{
		return false; // Stop this
	}
	return true;
}
//==================================================================

//==================================================================
//Reduce tools bar
//==================================================================
function reduce_tool_bar()
{
	if(MainTimer.c_loop >= 20)
	{
		
		if(document.getElementById('headdetails').offsetHeight > 1)
		{
			document.getElementById('slideh').style.display = "block";
			var hauteur = document.getElementById('headdetails').offsetHeight;
			hauteur = hauteur - 1;
			document.getElementById('headdetails').style.height = hauteur+"px";
			resize_details();
		}
		else
		{
			return false;
		}
	}
	return true;
}
//==================================================================

function active_expand_tools_bar()
{
	MainTimer.add_event(1,"expand_tool_bar()");
	document.getElementById('slideh').style.display = "none";
}

function expand_tool_bar()
{
	if(document.getElementById('headdetails').offsetHeight < 20)
	{
		var hauteur = document.getElementById('headdetails').offsetHeight;
		hauteur = hauteur + 1;
		document.getElementById('headdetails').style.height = hauteur+"px";
		resize_details();
		resize_details();
	}
	else
	{
		document.getElementById('slideh').style.display = "none";
		return false;
	}
	return true;	
}

var tree_flap = false; // Means open
var tree_position = 430;
function active_expand_navigation_tree()
{
	MainTimer.add_event(1,"expand_navigation_tree()");
}

function expand_navigation_tree()
{
	if(tree_flap)
	{
		// Open flap
		document.getElementById('iksetup').style.display = "block";
		document.getElementById('gauche').style.width = "30%";
		//document.getElementById('slidev').style.left = tree_position+"px";
		//document.getElementById('details').style.left = tree_position+"px";		
		tree_flap = false;
		resize_details();
		resize_details();
		return false;
	}
	else
	{
		// Close flap
		document.getElementById('iksetup').style.display = "none";
		document.getElementById('gauche').style.width = "0";
		//document.getElementById('details').style.left = "0px";		
		tree_flap = true;
		resize_details();
		resize_details();
		return false;
	}
	return true;	
}

function init_load(language_tiny)
{
	init_load_tiny(language_tiny);
}

function read_details(internal_id, row_id, mode, refresh)
{
	//==================================================================
	// Setup Ajax configuration
	//==================================================================
	var configuration = new Array();	
	
	configuration['page'] = 'ajax/setup/display_html.php';

	configuration['delai_tentative'] = 10000; // 10 seconds max
	configuration['max_tentative'] = 2;
	configuration['type_retour'] = false;		// ReponseText
	configuration['param'] = 'ssid='+ssid+'&language='+language+'&mode='+mode+'&internal_id='+internal_id+'&IDitem='+row_id;
	configuration['fonction_a_executer_reponse'] = 'read_done';
	configuration['param_fonction_a_executer_reponse'] = "'"+internal_id+"','"+row_id+"','"+refresh+"'";
	
	// Do the call
	ajax_call(configuration);
	//==================================================================
}

function lib_hover(p_lib)
{
	document.getElementById('txt_help').innerHTML = p_lib;
}

function lib_out()
{
	document.getElementById('txt_help').innerHTML = '';
}

function read_done(internal_id,row_id,refresh,retour)
{
	retour = JSON.parse(retour);
	document.getElementById('details_tiny').style.display = 'none';
	document.getElementById('details_input').style.display = 'none';
		
	document.getElementById('details').innerHTML = retour.HTML;
	document.getElementById('details').style.display = 'block';
	
	if(refresh == "D")
	{
		refresh_display(internal_id,row_id);
		// Try to focus on tree node
		try
		{
			document.getElementById(internal_id+row_id).focus();
		}
		catch (e)
		{
			// No focus enable.. doesn't matter.. continue
		}
	}
	try
	{
		document.getElementById('boutton_back').style.display = 'none';
		document.getElementById('boutton_edit').style.display = 'block';
	}
	catch (e)
	{
		// Display mode... continue
	}
	resize_details();
	resize_details();
}

function resize_details()
{
	// 2 times please !!
	try
	{
		heightvalue = document.getElementById('details_value').offsetHeight;
	}
	catch (e)
	{
		heightvalue = 0;
	}
	document.getElementById('main_details').style.width = document.body.offsetWidth - document.getElementById('gauche').offsetWidth + "px";
	document.getElementById('details_body').style.height = document.body.offsetHeight - document.getElementById('head_path').offsetHeight - document.getElementById('slideh').offsetHeight - document.getElementById('headdetails').offsetHeight - heightvalue - document.getElementById('footer').offsetHeight + "px";
	//
	document.getElementById('iksetup').style.width = document.getElementById('gauche').offsetWidth + "px";
	document.getElementById('slidev').style.left = document.getElementById('gauche').offsetWidth + "px";
}


window.onresize = function() {
	resize_details();
};

//==================================================================
// Jump to screen
//==================================================================
function jump_screen(page)
{
	var my_root_path = window.location.origin;
	if (my_root_path == undefined)
		{
			// Opera exception
			my_root_path = '';
		}
	var href = my_root_path+'/'+page+'?IKLNG='+language+'&ssid='+ssid;
	window.location = href;
}
//==================================================================


function checklogin(language,mode,id_row)
{
	var my_root_path = window.location.origin;
	if (my_root_path == undefined)
		{
			// Opera exception
			my_root_path = '';
		}
	
	if(mode)
	{
		var href = my_root_path+'/includes/security/setup.php?ssid='+ssid+'&IKLNG='+language+'&id='+id_row;
	}
	else
	{
		var href = my_root_path+'/setup.php'+'?IKLNG='+language+'&id='+id_row;
	}
	window.location = href;
}

function html_detail_display(internal_id)
{
	//==================================================================
	// Setup Ajax configuration
	//==================================================================
	var configuration = new Array();	
	
	configuration['page'] = 'ajax/setup/get_current_page.php';

	configuration['delai_tentative'] = 10000; // 10 seconds max
	configuration['max_tentative'] = 2;
	configuration['type_retour'] = false;		// ReponseText
	configuration['param'] = 'ssid='+ssid;
	configuration['fonction_a_executer_reponse'] = 'get_current_page_done';
	configuration['param_fonction_a_executer_reponse'] = "'"+internal_id+"'";
	
	// Do the call
	ajax_call(configuration);
	//==================================================================
}
function get_current_page_done(internal_id,retour)
{
	read_details(internal_id,retour,'U','D');
}

//==================================================================
//Initilialize tinyMCE
//==================================================================
function show_tiny(language)
{
	//==================================================================
	// Setup Ajax configuration
	//==================================================================
	var configuration = new Array();	
	
	configuration['page'] = 'ajax/setup/recover_page_for_update.php';

	configuration['delai_tentative'] = 10000; // 10 seconds max
	configuration['max_tentative'] = 2;
	configuration['type_retour'] = false;		// ReponseText
	configuration['param'] = 'ssid='+ssid+'&language='+language;
	configuration['fonction_a_executer_reponse'] = 'read_for_tiny';
	//configuration['param_fonction_a_executer_reponse'] = "'"+internal_id+"','"+row_id+"','"+refresh+"'";
	
	// Do the call
	ajax_call(configuration);
	//==================================================================
}
//==================================================================

function read_for_tiny(retour)
{
	retour = JSON.parse(retour);
	document.getElementById('details').style.display = 'none';
	tinymce.get('elm1').setContent(retour.HTML);
	document.getElementById('details_input').innerHTML = retour.INPUT;
	document.getElementById('details_input').style.display = 'block';
	document.getElementById('details_tiny').style.display = 'block';	
	document.getElementById('boutton_back').style.display = 'block';
	document.getElementById('boutton_edit').style.display = 'none';

	resize_details();
	resize_details();
}

function sauvegarder(ssid, language)
{
	var chaine = encodeURIComponent(document.getElementById("elm1").value);

	var valeur = encodeURIComponent(document.getElementById("my_value").value);
	//==================================================================
	// Setup Ajax configuration
	//==================================================================
	var configuration = new Array();	
	
	configuration['page'] = 'ajax/setup/record.php';
	//configuration['div_wait'] = 'ajax_load_etape'+id_etape;
	//configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
	configuration['delai_tentative'] = 3000; // 3 seconds max
	configuration['max_tentative'] = 2;
	configuration['type_retour'] = false;		// ReponseText

	configuration['param'] = 'ssid='+ssid+'&corps='+chaine+'&valeur='+valeur+'&language='+language;
	configuration['fonction_a_executer_reponse'] = 'sauvegarde_retour';
	//configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';

	// Usage : Always 3 parameters for HTML_TreeReturn !!!
	//				"'div_id','tree_item_prefixe','item_number_to_focus'"
	// if no focus	"'div_id',null,null"
	//configuration['param_fonction_a_executer_reponse'] = "";
	
	// Do the call
	ajax_call(configuration);
	//==================================================================
}

function sauvegarde_retour(retour)
{
	alert(retour);
}

//==================================================================
// Initilialize tinyMCE
//==================================================================
function init_load_tiny(tiny_lang)
{
	tinyMCE.init({
		// General options
		mode : "textareas",
		language : tiny_lang,
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "save,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location : "top",
		//theme_advanced_styles : "Code=codeStyle;Quote=quoteStyle",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/home/tiny_details.css",
		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "34834854"
		}
	});
}
//==================================================================