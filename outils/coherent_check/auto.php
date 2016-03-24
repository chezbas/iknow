<?php
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require '../../includes/common/ssid_simple.php';
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
	* Define language
	====================================================================*/	
	require('../../includes/common/language.php');
	/*===================================================================*/	
	
	$dir_obj = '../../vimofy/';
	
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
			
			if(isset($_GET['iobject']))
			{
				switch ($_GET['iobject']) {
					case '__ICODE__':
						$lib_iobject = $_SESSION[$ssid]['message'][314];
						$object_file = 'icode.php';
						break;
					case '__IFICHE__':
						$lib_iobject = $_SESSION[$ssid]['message'][313];
						$object_file = 'ifiche.php';
						break;
				}
			}
		?>
		</script>
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
			var ssid='<?php echo $ssid; ?>';
			function over(p_txt)
			{
				document.getElementById('help').innerHTML = p_txt;
			}
	
			function unset_text_help()
			{
				document.getElementById('help').innerHTML = '';
			}
		</script>
		<title><?php echo $_SESSION[$ssid]['message'][548].' '.$lib_iobject.' '.$_GET['id'];?></title>
	</head>
	<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup();">
		<div id="header" style="height:85px;">
			<div style="position:absolute;top:2px;left:5px;">
				<div class="boutton_url_home boutton_outils" onclick="window.location.replace('../../?ssid='+ssid);" onmouseover="over('Portail iKnow');" onmouseout="unset_text_help();"></div>
			</div>
			<div class="logo"></div>
			<div id="search_bar">
				<form action="javascript:browse_child_start_auto(<?php echo "'".$_GET['iobject']."',".$_GET['id']; ?>);" name="form_iobject">
						<input type="radio" style="display:none;" name="iobject" id="rd_icode" <?php if($_GET['iobject'] == '__ICODE__')echo 'checked="checked"';?>> 
						<input type="radio" style="display:none;" name="iobject" id="rd_ifiche" <?php if($_GET['iobject'] == '__IFICHE__')echo 'checked="checked"';?>/> 
						<input type="text" style="display:none;" id="input_ctrl" class="gradient search" value="<?php echo $_GET['id']; ?>" onclick="input_focus();"/>
						<input type="button" id="button_search" value="<?php echo $_SESSION[$ssid]['message'][549]; ?>" onclick="browse_child_start_auto(<?php echo "'".$_GET['iobject']."',".$_GET['id']; ?>);"/>
						<input type="button" id="button_stop" value="<?php echo $_SESSION[$ssid]['message'][543]; ?>" style="display:none;" onclick="browse_child_stop();"/>
				</form>
			</div>
			<div style="position:absolute;width:100%;text-align:center;top:95px;color: #000;font-size: 12px;">
				<div class="progress-bar-container" id="progress-bar-container" style="display:none;">
					<div class="progress-bar" id="progress-bar">0%</div>
				</div>
				<div id="tot" style="position:absolute;width:100%;text-align:center;top:25px;color: #000;font-size: 12px;color:#AA0000;font-weight:bold;"></div>
				<div style="position:absolute;width:100%;text-align:center;top:45px;color: #000;font-size: 12px;"><a href="../../<?php echo $object_file.'?ID='.$_GET['id']; ?>"><?php echo $_SESSION[$ssid]['message'][547]; ?></a></div>
			</div>
		</div>
		<div style="width:100%;bottom:23px;top:162px;position:absolute;background-color:#999;overflow:auto;display:none;" id="vimofy">
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
		?>
		<script type="text/javascript">
			<?php 
			
				if(isset($_GET['iobject']))
				{
					if($_GET['iobject'] == '__ICODE__')
					{
						echo "var redirect = '../../icode.php?ID=".$_GET['id']."';";
					}
				} 
			?>
			browse_child_start_auto(<?php echo "'".$_GET['iobject']."',".$_GET['id']; ?>);
		</script>
	</body>
</html>