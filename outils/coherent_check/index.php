<?php
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('../../includes/common/ssid_simple.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Load global functions
	 ====================================================================*/	
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('../../includes/common/load_conf_session.php');
	/*===================================================================*/

	
	/**==================================================================
	* Recover language from URL or Database
	====================================================================*/	
	require('../../includes/common/language.php');
	/*===================================================================*/

	
	$dir_obj = '../../vimofy/'; // Vimofy path access

	require('../../class/common/class_bdd.php');
	require('class_coherent_check.php');
	
	$_SESSION[$ssid]['perf_time'] = 0;

	
	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('../../includes/common/html_doctype.php');
	/*===================================================================*/	
?> 
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<script type="text/javascript">
			var ssid = '<?php echo $ssid; ?>';
			var version_soft = '';

			var libelle_common = Array();
			<?php 
				/**==================================================================
				* Recover text
				====================================================================*/	
				$type_soft = 8;
				require('../../includes/common/textes.php');
				/*===================================================================*/
				
				// Variables pour l'uptime de la session
				$gc_lifetime = ini_get('session.gc_maxlifetime'); 
				$end_visu_date  = time() + $gc_lifetime;
				$end_visu_time = $end_visu_date;
				$end_visu_date = date('m/d/Y',$end_visu_date);
				$end_visu_time = date('H:i:s',$end_visu_time);
			?>
			var end_visu_date = '<?php echo $end_visu_date; ?>';
			var end_visu_time = '<?php echo $end_visu_time; ?>';
	
			function over(p_txt)
			{
				document.getElementById('help').innerHTML = p_txt;
			}
	
			function unset_text_help()
			{
				document.getElementById('help').innerHTML = '';
			}
		</script>
		<title><?php echo $_SESSION[$ssid]['message'][521]; ?></title>
		
		<link rel="stylesheet" type="text/css" href="../../css/common/outils.css"/>
		<link rel="stylesheet" type="text/css" href="style.css"/>
		<link rel="stylesheet" href="../../css/common/icones_iknow.css" type="text/css">
		<link rel="stylesheet" href="../../css/common/style.css" type="text/css">
		
		<script type="text/javascript" src="fonction.js"></script>
		<script type="text/javascript" src="../../ajax/common/ajax_generique.js"></script>
		
		<link rel="stylesheet" href="../../css/common/iknow/iknow_footer.css" type="text/css">
		<link rel="stylesheet" href="../../css/common/iknow/iknow_msgbox.css" type="text/css">
		<script type="text/javascript" src="../../js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="../../js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="../../js/common/session_management.js"></script>

		<?php	
			/**==================================================================
			 * Vimofy include
			 ====================================================================*/	
			require $dir_obj.'vimofy_includes.php';
			/*===================================================================*/	
			
			/**==================================================================
			 * Vimofy init
			 ====================================================================*/	
			require 'vim_child.php';
			/*===================================================================*/	
			
			/*==================================================================
			* Vimofy internal init
			====================================================================*/  
			$vimofy_child->generate_public_header();
			$vimofy_child->vimofy_generate_header();
			/*===================================================================*/    
		?>
	</head>
	<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup();">
		<div id="header" style="height:85px;">
			<div style="position:absolute;top:2px;left:5px;">
				<div class="boutton_url_home boutton_outils" onclick="window.location.replace('../../?ssid='+ssid);" onmouseover="over('<?php echo $_SESSION[$ssid]['message']['iknow'][352]; ?>');" onmouseout="unset_text_help();"></div>
			</div>
			<div class="logo"></div>
			
			<div id="search_bar">
				<form action="javascript:browse_child_start();" name="form_iobject">
						<input type="radio" name="iobject" id="rd_icode" checked="checked"><?php echo $_SESSION[$ssid]['message'][525]; ?> 
						<input type="radio" name="iobject" id="rd_ifiche"/><?php echo $_SESSION[$ssid]['message'][524]; ?>
						<input type="text" id="input_ctrl" class="gradient search" value="<?php echo $_SESSION[$ssid]['message'][523]; ?>" onclick="input_focus();"/>
						<input type="button" id="button_search" value="<?php echo $_SESSION[$ssid]['message'][522]; ?>" onclick="browse_child_start();"/>
						<input type="button" id="button_stop" value="<?php echo $_SESSION[$ssid]['message'][543]; ?>" style="display:none;" onclick="browse_child_stop();"/>
				</form>
			</div>
			<div style="position:absolute;width:100%;text-align:center;top:95px;color: #000;font-size: 12px;">
				<div class="progress-bar-container" id="progress-bar-container" style="display:none;">
					<div class="progress-bar" id="progress-bar">0%</div>
				</div>
				<div id="tot" style="position:absolute;width:100%;text-align:center;top:25px;color: #000;font-size: 12px;"></div>
			</div>
		</div>	
		<div style="width:100%;bottom:23px;top:142px;position:absolute;background-color:#999;overflow:auto;display:none;" id="vimofy">
			<?php 
				echo $vimofy_child->generate_vimofy(); 
			?>
		</div>
		<div id="footer">
		</div>
		<script type="text/javascript">
			var footer = new iknow_footer('../../js/common/iknow/');

			footer.add_element('<div id="help" style="position: absolute;left: 0;font-weight: bold;font-size: 11px;"></div>',__FOOTER_LEFT__);	
			
			footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
			footer.add_element('<div style="font-weight: bold;" id="ctrl_status"></div>',__FOOTER_RIGHT__);
				
			footer.generate();
		</script>
		<?php
			$vimofy_child->vimofy_generate_js_body();
		?>
	</body>
</html>