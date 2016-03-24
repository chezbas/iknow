function initmce_description() {
		tinyMCE.init({
			// General options
			mode : "textareas",
			theme : "advanced",
			id_etape: 0,
			onglet_general: 0,
			obj: 1,
			ssid: ssid,
			editor_selector : 'edit_etape',
			height:320,
			plugins : "iknow,insertdatetime,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
			language : iknow_lng_tinyMCE, /* Recover Language from Iknow */
			auto_focus : "edit_etape",
			fonction_save : 'sauvegarder_description();',
			// Theme options
			theme_advanced_buttons1 : "removeformat,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor,code,image,preview,cleanup?",
			theme_advanced_buttons2 : "tablecontrols,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,hr,|,sub,sup,|,charmap,|,fullscreen,",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
	
			// Example content CSS (should be your site CSS)
			content_css : "css/ifiche/tiny.css",
	
			// Drop lists for link/image/media/template dialogs
			//template_external_list_url : "js/template_list.js",
			//external_link_list_url : "js/link_list.js",
					
			media_external_list_url : "js/media_list.js",
	
			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
		});	
}

/**
 * Initialisation de la tinymce de modification d'une étape.
 */
function initmce_prerequis() {
		tinyMCE.init({
			// General options
			mode : "textareas",
			theme : "advanced",
			id_etape: 0,
			onglet_general: 1,
			obj: 1,
			ssid: ssid,
			editor_selector : 'edit_etape',
			height:320,
			plugins : "iknow,insertdatetime,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
			language : iknow_lng_tinyMCE, /* Recover Language from Iknow */
			auto_focus : "edit_etape",
			fonction_save : 'sauvegarder_prerequis();',
			// Theme options
			theme_advanced_buttons1 : "removeformat,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor,code,image,preview,cleanup?",
			theme_advanced_buttons2 : "tablecontrols,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,hr,|,sub,sup,|,charmap,|,fullscreen,",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
	
			// Example content CSS (should be your site CSS)
			content_css : "css/ifiche/tiny.css",
	
			// Drop lists for link/image/media/template dialogs
			//template_external_list_url : "js/template_list.js",
			//external_link_list_url : "js/link_list.js",
					
			media_external_list_url : "js/media_list.js",
	
			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
		});	
}

/**
 * Initialisation de la tinymce de modification d'une étape.
 */
function initmce_step(p_id_etape) {
//onglet_general: 0 -> description, 1 -> prerequis
	
	if(document.getElementById('onglet_nbr_etape').innerHTML == 1)
	{
		var s_theme_advanced_buttons1 = "removeformat,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor,code,image,preview,cleanup,|,InsertVarIn,InsertVarOut,InsertSpecField,LinkiKnow,LinkPassword,IKCalc,";
	}
	else
	{
		var s_theme_advanced_buttons1 = "removeformat,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor,code,image,preview,cleanup,|,InsertVarIn,InsertVarOut,InsertSpecField,LinkStep,LinkiKnow,LinkPassword,IKCalc,";
	}
		tinyMCE.init({
			
			// General options
			mode : "textareas",
			theme : "advanced",
			onglet_general: 1,
			obj: 1,
			ssid: ssid,
			editor_selector : 'edit_etape',
			height:320,
			plugins : "iknow,insertdatetime,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
			language : iknow_lng_tinyMCE, /* Recover Language from Iknow */
			auto_focus : "edit_etape",
			fonction_save : 'save_step('+p_id_etape+');',
			id_etape : p_id_etape,
			// Theme options
			theme_advanced_buttons1 : s_theme_advanced_buttons1,
			theme_advanced_buttons2 : "tablecontrols,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,hr,|,sub,sup,|,charmap,|,fullscreen,",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
	
			// Example content CSS (should be your site CSS)
			content_css : "css/ifiche/tiny.css",
	
			// Drop lists for link/image/media/template dialogs
			//template_external_list_url : "js/template_list.js",
			//external_link_list_url : "js/link_list.js",
					
			media_external_list_url : "js/media_list.js",
	
			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
		});	
}
