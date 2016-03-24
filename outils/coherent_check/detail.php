<?php
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	
	
	require('../../class/common/class_bdd.php');
	require('class_coherent_check.php');
	
	/**==================================================================
	 * Active php session
	 ====================================================================*/	
	$ssid = $_GET['ssid'];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	

	$_SESSION[$ssid]['perf_time'] = 0;
	
	/**==================================================================
	 * Load global functions
	 ====================================================================*/	
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	

	/**==================================================================
	* Page database connexion and load conf parameters in session
	====================================================================*/	
	require('../../includes/common/load_conf_session.php');
	/*===================================================================*/

	/**==================================================================
	* Define language
	====================================================================*/	
	require('../../includes/common/language.php');
	/*===================================================================*/	

	if(!isset($_SESSION['coherence_check']))header('Location: auto.php?id='.$_GET['id'].'&iobject='.$_GET['iobject']);
	$dir_obj = '../../vimofy/';

	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('../../includes/common/html_doctype.php');
	/*===================================================================*/	
?> 
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<link rel="stylesheet" type="text/css" href="../../css/common/outils.css"/>
		<link rel="stylesheet" type="text/css" href="style.css"/>
		<link rel="stylesheet" href="../../css/common/icones_iknow.css" type="text/css">
		<link rel="stylesheet" href="../../css/common/style.css" type="text/css">
		
		<script type="text/javascript" src="fonction.js"></script>
		<script type="text/javascript" src="../../ajax/common/ajax_generique.js"></script>
		<script type="text/javascript">
		<?php
			$type_soft = 8;
			require('../../includes/common/textes.php');
		?>
		</script>
		<title><?php echo $_SESSION[$ssid]['message'][548]; ?></title>
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
		<script type="text/javascript">
			var ssid= '<?php echo $ssid; ?>';
			function over(p_txt)
			{
				document.getElementById('help').innerHTML = p_txt;
			}
	
			function unset_text_help()
			{
				document.getElementById('help').innerHTML = '';
			}
		</script>
	</head>
<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup();">
		<div id="header" style="height:85px;">
			<div style="position:absolute;top:2px;left:5px;">
				<div class="boutton_url_home boutton_outils" onclick="window.location.replace('../../?ssid='+ssid);" onmouseover="over('Portail iKnow');" onmouseout="unset_text_help();"></div>
			</div>
			<div class="logo"></div>
			<div id="search_bar">
				<form action="javascript:browse_child_start_auto(<?php echo "'".$_SESSION['coherence_check']->get_type_object()."',".$_SESSION['coherence_check']->get_id_object(); ?>);" name="form_iobject">
						<input type="radio" style="display:none;" name="iobject" id="rd_icode"/> 
						<input type="radio" style="display:none;" name="iobject" id="rd_ifiche"/> 
						<input type="text" style="display:none;" id="input_ctrl" class="gradient search" value="" onclick="input_focus();"/>
						<input type="button" id="button_search" value="<?php echo $_SESSION[$ssid]['message'][549]; ?>" onclick="browse_child_start_auto(<?php echo "'".$_SESSION['coherence_check']->get_type_object()."',".$_SESSION['coherence_check']->get_id_object(); ?>);"/>
						<input type="button" id="button_stop" value="<?php echo $_SESSION[$ssid]['message'][543]; ?>" style="display:none;" onclick="browse_child_stop();"/>
				</form>
			</div>
			<div style="position:absolute;width:100%;text-align:center;top:100px;color: #000;font-size: 12px;">
				<div class="progress-bar-container" id="progress-bar-container" style="display:none;">
					<div class="progress-bar" id="progress-bar">0%</div>
				</div>
				<div id="tot" style="position:absolute;width:100%;text-align:center;top:25px;color: #000;font-size: 12px;color:#AA0000;font-weight:bold;"></div>
			</div>
		</div>
		<div style="width:100%;bottom:23px;top:97px;position:absolute;background-color:#999;overflow:auto;" id="vimofy">
			<?php
				echo $vimofy_child->generate_vimofy();
			?>
		</div>
		<div id="footer">
			<div id="help" style="position: absolute;left: 0;font-weight: bold;font-size: 11px;"></div>
			<div style="position: absolute;right:200px;font-weight: bold;" id="dt_begin"></div>
			<div style="position: absolute;right:150px;font-weight: bold;" id="dt_end"></div>
			<div style="position: absolute;right:5px;font-weight: bold;" id="ctrl_status"></div>
		</div>
		<script type="text/javascript">
			<?php 
				//echo $_SESSION['coherence_check']->get_array_child_json();
			?>
			//heure('dt_begin');
			//browse_child();
			
		</script>
		<?php
			$vimofy_child->vimofy_generate_js_body();
			unset($_SESSION['coherence_check']);
		?>
	</body>
</html>