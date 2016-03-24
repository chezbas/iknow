/**
 * Initialisation de la tinymce de modification du code
 */
function initmce_modif() {
	
	tinyMCE.init({	
		mode : "textareas",
		theme : "advanced",
		readonly : false
		plugins : "insertdatetime,safari,spellchecker,pagebreak,style,layer,save,advhr,iespell,inlinepopups,preview,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		language : 'fr',
				

		// Theme options
		theme_advanced_buttons1 : "code,preview,cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,removeformat,|,charmap,|,fullscreen,|,outdent,indent,",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	});
	
	
}
