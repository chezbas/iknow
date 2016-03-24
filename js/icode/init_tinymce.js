/**
 * Initialisation de la tinymce de modification de la description.
 */		
function initmce_onglet_general()
{
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		editor_selector : "Descriptif",
		plugins : "insertdatetime,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		language : iknow_lng_tinyMCE, /* Recover Language from Iknow */
		setup : function(ed) {
		      ed.onKeyDown.add(function(ed, e)
		      {
		    	  check_description();
		      });
		 },
				
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,forecolor,backcolor,code,image,preview,cleanup,",
		theme_advanced_buttons2 : "tablecontrols,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,hr,removeformat,|,sub,sup,|,charmap,|,fullscreen,",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	});
}