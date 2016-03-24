<?php
/**==================================================================
 * Main configuration and definition for MagicTree
 ====================================================================*/

	define("__MAGICTREE_APPLICATION_NAME","MagicTree");							// Name of application				
	define("__MAGICTREE_APPLICATION_RELEASE__","MT0.10");						// MagicTree package name in use				

	define("__PREFIX_URL_COOKIES__","");										// SSID root prefixe

	//==================================================================
	// General database connexion
	//==================================================================
	define("__MAGICTREE_DATABASE_HOST__","localhost");							// Host			
	define("__MAGICTREE_DATABASE_SCHEMA__","iknow");							// Schema
	define("__MAGICTREE_DATABASE_USER__","adm_iknow");							// user
	define("__MAGICTREE_DATABASE_PASSWORD__","MC&hny11");						// password
	//==================================================================
	
	//==================================================================
	// Define common tables for MagicTreee ( configuration, available languages, textes )
	//==================================================================
	define("__MAGICTREE_TABLE_SETUP__","mt_conf");								// General configuration of magic tree
	define("__MAGICTREE_TABLE_LANGUAGE__","mt_lang");							// Liste of language available
	define("__MAGICTREE_TABLE_TEXT__","mt_text");								// Text for internal use
	//==================================================================
	
	//==================================================================
	// Define tables for tickets list ( Optional )
	//==================================================================
	// Text for documentation and tickets screen
	define("__MAGICTREE_TABLE_EXTRA_TEXT__","mt_extra_screen_text");			// Text for documentation and tickets screen			
	define("__MAGICTREE_TABLE_EXTRA_TICK__","bugsreports");						// Contains list of tickets / changelog			
	define("__MAGICTREE_TABLE_EXTRA_TICK_TEXT__","bugstexts");					// Contains texts of bug / ticket screen			
	define("__MAGICTREE_TABLE_EXTRA_TICK_CLAS__","bugsclass");					// Contains status of tickets ( Open, analyse, valid and so on.. )			
	
	// User documentation tree table ( Optional )
//	define("__MT_TABLE_USER_DOCU__","lisha_mt_doc_user");						// User tree documentation			
//	define("__MT_TABLE_USER_DOCU_CAPTION__","lisha_mt_doc_user_caption");		// User tree documentation caption			

	// Technical documentation tree table ( Optional )
//	define("__MT_TABLE_TECH_DOCU__","lisha_mt_doc_tech");						// Technical tree documentation			
//	define("__MT_TABLE_TECH_DOCU_CAPTION__","lisha_mt_doc_tech_caption");		// Technical tree documentation caption			
	//==================================================================

	$path_root_magictree =  $path_root_framework.__MAGICTREE_APPLICATION_RELEASE__;	// Internal use for tickets screen. Don't remove please