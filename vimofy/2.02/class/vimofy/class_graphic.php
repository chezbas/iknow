<?php
	class graphic_vimofy
	{
		/**==================================================================
		 * Attributes
		 ====================================================================*/		
		private $c_id;					// Vimofy Id
		private $c_ssid;				// Session Id
		private $c_lng;								// Language of the vimofy
		private $c_dir_obj;				// Directory of the vimofy object
		private $c_img_obj;				// Directory of the img
		
		public $c_columns;				// Columns (array())
		private $c_readonly;			// Vimofy mode -> true : readonly, false read/write
		private $c_theme;				// Vimofy css style
		private $c_height;				// Vimofy height
		private $c_h_unity;				// Vimofy unity height (px,%)
		private $c_width;				// Vimofy width
		private $c_w_unity;				// Vimofy unity width (px,%)
		private $c_nb_line;				// Number of line per page
		private $c_default_page;		// default page number
		
		private $c_title;				// Vimofy title
		private $c_color_mask;			// Color mask (array())
		private $c_software_version;	// Version of the vimofy
		private $c_dir_img;				// Directory for images
		private $c_mode;				// LMOD or NMOD
		private $c_return_mode;			// Mode of return (in LMOD mode)
		
		// Page 
		private $c_active_page;			// Current page 
		private $c_limit_min;			// Min value of the limit
		private $c_limit_max;			// Max value of the limit
		
		private $c_recordset_line;		// Total line of the recordset
		private $c_obj_bdd;
		private $c_background_logo;		// Background logo of the vimofy
		private $c_background_repeat;	// Background repeat of the logo
		private $c_id_parent;
		private $c_id_parent_column;
		private $c_col_return;
		private	$c_cols_sep_display;
		private $c_rows_sep_display;
		private $c_page_selection_display;
		private $c_param_adv_filter;
		private $c_type_internal_vimofy;
		private $c_navbar_txt_activate;
		private $c_navbar_refresh_button_activate;
		private $c_navbar_nav_button_activate;
		private $c_navbar_txt_line_per_page_activate;
		private $c_default_input_focus;
		private $c_title_display;
		private $c_lmod_specified_width;
		private $c_toolbar_delete_btn;
		private $c_toolbar_add_btn;
		/*===================================================================*/	
		
		/**==================================================================
		 * Methods
		 ====================================================================*/	
		/**
		 * Builder of Vimofy graphic class
		 * @param $p_style
		 * @param $p_width
		 * @param $p_columns
		 * @param $p_color_mask
		 */
		public function __construct(&$p_software_version,&$p_id,&$p_ssid,$p_obj_bdd,$p_dir_obj,$p_img_obj,&$p_columns,&$p_selected_line,&$p_type_internal_vimofy,$p_lng)
		{
			$this->c_dir_obj = &$p_dir_obj;
			$this->c_img_obj = &$p_img_obj;
			$this->c_software_version = &$p_software_version;
			$this->c_id = &$p_id;
			$this->c_ssid = &$p_ssid;
			$this->c_obj_bdd = &$p_obj_bdd;
			$this->c_cols_sep_display = true;
			$this->c_rows_sep_display = true;
			$this->c_columns = &$p_columns;
			$this->c_selected_lines = &$p_selected_line;
			$this->c_type_internal_vimofy = &$p_type_internal_vimofy;
			$this->c_lng = $p_lng;
			$this->c_navbar_txt_activate = true;
			$this->c_navbar_refresh_button_activate = true;
			$this->c_navbar_nav_button_activate = true;
			$this->c_navbar_txt_line_per_page_activate = true;
			$this->c_lmod_specified_width = null;
			$this->c_toolbar_delete_btn = true;
			$this->c_toolbar_add_btn = true;
		}
		
		
		/**
		 * Generate the visual content of the vimofy
		 * 
		 * @param dataset The dataset of the query
		 */
		public function draw_vimofy($p_resultat,$p_result_header = false,$p_ajax_call = null,$p_edit = false)
		{
			$vimofy = '';
			
			if($p_ajax_call == null)
			{
				/**==================================================================
				 * Create the container of the vimofy
				 ====================================================================*/
				$vimofy .=  '<div id="vim__'.$this->c_theme.'__vimofy_table_'.$this->c_id.'__" onclick="vimofy_click(event,\''.$this->c_id.'\')">';
				/*===================================================================*/
				
				/**==================================================================
				 * Wait and msgbox div
				 ====================================================================*/
				// Begin container
				$vimofy .= '<div id="vim__'.$this->c_theme.'__hide_container_'.$this->c_id.'__" onclick="vimofy_hide_container_click(\''.$this->c_id.'\');" class="__'.$this->c_theme.'_hide_container">';
				
				// Wait
				$vimofy .= '<div id="vim__'.$this->c_theme.'__wait_'.$this->c_id.'__" class="__'.$this->c_theme.'_wait" style="display:none;"></div>';
				
				// Msgbox
				$vimofy .= '<div class="msgbox_conteneur" id="vim_msgbox_conteneur_'.$this->c_id.'" style="display:none;"></div>';
				
				// End container
				$vimofy .= '</div>';
				/*===================================================================*/
				
				/**==================================================================
				 * Create the div for column move function
				 ====================================================================*/
				// Arrows
				if($this->c_mode != __CMOD__)
				{
					$vimofy .= '<div class="__'.$this->c_theme.'__arrow_move_column_top" id="arrow_move_column_top_'.$this->c_id.'"></div>';
					$vimofy .= '<div class="__'.$this->c_theme.'__arrow_move_column_bottom" id="arrow_move_column_bottom_'.$this->c_id.'"></div>';
				}
				
				// Wait input 
				$vimofy .= '<div class="__'.$this->c_theme.'__wait_input" id="wait_input_'.$this->c_id.'"></div>';
				
				// Internal vimofy 
				//$vimofy .= '<div class="__'.$this->c_theme.'__internal_vimofy" id="internal_vimofy_'.$this->c_id.'"></div>';
				
				// Float div
				$vimofy .= '<div class="__'.$this->c_theme.'__float_move_column" id="vimofy_column_move_div_float_'.$this->c_id.'">';
				$vimofy .= '<table class="shadow">';
				$vimofy .= '<tr><td class="shadow_l"></td><td class="shadow">';
				$vimofy .= '<table id="table_header_menu_'.$this->c_id.'" class="__'.$this->c_theme.'_column_header_menu">';
				$vimofy .= '<tr class="__'.$this->c_theme.'_column_header_menu __'.$this->c_theme.'__float_move_column_content">';
				$vimofy .= '<td><div id="vimofy_column_move_div_float_forbidden_'.$this->c_id.'" class="__'.$this->c_theme.'__float_column"></div></td><td><div class="__'.$this->c_theme.'__float_move_column_content" id="vimofy_column_move_div_float_content_'.$this->c_id.'">..........</div></td>';
	    		$vimofy .= '</tr>';
		    	$vimofy .= '</table>';
				$vimofy .= '</td></tr>';
				$vimofy .= '<tr><td class="shadow_l_b"></td><td class="shadow_b"></td></tr></table>';
				$vimofy .= '</div>';
				/*===================================================================*/
				
				/**==================================================================
				 * Title
				 ====================================================================*/	
				if($this->c_title_display == true)
				{
					$vimofy .= '<div class="__'.$this->c_theme.'_vimofy_title" id="vimofy_title_'.$this->c_id.'">';
					$vimofy .= $this->generate_title();
					$vimofy .= '</div>';
				}
				/*===================================================================*/
				
				/**==================================================================
				 * Toolbar
				 ====================================================================*/	
				$vimofy .= '<div class="__'.$this->c_theme.'_vimofy_toolbar" id="vimofy_toolbar_'.$this->c_id.'">';
				$vimofy .= $this->generate_toolbar($p_edit);
				$vimofy .= '</div>';
				/*===================================================================*/	
				
				$vimofy .= '<div id="vimofy_ajax_return_'.$this->c_id.'">';
			}

			/**==================================================================
			 * Header page selection
			 ====================================================================*/	
			if($this->c_page_selection_display['header'] == true)
			{
				$vimofy .= '<div class="__'.$this->c_theme.'_vimofy_header_page_selection" id="vimofy_header_page_selection_'.$this->c_id.'">';
				$vimofy .= $this->generate_page_selection($p_resultat,__HEADER__);
				$vimofy .= '</div>';
			}
			/*===================================================================*/	
			
			/**==================================================================
			 * Column header
			 ====================================================================*/	
			$vimofy .= '<div class="__'.$this->c_theme.'__vimofy_header__" id="header_'.$this->c_id.'">';
			$vimofy .= '<table style="border-collapse:collapse;height:46px;" cellpadding="0" cellspacing="0" id="table_header_'.$this->c_id.'">';
			$vimofy .= '<tbody>';
			$vimofy .= $this->generate_columns_header($p_edit,$p_resultat,$p_result_header);
			$vimofy .= '</tbody>';
			$vimofy .= '</table>';
			$vimofy .= '</div>';
			/*===================================================================*/	
			
			/**==================================================================
			 * Create the column header menu
			 ====================================================================*/	
			$vimofy .= '<div class="column_menu" id="conteneur_menu_'.$this->c_id.'">';
			$vimofy .= '<div id="vim_column_header_menu_'.$this->c_id.'" class="__'.$this->c_theme.'_column_header_menu">...</div>';
			$vimofy .= '</div>';
			$vimofy .= '<div class="__'.$this->c_theme.'__internal_vimofy" id="internal_vimofy_'.$this->c_id.'"></div>';
			$vimofy .= '<div class="__'.$this->c_theme.'_calendar" id="vim_column_calendar_'.$this->c_id.'" >AAAA</div>';
			
			/*===================================================================*/
			
			/**==================================================================
			 * Data
			 ====================================================================*/	
			$vimofy .= '<div id="vimofy_table_mask_'.$this->c_id.'" class="__'.$this->c_theme.'_table_mask_'.$this->c_id.'"></div><div class="__'.$this->c_theme.'_vimofy_content_'.$this->c_id.'" id="liste_'.$this->c_id.'">';
			$vimofy .= '<table style="border-collapse:collapse;" cellpadding="0" cellspacing="0" id="table_liste_'.$this->c_id.'">';
			$vimofy .= $this->generate_data_content($p_resultat,$p_edit);
			$vimofy .= '</table>';
			$vimofy .= '</div>';
			/*===================================================================*/	
			
			/**==================================================================
			 * Footer page selection
			 ====================================================================*/	
			if($this->c_page_selection_display['footer'] == true)
			{
				$vimofy .= '<div class="__'.$this->c_theme.'_vimofy_footer_page_selection" id="vimofy_footer_page_selection_'.$this->c_id.'">';
				$vimofy .= $this->generate_page_selection($p_resultat,__FOOTER__);
				$vimofy .= '</div>';
			}
			/*===================================================================*/	
			
			if($p_ajax_call == null)
			{
				$vimofy .= '</div>';
				/**==================================================================
				 * End of the container of the vimofy
				 ====================================================================*/
				$vimofy .= '</div>';
				/*===================================================================*/
			}
			
			// Return the Vimofy
			return $vimofy;
		}
		

		public function generate_style($p_bal_style = true)
		{
			$style = '';
			
			if($p_bal_style)
			{
				$style .= '<style type="text/css">';
			}
			
			$style .= $this->generate_vimofy_global_style();
			$style .= $this->generate_color_line_style();
			
			if($p_bal_style)
			{
				$style .= '</style>';
			}
			
			return $style;
		}
		
		
		private function generate_color_line_style()
		{
			$style = '/* ---------- Begin color line style ---------- */';
			
			foreach($this->c_color_mask as $key => $value)
			{
				$style .= '.lc_'.$key.'_'.$this->c_id;
				$style .= '{';
				$style .= 'background-color:#'.$value['color_code'].';';
				$style .= 'color:#'.$value['color_text'].';';
				$style .= 'font-size:0.7em;';
				$style .= 'font-family: tahoma, arial, helvetica, sans-serif;';
				$style .= '}';
				
				// Selected
				$style .= '.line_selected_color_'.$key.'_'.$this->c_id;
				$style .= '{';
				$style .= 'background-color:#'.$value['color_selected_code'].';';
				$style .= 'color:#'.$value['color_text_selected'].';';
				$style .= 'font-size:11px;';
				$style .= 'font-family: tahoma, arial, helvetica, sans-serif;';
				$style .= 'cursor:pointer;';
				$style .= '}';
				
				// Hover
				$style .= '.lc_'.$key.'_'.$this->c_id.':hover';
				$style .= '{';
				$style .= 'background-color:#'.$value['color_hover_code'].';';
				$style .= 'cursor:pointer;';
				$style .= '}';
				
				// -----------------------------------------------------------------
				
				$style .= '.lc_'.$key.'_'.$this->c_id.'_child';
				$style .= '{';
				$style .= 'background-color:#'.$value['color_code'].';';
				$style .= 'color:#'.$value['color_text'].';';
				$style .= 'font-size:11px;';
				$style .= 'font-family: tahoma, arial, helvetica, sans-serif;';
				$style .= '}';
				
				// Selected
				$style .= '.line_selected_color_'.$key.'_'.$this->c_id.'_child';
				$style .= '{';
				$style .= 'background-color:#'.$value['color_selected_code'].';';
				$style .= 'color:#'.$value['color_text_selected'].';';
				$style .= 'font-size:11px;';
				$style .= 'font-family: tahoma, arial, helvetica, sans-serif;';
				$style .= 'cursor:pointer;';
				$style .= '}';
				
				// Hover
				$style .= '.lc_'.$key.'_'.$this->c_id.'_child'.':hover';
				$style .= '{';
				$style .= 'background-color:#'.$value['color_hover_code'].';';
				$style .= 'cursor:pointer;';
				$style .= '}';
			}
			
			$style .= '/* ---------- End color line style ---------- */';
			return $style;
		}
		
		private function generate_vimofy_global_style()
		{
			$style = '#vim__'.$this->c_theme.'__vimofy_table_'.$this->c_id.'__';
			$style .= '{';
			$style .= 'width:'.$this->c_width.$this->c_w_unity.';';
			$style .= 'height:'.$this->c_height.$this->c_h_unity.';';
			$style .= 'border:0 solid #777;';
			$style .= 'overflow:hidden;';
			$style .= 'font-size:15px;';
			
			if($this->c_h_unity == '%')
			{
				$style .= 'position:absolute;';
			}
			else
			{
				$style .= 'position:relative;';
			}
			
			$style .= '}';																																		
									
			$style .= '#vim__'.$this->c_theme.'__vimofy_table_'.$this->c_id.'_child__';
			$style .= '{';
			$style .= 'width:100%;';
			$style .= 'height:300px;';
			$style .= 'border:0 solid #777;';
			$style .= 'overflow:hidden;';
			$style .= 'position:relative;';
			$style .= '}';																																		
									
			/**==================================================================
			 * Title of the columns
			 ====================================================================*/	
			$style .= '.__'.$this->c_theme.'_vimofy_content_'.$this->c_id;
			$style .= '{';
			$style .= 'width: 100%;';
			$style .= 'overflow-x: auto;';
			$style .= 'overflow-y: auto;';
									
			if($this->c_background_logo == '')
			{
				$style .= 'background-color: #E8E8E8;';
			}
			else
			{
				$style .= 'background: #E8E8E8 url('.$this->c_background_logo.') '.$this->c_background_repeat.' center center;';
			}
			
			if($this->c_h_unity == '%')
			{
				$style .= 'position: absolute;';
				$top_height = 71;
				$bottom_size = 0;
				if($this->c_title_display) $top_height += 22;
				if($this->c_page_selection_display['header']) $top_height += 25;
				$style .= 'top:'.$top_height.'px;';
				if($this->c_page_selection_display['footer']) $bottom_size += 25;
				$style .= 'bottom:'.$bottom_size.'px;';
			}
			else
			{
				$style .= 'position: relative;';
				$height_size = $this->c_height - 142;
				if(!$this->c_page_selection_display['header']) $height_size += 25;
				if(!$this->c_page_selection_display['footer']) $height_size += 25;
				if(!$this->c_title_display) $height_size += 22;
				$style .= 'height: '.$height_size.$this->c_h_unity.';';
			}
			
			$style .= '}';	
			
			$style .= '.__'.$this->c_theme.'_table_mask_'.$this->c_id;
			$style .= '{';
			$style .= 'width: 100%;';
			$style .= 'overflow-x: auto;';
			$style .= 'overflow-y: auto;';
			$style .= 'z-index: 1;';
			$style .= 'display: none;';
			$style .= 'background: url('.$this->c_dir_obj.$this->c_dir_img.'/../transparent/transp_light.png) repeat;';
			
									
			if($this->c_h_unity == '%')
			{
				$style .= 'position: absolute;';
				$top_height = 71;
				$bottom_size = 0;
				if($this->c_title_display) $top_height += 22;
				if($this->c_page_selection_display['header']) $top_height += 25;
				$style .= 'top:'.$top_height.'px;';
				if($this->c_page_selection_display['footer']) $bottom_size += 25;
				$style .= 'bottom:'.$bottom_size.'px;';
			}
			else
			{
				$style .= 'position: relative;';
				$height_size = $this->c_height - 142;
				if(!$this->c_page_selection_display['header']) $height_size += 25;
				if(!$this->c_page_selection_display['footer']) $height_size += 25;
				if(!$this->c_title_display) $height_size += 22;
				$style .= 'height: '.$height_size.$this->c_h_unity.';';
			}
			
			$style .= '}';	
			
			$style .= '.__'.$this->c_theme.'_vimofy_content_'.$this->c_id.'_child';
			$style .= '{';
			$style .= 'width: 100%;';
			$style .= 'overflow-x: auto;';
			$style .= 'overflow-y: auto;';
			
			if($this->c_background_logo == '')
			{
				$style .= 'background-color: #E8E8E8;';
			}
			else
			{
				$style .= 'background: #E8E8E8 url('.$this->c_background_logo.') '.$this->c_background_repeat.' center center;';
			}
			
			if($this->c_h_unity == '%')
			{
				$top_height_size = 118;
				if(!$this->c_page_selection_display['header']) $top_height_size -= 25;
				$style .= 'position: absolute;';
				$style .= 'top:'.$top_height_size.'px;';
				$style .= 'bottom:25px;';
			}
			else
			{
				$style .= 'position: relative;';
				$style .= 'height: '.($this->c_height - 143).$this->c_h_unity.';';
			}
			
			$style .= '}';	
			/*===================================================================*/
			
			/**==================================================================
			 * Footer
			 ====================================================================*/	
			if($this->c_h_unity == '%')
			{
				$style .= '#vimofy_footer_page_selection_'.$this->c_id;
				$style .= '{';
				$style .= 'position: absolute;';
				$style .= 'bottom: 0;';
				$style .= '}';
			}
			
			$style .= '#vimofy_footer_page_selection_'.$this->c_id.'_child';
			$style .= '{';
			$style .= 'position: absolute;';
			$style .= 'bottom: 0;';
			$style .= '}';
			/*===================================================================*/
			
			return $style;
		}
		
			
		/**
		 * Generate page selection
		 */
		private function generate_page_selection($p_resultat,$p_type)
		{
			$style_ln = '';
			$style_fp = '';
			
			$class_ln = ' c_pointer';
			$class_fp = ' c_pointer';
			$onclick_fp_first = '';
			$onclick_fp_previous = '';
			$onclick_ln_last = '';
			$onclick_ln_next = '';
			
			$hover_fp_first = '';
			$hover_fp_previous = '';
			$hover_ln_last = '';
			$hover_ln_next = '';
			
			if($this->c_active_page == 1)
			{
				// first, previous
				$class_fp = ' grey_el';
				
				if(ceil($this->c_recordset_line / $this->c_nb_line) <= 1)
				{
					// No next or previous page
					$class_ln = ' grey_el';
				}
				else 
				{
					$onclick_ln_last = 'onclick="vimofy_page_change_ajax(\''.$this->c_id.'\',__VIMOFY_LAST__);"';
					$onclick_ln_next = 'onclick="vimofy_page_change_ajax(\''.$this->c_id.'\',__VIMOFY_NEXT__);"';
					$hover_ln_last = $this->hover_out_lib(15,15);
					$hover_ln_next = $this->hover_out_lib(14,14);
				}
			}
			else
			{ 
				if($this->c_active_page == ceil($this->c_recordset_line / $this->c_nb_line))
				{
					$class_ln = ' grey_el';
					
				}
				else 
				{
					$onclick_ln_last = 'onclick="vimofy_page_change_ajax(\''.$this->c_id.'\',__VIMOFY_LAST__);"';
					$onclick_ln_next = 'onclick="vimofy_page_change_ajax(\''.$this->c_id.'\',__VIMOFY_NEXT__);"';
					$hover_ln_last = $this->hover_out_lib(15,15);
					$hover_ln_next = $this->hover_out_lib(14,14);
				}
				$onclick_fp_first = 'onclick="vimofy_page_change_ajax(\''.$this->c_id.'\',__VIMOFY_FIRST__);"';
				$onclick_fp_previous = 'onclick="vimofy_page_change_ajax(\''.$this->c_id.'\',__VIMOFY_PREVIOUS__);"';
				$hover_fp_first = $this->hover_out_lib(12,12);
				$hover_fp_previous = $this->hover_out_lib(13,13);
			}
			
				
			$nb_line = $this->c_obj_bdd->rds_num_rows($p_resultat);
			
			if($this->c_active_page == ceil($this->c_recordset_line / $this->c_nb_line))
			{
				$to = $this->c_limit_min + $nb_line;
			}
			else
			{
				$to = ($this->c_nb_line + $this->c_limit_min);
			}
			
			$onkeyup_page = '';
			$onkeyup_line = '';
			if($p_type == __HEADER__)
			{
				if($this->c_page_selection_display['footer'])
				{
					$onkeyup_page = 'document.getElementById(\''.$this->c_id.'_page_selection_footer\').value = this.value;';
					$onkeyup_line = 'document.getElementById(\''.$this->c_id.'_line_selection_footer\').value = this.value;';
				}
			}
			else
			{
				if($this->c_page_selection_display['header'])
				{
					$onkeyup_page = 'document.getElementById(\''.$this->c_id.'_line_selection_header\').value = this.value;';
					$onkeyup_line = 'document.getElementById(\''.$this->c_id.'_line_selection_footer\').value = this.value;';
				}
			}
			
			$html  = '<table class="__'.$this->c_theme.'_table_info" cellpadding="0" cellspacing="0">';
			$html .= '<tr>';
			if($this->c_navbar_nav_button_activate)
			{
				$html .= '<td class="__'.$this->c_theme.'_infobar"><div '.$hover_fp_first.' '.$style_fp.' '.$onclick_fp_first.' class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_first __'.$this->c_theme.'_table_page_selection '.$class_fp.'" ></div></td>';
				$html .= '<td class="__'.$this->c_theme.'_infobar __'.$this->c_theme.'_infobar_separator_right"><div '.$style_fp.' '.$onclick_fp_previous.' class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_previous __'.$this->c_theme.'_table_page_selection '.$class_fp.'" '.$hover_fp_previous.'></div></td>';
			}
			$html .= '<td class="__'.$this->c_theme.'_infobar __'.$this->c_theme.'_infobar_separator_right __'.$this->c_theme.'_infobar_separator_left"> '.$this->lib(22).' <input id="'.$this->c_id.'_page_selection_'.$p_type.'" class="__'.$this->c_theme.'_input_text __'.$this->c_theme.'__input_h" onkeyup="'.$onkeyup_page.'vimofy_input_page_change(event,\''.$this->c_id.'\',this);" type="text" size=1 value="'.$this->c_active_page.'"/> '.$this->lib(23).' '.ceil($this->c_recordset_line / $this->c_nb_line).'</td>';
			if($this->c_navbar_nav_button_activate)
			{
				$html .= '<td class="__'.$this->c_theme.'_infobar __'.$this->c_theme.'_infobar_separator_left"><div '.$style_ln.' '.$onclick_ln_next.' class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_next __'.$this->c_theme.'_table_page_selection '.$class_ln.'" '.$hover_ln_next.'></div></td>';
				$html .= '<td class="__'.$this->c_theme.'_infobar __'.$this->c_theme.'_infobar_separator_right"><div '.$style_ln.' '.$onclick_ln_last.' class="__'.$this->c_theme.'_table_page_selection '.$class_ln.' __'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_last" '.$hover_ln_last.'></div></td>';			
			}
			$html .= '<td class="__'.$this->c_theme.'_infobar __'.$this->c_theme.'_infobar_separator_right __'.$this->c_theme.'_infobar_separator_left"><input id="'.$this->c_id.'_line_selection_'.$p_type.'" type="text" onkeyup="'.$onkeyup_line.'vimofy_input_line_per_page_change(event,\''.$this->c_id.'\',this);" value="'.$this->c_nb_line.'" size="2" class="__'.$this->c_theme.'_input_text __'.$this->c_theme.'__input_h"/> ';
			if($this->c_navbar_txt_line_per_page_activate)
			{
				$html .= $this->lib(24);
			}
			
			$html .= '</td>';
			if($this->c_navbar_refresh_button_activate)
			$html .= '<td class="__'.$this->c_theme.'_infobar __'.$this->c_theme.'_infobar_separator_left __'.$this->c_theme.'_infobar_separator_right"><div onclick="vimofy_refresh_page_ajax(\''.$this->c_id.'\');" class="c_pointer __'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_refresh" '.$this->hover_out_lib(11,11).'></div></td>';
			
			if($this->c_mode != __CMOD__ && $this->c_navbar_txt_activate)
			{
				$html .= '<td class="__'.$this->c_theme.'_infobar __'.$this->c_theme.'_infobar_separator_left">'.$this->lib(25).' : '.($this->c_limit_min + 1).' '.$this->lib(26).' '.$to.' '.$this->lib(27).' '.$this->c_recordset_line.' '.$this->lib(28).' ';
			
				if($this->c_active_page == ceil($this->c_recordset_line / $this->c_nb_line) && ceil($this->c_recordset_line / $this->c_nb_line) > 1)
					$html .= str_replace('$x',$nb_line,$this->lib(29));
				
				$html .= '</td>';
			}
			$html .= '</tr>';
			$html .= '</table>';

			return $html;
		}
		
		/**
		 * Generate the header of each define columns.
		 * @param boolean $p_edit false if not in edit mode, true in other case
		 * @param recordset $p_resultat database result
		 */
		private function generate_columns_header($p_edit,&$p_resultat,&$p_result_header)
		{
			// Create a new line
			$html = '<tr id="tr_header_title_'.$this->c_id.'">';
			
			/**==================================================================
			 * Create the first column (checkbox and edit button)
			 ====================================================================*/	
			$html .= '<td align="left" id="header_th_0__'.$this->c_id.'" class="__'.$this->c_theme.'__cell_opt_h"><div id="th0_'.$this->c_id.'"></div></td>';
			$html .= '<td></td>';
			/*===================================================================*/
			
			// Quantify of order clause
			$qtt_order = $this->get_nbr_order();
			
			/**==================================================================
			 * Display the resize cursor or not
			 ====================================================================*/	
			if($this->c_mode != __CMOD__)
			{
				$cursor = ' cur_resize';
			}
			else
			{
				$cursor = '';
			}
			/*===================================================================*/
			
			/**==================================================================
			 * Browse all columns
			 ====================================================================*/	
			foreach($this->c_columns as $key_col => $val_col)
			{
				if($val_col['display'])
				{
					/**==================================================================
					 * Define order icon
					 ====================================================================*/	
					if($this->c_columns[$key_col]['order_by'] != false)
					{
						// An order clause is defined
						if($this->c_columns[$key_col]['order_by'] == __ASC__)
						{
							// ASC icon
							$class_order = ' __'.$this->c_theme.'_ico_sort-ascend __'.$this->c_theme.'_ico';
						}
						else 
						{
							// DESC icon
							$class_order = ' __'.$this->c_theme.'_ico_sort-descend __'.$this->c_theme.'_ico';
						}
						
						// Display the number of order only if there is more than one order clause
						($qtt_order > 1) ? $order_prio = $this->c_columns[$key_col]['order_priority'] : $order_prio = '';
					}
					else
					{
						// No order clause defined
						$class_order = '';
						$order_prio = '';
					}
					
					// Order column
					$html .= '<td class="__'.$this->c_theme.'_bloc_empty'.$class_order.'"><span class="__vimofy_txt_mini_ vimofy_txt_top">'.$order_prio.'</span></td>';
					/*===================================================================*/
					
					/**==================================================================
					 * Define order icon
					 ====================================================================*/	
					if($this->c_mode != __CMOD__)
					{
						$onmousedown = 'onmousedown="vimofy_resize_column_start('.$key_col.',\''.$this->c_id.'\');"';
						$ondblclick = 'ondblclick="vimofy_mini_size_column('.$key_col.',\''.$this->c_id.'\');" ';
						$lib_redim = $this->hover_out_lib(17,17);
						$event = $this->hover_out_lib(40,40).' onmousedown="vimofy_move_column_start(event,'.$key_col.',\''.$this->c_id.'\');"';
					}
					else
					{
						$onmousedown = '';
						$ondblclick = '';
						$lib_redim = '';
						$event = $this->hover_out_lib(40,40).' onmousedown="click_column_order(\''.$this->c_id.'\','.$key_col.');"';
					}
					/*===================================================================*/

					// Column title
					$html .= '<td align="left" class="__'.$this->c_theme.'__cell_h nowrap" id="header_th_'.$key_col.'__'.$this->c_id.'"><div '.$event.' class="align_'.$this->c_columns[$key_col]['alignment'].' __'.$this->c_theme.'_column_title" id="th'.$key_col.'_'.$this->c_id.'"><span id="span_'.$key_col.'_'.$this->c_id.'">'.$this->c_columns[$key_col]['name'].'</span></div></td>';
					
					/**==================================================================
					 * Display other column
					 ====================================================================*/	
					if($p_edit != false) $html .= '<td style="padding:0;margin:0;"></td>';

					$html .= '<td '.$lib_redim.' '.$ondblclick.' '.$onmousedown.' class="__'.$this->c_theme.'__cell_h_resizable'.$cursor.'"><div class="__'.$this->c_theme.'__cell_resize"></div></td>';
					$html .= '<td '.$lib_redim.' '.$ondblclick.' '.$onmousedown.' class="__'.$this->c_theme.'__sep_h'.$cursor.'"></td>';
					$html .= '<td id="right_mark_'.$key_col.'_'.$this->c_id.'" '.$lib_redim.' '.$ondblclick.' '.$onmousedown.' class="__'.$this->c_theme.'__cell_h_resizable'.$cursor.'"><div class="__'.$this->c_theme.'__cell_resize"></div></td>';
					/*===================================================================*/
				}
			}
			
			$html .= '</tr>';
			/*===================================================================*/
			
			
			/**==================================================================
			 * Input for search on the column
			 ====================================================================*/	
			// Create a new line
			$html .= '<tr id="tr_header_input_'.$this->c_id.'">';
			
			// Create the first column (checkbox and edit button)
			$html .= '<td align="left" class="__'.$this->c_theme.'__cell_opt_h"><div id="thf0_'.$this->c_id.'" class="__'.$this->c_theme.'__vimofy_version" onclick="window.open(\'vimofy_bugs\');">v'.$this->c_software_version.'</div></td>';
			
			// Id column display counter
			$id_col_display = 0;
			
		
			/**==================================================================
			 * Browse all columns
			 ====================================================================*/	
			foreach($this->c_columns as $key_col => $val_col)
			{
				if($val_col['display'])
				{
					if($id_col_display == 0)
					{
						$html .= '<td id="th_0_c'.$key_col.'_'.$this->c_id.'"></td>';
					}
					else 
					{
						$html .= '<td id="th_0_c'.($key_col).'_'.$this->c_id.'" '.$this->hover_out_lib(17,17).' '.$ondblclick.' '.$onmousedown.' class="__'.$this->c_theme.'__cell_h_resizable'.$cursor.'"><div class="__'.$this->c_theme.'__cell_resize"></div></td>';
					}
					
					/*if(isset($this->c_columns[$key_col]))
					{*/
						$onmousedown = 'onmousedown="vimofy_resize_column_start('.$key_col.',\''.$this->c_id.'\');"';
						$ondblclick = 'ondblclick="vimofy_mini_size_column('.$key_col.',\''.$this->c_id.'\');" ';
						
						/**==================================================================
						 * Define the filter value
						 ====================================================================*/	
						$filter_input_value = '';
						$state_filter_input = '';
						if(isset($val_col['filter']['input']))
						{
							// A filter was defined by the user
							$filter_input_value = $val_col['filter']['input']['filter'];
						}
						else
						{
							// No filter was defined by the user
							
							// Check if vimofy was in edit mode
							if($p_edit != false && !isset($val_col['rw_flag']) || ($p_edit != false && isset($val_col['rw_flag']) && $val_col['rw_flag'] != __FORBIDEN__))
							{
								// The vimofy was in edit mode, search all same value in the column
								
								// Place the cursor on the first row of the recordset
								if($this->c_obj_bdd->rds_num_rows($p_result_header) > 0)
								{
									$this->c_obj_bdd->rds_data_seek($p_result_header,0);
								}
								// TODO Use a DISTINCT QUERY - Vimofy 1.0
								$key_cold_line = 0;
								$last_value = '';
								$flag_same = false;
								
								while($row = $this->c_obj_bdd->rds_fetch_array($p_result_header))
								{
									if($key_cold_line > 0)
									{
										if($last_value == $row[$val_col['sql_as']])
										{
											$flag_same = true;
										}
										else
										{
											$flag_same = false;
											// The value is not the same of the previous, stop browsing data 
											break;
										}
									}
									else
									{
										$flag_same = true;
									}

									$last_value = $row[$val_col['sql_as']];
									$key_cold_line = $key_cold_line + 1;
								}
								
								if($flag_same)
								{
									$filter_input_value = $last_value;
								}
								else 
								{
									$filter_input_value = '';
								}
							}
							else 
							{
								if($p_edit != false && isset($val_col['rw_flag']) && $val_col['rw_flag'] == __FORBIDEN__)
								{
									$state_filter_input = 'disabled';									// Disable the input because the edition of column is forbiden
								}
							}
						}
						/*===================================================================*/
						
						if(isset($val_col['filter']))
						{
							$class_btn_menu = '__'.$this->c_theme.'_menu_header_on __'.$this->c_theme.'_men_head';
						}
						else 
						{
							if(isset($val_col['lov']) && isset($val_col['is_lovable']) && $val_col['is_lovable'] == true)
							{
								$class_btn_menu = '__'.$this->c_theme.'_menu_header_lovable __'.$this->c_theme.'_men_head';
							}
							else
							{
								if(isset($val_col['lov']))
								{
									$class_btn_menu = '__'.$this->c_theme.'_menu_header_no_icon __'.$this->c_theme.'_men_head';
								}
								else
								{
									$class_btn_menu = '__'.$this->c_theme.'_menu_header __'.$this->c_theme.'_men_head';
								}
							}
						}

						/**==================================================================
						 * Menu button oncontextmenu
						 ====================================================================*/	
						if($this->c_type_internal_vimofy == false)
						{
							// Principal vimofy, diplay internal vimofy
							$oncontextmenu = 'vimofy_display_internal_vim(\''.$this->c_id.'\',__POSSIBLE_VALUES__,'.$key_col.');return false;';
						}
						else
						{
							// Internal vimofy, doesn't display other internal vimofy
							$oncontextmenu = 'return false;';
						}
						/*===================================================================*/
						
						$html .= '<td id="th_1_c'.$key_col.'_'.$this->c_id.'" class="__vimofy_unselectable" style="width:20px;"><div style="width:20px;margin:0;" '.$this->hover_out_lib(21,21).' oncontextmenu="'.$oncontextmenu.'" class="'.$class_btn_menu.'" onclick="vimofy_toggle_header_menu(\''.$this->c_id.'\','.$key_col.');" id="th_menu_'.$key_col.'__'.$this->c_id.'"></div></td>';
						$html .= '<td id="th_2_c'.$key_col.'_'.$this->c_id.'" align="left" class="__'.$this->c_theme.'__cell_h">';
						$html .= '<div style="margin:0 3px;"><input value="'.str_replace('"','&quot;',$filter_input_value).'" class="__'.$this->c_theme.'__input_h full_width" '.$state_filter_input.' id="th_input_'.$key_col.'__'.$this->c_id.'" type="text" style="margin: 2px 0;" size=1 onkeyup="if(document.getElementById(\'chk_edit_c'.$key_col.'_'.$this->c_id.'\'))document.getElementById(\'chk_edit_c'.$key_col.'_'.$this->c_id.'\').checked=true;vimofy_input_keydown(event,this,\''.$this->c_id.'\','.$key_col.');" onchange="vimofy_col_input_change(\''.$this->c_id.'\','.$key_col.');"/></div>';
						if($p_edit != false)
						{
							if($state_filter_input == '')
							{
								$html .= '<td style="width:10px;padding:0;margin:0;"><input '.$this->hover_out_lib(76,76).' type="checkbox" id="chk_edit_c'.$key_col.'_'.$this->c_id.'" style="height:11px;width:11px;margin: 0 5px 0 2px;display:block;"/></td>';
							}
							else
							{
								$html .= '<td style="width:0;padding:0;margin:0;"></td>';
							}
						}
						
						$html .= '</td>';
						$html .= '<td id="th_3_c'.$key_col.'_'.$this->c_id.'" '.$this->hover_out_lib(17,17).' '.$ondblclick.' '.$onmousedown.' class="__'.$this->c_theme.'__cell_h_resizable'.$cursor.'"><div class="__'.$this->c_theme.'__cell_resize"></div></td>';
						$html .= '<td id="th_4_c'.$key_col.'_'.$this->c_id.'" '.$this->hover_out_lib(17,17).' '.$ondblclick.' '.$onmousedown.' class="__'.$this->c_theme.'__sep_h'.$cursor.'"></td>';
					}
					$id_col_display = $id_col_display + 1;
				//}
			}
			/*===================================================================*/
			
			$html .= '<td id="th_0_c'.($key_col+1).'_'.$this->c_id.'" '.$this->hover_out_lib(17,17).' '.$ondblclick.' '.$onmousedown.' class="__'.$this->c_theme.'__cell_h_resizable'.$cursor.'"><div class="__'.$this->c_theme.'__cell_resize"></div></td>';
			$html.= '<td><div style="width:200px"></div></td>';
			$html .= '</tr>';
			/*===================================================================*/

			// Place the cursor on the first row of the recordset
			if($this->c_obj_bdd->rds_num_rows($p_resultat) > 0)
			{
				$this->c_obj_bdd->rds_data_seek($p_resultat,0);
			}
				
			return $html;
		}
	
		private function get_qtt_column()
		{
			return count($this->c_columns);
		}
		
		/**
		 * Generate the data in a table
		 */
		private function generate_data_content($p_resultat,$p_edit)
		{
			// Flag for the line color
			$i_color = 0;

			// Line counter
			$line = 1;
			
			// Vimofy content
			$vimofy = '';
			
			// Quantity of rows
			$num_rows = $this->c_obj_bdd->rds_num_rows($p_resultat);
			
			// Line selected flag
			$line_selected = false;
			
			// Last displayed column
			$last_display_col = $this->get_last_display_column();
			
			/**==================================================================
			* Define the columns & rows style
			====================================================================*/	
			($this->c_cols_sep_display) ? $sep_column = '__'.$this->c_theme.'_col_sep_on' : $sep_column = '__'.$this->c_theme.'_col_sep_off';
			($this->c_cols_sep_display) ? $sep_column_end = '__'.$this->c_theme.'_col_sep_end_on' : $sep_column_end = '__'.$this->c_theme.'_col_sep_end_off';
			($this->c_rows_sep_display) ? $sep_cell = '__'.$this->c_theme.'_cell_sep' : $sep_cell = '';
			/*===================================================================*/
			
			// Parsing sql result
			while($row = $this->c_obj_bdd->rds_fetch_array($p_resultat))
			{
				// Manages the end of line, add a cell_sep if it is the last line
				if($num_rows == $line) $sep_cell = '__'.$this->c_theme.'_cell_sep';
				
				/**==================================================================
				* Line selected managment
				====================================================================*/	
				if(is_array($this->c_selected_lines['key_concat']) && in_array($row['vimofy_internal_key_concat'],$this->c_selected_lines['key_concat']))
				{
					// The line is seleted
					$line_selected_class = 'line_selected_color_'.$i_color.'_'.$this->c_id;
					$checked = 'checked';
				}
				else
				{
					// The line isn't selected
					$line_selected_class = 'lc_'.$i_color.'_'.$this->c_id;
					$checked = '';
				}
				/*===================================================================*/
				
				/**==================================================================
				* Create a new line
				====================================================================*/
				if(!$this->c_type_internal_vimofy)
				{
					// Principal Vimofy
					if(!$p_edit)
					{
						$vimofy .= '<tr onclick="vimofy_checkbox('.$line.',event,null,\''.$this->c_id.'\');" id="l'.$line.'_'.$this->c_id.'" class="'.$line_selected_class.'">';
					}
					else
					{
						$vimofy .= '<tr id="l'.$line.'_'.$this->c_id.'" class="'.$line_selected_class.'">';
					}
				}
				else
				{
					// Internal Vimofy
					switch($this->c_type_internal_vimofy) 
					{
						case '__POSSIBLE_VALUES__':
							$vimofy .= '<tr onclick="vimofy_child_insert_into_parent(\'div_td_l'.$line.'_c'.$this->get_id_col_lov($this->c_col_return).'_'.$this->c_id.'\',\''.$this->c_id_parent.'\','.$this->c_id_parent_column.');" id="l'.$line.'_'.$this->c_id.'" class="lc_'.$i_color.'_'.$this->c_id.'" '.$this->hover_out_lib(65,65).'>';
							break;
						case '__LOAD_FILTER__':
							$vimofy .= '<tr onclick="vimofy_load_filter(\''.$this->c_id_parent.'\',\'div_td_l'.$line.'_c'.$this->get_id_col_lov($this->c_col_return).'_'.$this->c_id.'\');" id="l'.$line.'_'.$this->c_id.'" class="lc_'.$i_color.'_'.$this->c_id.'">';
							break;
					}
				}
				/*===================================================================*/
				
				/**==================================================================
				* Create the first column (checkbox and edit button)
				====================================================================*/
				$vimofy .= '<td align="left" id="td_l'.$line.'_c0_'.$this->c_id.'" class="__'.$this->c_theme.'__cell_opt '.$sep_cell.'"><div id="div_td_l'.$line.'_c0_'.$this->c_id.'"><table><tr>';
				if(!$p_edit)
				{
					$vimofy .= '<td><input class="vimofy_checkbox" '.$checked.' onclick="vimofy_checkbox('.$line.',event,true,\''.$this->c_id.'\');" id="chk_l'.$line.'_c0_'.$this->c_id.'" type="checkbox" '.$this->hover_out_lib(77,77).'/></td>';
				}
				else
				{
					$vimofy .= '<td></td>';
				}
				
				// If the vimofy is in read & write mode, add the edit button
				if($this->c_readonly == __RW__ && !$p_edit)
				{
					$vimofy .= '<td style="padding:0;vertical-align:middle;"><div '.$this->hover_out_lib(16,16).' onclick="edit_lines(event,'.$line.',\''.$this->c_id.'\');" class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_page_edit"></div></td>';
				}
				else
				{
					$vimofy .= '<td style="padding:0;vertical-align:middle;"><div '.$this->hover_out_lib(16,16).' onclick="edit_lines(event,'.$line.',\''.$this->c_id.'\');" class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_page_edit" style="visibility:hidden;"></div></td>';
				}
				
				$vimofy .= '</tr></table></div></td>';
				/*===================================================================*/
				
				/**==================================================================
				* Create all data columns
				====================================================================*/
				foreach($this->c_columns as $key_col => $val_col)
				{
					if($val_col['display'])
					{
						($line == 1) ? $key_cold_l = 'id="td_0_c'.$key_col.'_'.$this->c_id.'"' : $key_cold_l = '';
						
						
						$vimofy .= '<td '.$key_cold_l.' class="'.$sep_cell.$this->c_columns[$key_col]['nowrap'].'"><div class="__'.$this->c_theme.'__cell_resize"></div></td>';
								
						$content = $this->get_data_in_html($key_col,$row[$this->c_columns[$key_col]['sql_as']]);
						// Data column
						if($line == 1)
						{
							$key_cold_id_l1 = 'id="td_1_c'.$key_col.'_'.$this->c_id.'"';
							$key_cold_id_l2 = 'id="td_2_c'.$key_col.'_'.$this->c_id.'"';
							$key_cold_id_l3 = 'id="td_3_c'.$key_col.'_'.$this->c_id.'"';
						}
						else
						{
							$key_cold_id_l1 = '';
							$key_cold_id_l2 = '';
							$key_cold_id_l3 = '';
						}
						$vimofy .= '<td '.$key_cold_id_l1.' align="'.$this->c_columns[$key_col]['alignment'].'"  class="__'.$this->c_theme.'__cell '.$sep_cell.''.$this->c_columns[$key_col]['nowrap'].'"><div id="div_td_l'.$line.'_c'.$key_col.'_'.$this->c_id.'" class="div_content">'.$content.'</div></td>';
						
						// Right border column
						$vimofy .= '<td class="'.$sep_cell.$this->c_columns[$key_col]['nowrap'].'" '.$key_cold_id_l2.'><div class="__'.$this->c_theme.'__cell_resize"></div></td>';
						
						// Sep column
						if($key_col != $last_display_col)
						{
							$vimofy .= '<td '.$key_cold_id_l3.' class="__'.$this->c_theme.'__sep '.$sep_column.'"></td>';
						}
						else 
						{
							$vimofy .= '<td '.$key_cold_id_l3.' class="__'.$this->c_theme.'__sep '.$sep_column_end.'"></td>';
						}
					}
				}
				/*===================================================================*/
				
				$vimofy .= '</tr>';

				($i_color == count($this->c_color_mask) - 1) ? $i_color = 0 : $i_color = $i_color + 1;
				$line = $line + 1;
			}
			
			return $vimofy;
		}
		
		/**
		 * Get the data in html format
		 * @param integer $p_column id of the column
		 * @param string $data data of the row
		 */
		private function get_data_in_html($p_column,$data)
		{
			switch($this->c_columns[$p_column]['data_type'])
			{
				case __BBCODE__:
					return $this->convertBBCodetoHTML($data);
					break;
				default:
					return $data;
					break;
			}
		}
		
		/**
		 * Get the id of the last displayed column
		 */
		private function get_last_display_column()
		{
			$last_display_col = 0;
			
			foreach($this->c_columns as $key_col => $val_col)
			{
				if($val_col['display']) $last_display_col = $key_col;
			}
			
			return $last_display_col;
		}
		
		private function get_id_col_lov($p_name)
		{
			$i = 1;
			foreach($this->c_columns as $key => $value) 
			{
				if($p_name == $value['sql_as'])
					return $i;
				$i = $i + 1;
			}
			
			return 1;
		}
		
		/**
		 * Generate Vimofy toolbar
		 */
		public function generate_toolbar($p_edit = false)
		{
			$html  = '<table style="border:0px solid red;margin:0;padding:0;height:22px;" cellpadding="0" cellspacing="0">';
			$html .= '<tr style="border:0px solid red;">';
			
			if(count($this->c_columns) > 1)
				$html .= '<td class="btn_toolbar toolbar_separator_right grey_el"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_search_mode" '.$this->hover_out_lib(0,0).'></div></td>';
			
			if($this->c_mode != __CMOD__)
			{
				$html .= '<td class="btn_toolbar toolbar_separator_right grey_el"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_column_display" onclick="/*vimofy_hide_display_col_lov(\''.$this->c_id.'\',__HIDE_DISPLAY_COLUMN__);*/" '.$this->hover_out_lib(1,1).'></div></td>';
				if($p_edit == false)
				{
					$html .= '<td class="btn_toolbar toolbar_separator_left"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_save" '.$this->hover_out_lib(3,3).' onclick="vimofy_display_prompt_create_filter(\''.$this->c_id.'\');"></div></td>';
					$html .= '<td class="btn_toolbar toolbar_separator_right"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_load" '.$this->hover_out_lib(4,4).' onclick="vimofy_load_filter_lov(\''.$this->c_id.'\',__LOAD_FILTER__);"></div></td>';
				}
				else
				{
					$html .= '<td class="btn_toolbar toolbar_separator_left"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_save grey_el" '.$this->hover_out_lib(3,3).'></div></td>';
					$html .= '<td class="btn_toolbar toolbar_separator_right"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_load grey_el" '.$this->hover_out_lib(4,4).'></div></td>';	
				}
			}
			
			if($p_edit)
			{
				$html .= '<td class="toolbar_separator_left toolbar_separator_right btn_toolbar"><div onclick="vimofy_cancel_edit(\''.$this->c_id.'\');" class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_back" '.$this->hover_out_lib(78,78).'></div></td>';
			}
			else
			{
				$html .= '<td class="toolbar_separator_left toolbar_separator_right btn_toolbar"><div onclick="vimofy_reset(\''.$this->c_id.'\');" class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_display_table" '.$this->hover_out_lib(5,5).'></div></td>';
			}
			
			if($this->c_readonly == __RW__)
			{
				if($this->c_toolbar_add_btn == true)
				{
					$html .= '<td class="toolbar_separator_left btn_toolbar"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_add_line" onclick="add_line(\''.$this->c_id.'\');" '.$this->hover_out_lib(6,6).'></div></td>';
				}
				
				if($p_edit != false)
				{
					$html .= '<td class="btn_toolbar toolbar_separator_left"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_save" onclick="save_lines(\''.$this->c_id.'\');" '.$this->hover_out_lib(50,50).'></div></td>';
				}
				else
				{
					$html .= '<td class="btn_toolbar grey_el" id="vimofy_td_toolbar_edit_'.$this->c_id.'"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_page_edit " onclick="if(count_selected_lines(\''.$this->c_id.'\') > 0)edit_lines(event,null,\''.$this->c_id.'\');" '.$this->hover_out_lib(7,7).'></div></td>';
				}
				
				if($this->c_toolbar_delete_btn == true)
				{
					$html .= '<td class="btn_toolbar toolbar_separator_right grey_el" id="vimofy_td_toolbar_delete_'.$this->c_id.'"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_delete" onclick="if(count_selected_lines(\''.$this->c_id.'\') > 0)delete_lines(\''.$this->c_id.'\',false);" '.$this->hover_out_lib(8,8).'></div></td>';
				}
				else
				{
					$html .= '<td class="toolbar_separator_right"></td>';
				}
			}
			
			$html .= '<td class="toolbar_separator btn_toolbar toolbar_separator_left grey_el"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_excel" '.$this->hover_out_lib(9,9).'></div></td>';
			$html .= '<td class="btn_toolbar"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_help" onclick="window.open(\''.$this->c_dir_obj.'\');" '.$this->hover_out_lib(10,10).'></div></td>';
			$html .= '<td><div id="vim__vimofy_help_hover_'.$this->c_id.'__" class="nowrap"></div></td>';
			$html .= '</tr>';
			$html .= '</table>';
			
			return $html;
		}
		
		/**
		 * Generate an onmouseover & onmouseout event for help
		 * @param decimal $id_lib Id of the text
		 * @param decimal $id_help Id of the help
		 */
		private function hover_out_lib($id_lib,$id_help)
		{
			return 'onmouseout="vimofy_lib_out(\''.$this->c_id.'\');" onmouseover="vimofy_lib_hover('.$id_lib.','.$id_help.',\''.$this->c_id.'\');"';
		}
		
		/**
		 * Generate the title of the vimofy
		 */
		private function generate_title()
		{
			return $this->c_title;
		}
		
		public function generate_lmod_header()
		{
			$html = '<div id="vimofy_lmod_'.$this->c_id.'" class="__'.$this->c_theme.'_vimofy_lmod">
				<table class="shadow" summary="">
					<tr>
						<td class="shadow_l_t"></td>
						<td colspan=2 rowspan=2 class="no_pad_marg"><div id="lmod_vimofy_container_'.$this->c_id.'" class="__'.$this->c_theme.'_lmod_container"></div></td>
					</tr>
					<tr>
						<td class="shadow_l"></td>
					</tr>
					<tr>
						<td class="shadow_l_b"></td>
						<td class="shadow_b"></td>
						<td class="shadow_r_b"></td>
					</tr>	
				</table>
			</div>';
			
			return $html;
		}

		public function generate_lmod_form()
		{
			if($this->c_lmod_specified_width != null)
			{
				$vimofy = '<div class="vimofy_lmod_container gradient" style="width:'.$this->c_lmod_specified_width.'px;">';
				$vimofy .= '<div class="vimofy_lmod" onmousedown="vimofy_lmod_click(\''.$this->c_id.'\');">
					<input id="lst_'.$this->c_id.'" readonly onmousedown="vimofy_StopEventHandler(event);" type="text" class="vimofy_input_lmod gradient" style="width:'.($this->c_lmod_specified_width-20).'px;"/>
				</div>
			</div>';
							
			}
			else
			{
				$vimofy = '<div class="vimofy_lmod_container gradient">';
				$vimofy .= '<div class="vimofy_lmod" onmousedown="vimofy_lmod_click(\''.$this->c_id.'\');">
					<input id="lst_'.$this->c_id.'" readonly onmousedown="vimofy_StopEventHandler(event);" type="text" class="vimofy_input_lmod gradient"/>
				</div>
			</div>';			
			}
			


			return $vimofy;
		}

		public function clear_all_order()
		{
			foreach($this->c_columns as $key => &$value)
			{
				$value["order_by"] = false;
				$value["order_priority"] = false;
			}
		}
		
		
		/**
		 * Generate a calendar
		 */
		public function vimofy_generate_calendar($p_column,$p_year,$p_month,$p_day)
		{
			
			$id_day_of_week = array('FRA' => array(1 => 'L',2 => 'M',3 => 'M',4 => 'J',5 => 'V',6 => 'S',7 => 'D'),
									'ENG'=> array(1 => 'Mo',2 => 'Tu',3 => 'We',4 => 'Th',5 => 'Fr',6 => 'Sa',7 => 'Su'));
			$day_of_week = array('FRA' => array(1 => 'Lundi',2 => 'Mardi',3 => 'Mercredi',4 => 'Jeudi',5 => 'Vendredi',6 => 'Samedi',7 => 'Dimanche'),
								 'ENG' => array(1 => 'Monday',2 => 'Tuesday',3 => 'Wednesday',4 => 'Thursday',5 => 'Friday',6 => 'Saturday',7 => 'Sunday'));
			
			$month_of_year = array('FRA' => array(1 => 'Janvier',2 => 'Février',3 => 'Mars',4 => 'Avril',5 => 'Mai',6 => 'Juin',7 => 'Juillet',8 => 'Août',9 => 'Septembre',10 => 'Octobre',11 => 'Novembre',12 => 'Décembre'),
								   'ENG' => array(1 => 'January',2 => 'February',3 => 'March',4 => 'April',5 => 'May',6 => 'June',7 => 'July',8 => 'August',9 => 'September',10 => 'October',11 => 'November',12 => 'December'));
			$actual_year = $p_year;
			$actual_month = $p_month;
			$actual_day = $p_day;
			
			/**==================================================================
		 	* Control values
		 	====================================================================*/	
			if($actual_month > 12) $actual_month = 12;
			if($actual_month < 1) $actual_month = 1;
			if($actual_year < 1) $actual_year = 1;
			$nbr_day_of_month = cal_days_in_month(CAL_GREGORIAN, $actual_month, $actual_year);
			if($actual_day > $nbr_day_of_month) $actual_day = $nbr_day_of_month;
			if($actual_day < 1) $actual_day = 1;
			/*===================================================================*/	
			
			/**==================================================================
		 	* Get the first day of the month
		 	====================================================================*/	
			$date=mktime(0,0,0,$actual_month,1,$actual_year);
			$first_day = date("N",$date);
			/*===================================================================*/	
			
			
			/**==================================================================
		 	* Get the day of this date
		 	====================================================================*/	
			$date=mktime(0,0,0,$actual_month,$actual_day,$actual_year);
			$day_of_date = date("N",$date);
			/*===================================================================*/	
			
			/**==================================================================
		 	* Define previous and next Year
		 	====================================================================*/
			// Year
			if($actual_year == 1)
			{
				$previous_year = 1;
				$previous_year_class = 'grey_el';
				$previous_year_click = '';
			}
			else
			{
				$previous_year = $actual_year - 1;
				$previous_year_class = '';
				$previous_year_click = 'vimofy_load_date(\''.$this->c_id.'\','.$p_column.','.$previous_year.',null,null);';
			}
			
			$next_year = $actual_year + 1;	
			/*===================================================================*/	
			
			/**==================================================================
		 	* Define previous and next Month
		 	====================================================================*/
			if($actual_month == 1)
			{
				$previous_month = 1;
				$previous_month_class = 'grey_el';
				$previous_month_click = '';
			}
			else
			{
				$previous_month = $actual_month - 1;
				$previous_month_class = '';
				$previous_month_click = 'vimofy_load_date(\''.$this->c_id.'\','.$p_column.',null,'.$previous_month.',null);';
			}
			
			if($actual_month == 12)
			{
				$next_month = 12;
				$next_month_class = 'grey_el';
				$next_month_click = '';
			}
			else
			{
				$next_month = $actual_month + 1;
				$next_month_class = '';
				$next_month_click = 'vimofy_load_date(\''.$this->c_id.'\','.$p_column.',null,'.$next_month.',null);';
			}
			/*===================================================================*/	
			
			/**==================================================================
		 	* Define previous and next day
		 	====================================================================*/
			if($actual_day == 1)
			{
				$previous_day = 1;
				$previous_day_class = 'grey_el';
				$previous_day_click = '';
			}
			else
			{
				$previous_day = $actual_day - 1;
				$previous_day_class = '';
				$previous_day_click = 'vimofy_load_date(\''.$this->c_id.'\','.$p_column.',null,null,'.$previous_day.');';
			}
			
			if($actual_day == $nbr_day_of_month)
			{
				$next_day = $nbr_day_of_month;
				$next_day_class = 'grey_el';
				$next_day_click = '';
			}
			else
			{
				$next_day = $actual_day+1;
				$next_day_class = '';
				$next_day_click = 'vimofy_load_date(\''.$this->c_id.'\','.$p_column.',null,null,'.$next_day.');';
			}
			/*===================================================================*/	
			
			
			$calendar = '<div class="__'.$this->c_theme.'_date_select" style="/*text-align:center;*/">
							<form style="width:100px;margin:0 auto;" action="javascript:vimofy_load_date(\''.$this->c_id.'\','.$p_column.');">
								<table style="border-collapse:collapse;">
									<input type="submit" value="" style="border:0;width:0;height:0;margin:0;padding:0;visibility:hidden;float:left;"/>
										<tr>
											<td><div class="'.$previous_year_class.' c_pointer __'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_top_calendar" '.$this->hover_out_lib(73,73).' onclick="'.$previous_year_click.'"></div></td>
											<td><div class="'.$previous_month_class.' c_pointer __'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_top_calendar" '.$this->hover_out_lib(68,68).' onclick="'.$previous_month_click.'"></div></td>
											<td><div class="'.$previous_day_class.' c_pointer __'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_top_calendar" '.$this->hover_out_lib(70,70).' onclick="'.$previous_day_click.'"></div></td>
										</tr>
										</tr>
											<td><input id="vimofy_cal_year_'.$this->c_id.'" type="text" class="__'.$this->c_theme.'__input_h" style="width:40px;text-align:center;" value="'.$actual_year.'"/></td>
											<td><input id="vimofy_cal_month_'.$this->c_id.'" type="text" class="__'.$this->c_theme.'__input_h" style="width:20px;text-align:center;" value="'.$actual_month.'"/></td>
											<td><input id="vimofy_cal_day_'.$this->c_id.'" type="text" class="__'.$this->c_theme.'__input_h" style="width:20px;text-align:center;" value="'.$actual_day.'"/></td>
										</tr>
										<tr>
											<td><div class="c_pointer __'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_bottom_calendar" '.$this->hover_out_lib(72,72).' onclick="vimofy_load_date(\''.$this->c_id.'\','.$p_column.','.$next_year.',null,null);"></div></td>
											<td><div class="'.$next_month_class.' c_pointer __'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_bottom_calendar" '.$this->hover_out_lib(67,67).' onclick="'.$next_month_click.'"></div></td>
											<td><div class="'.$next_day_class.' c_pointer __'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_bottom_calendar" '.$this->hover_out_lib(71,71).' onclick="'.$next_day_click.'"></div></td>
										</tr>
								</table>
							</form>
						</div>
						<div id="calendar_load_'.$this->c_id.'" class="vimofy_calendar_load">
							<div class="vimofy_calendar_load_icon"></div>
						</div><table class="vimofy_calendar">';
			/**==================================================================
		 	* Construct the calendar (header)
		 	====================================================================*/
			$calendar .= '<tr class="vimofy_calendar">';
			foreach($id_day_of_week[$this->c_lng] as $key => $value)
			{
				if($key == $day_of_date)
				{
					$calendar .= '<th class="vimofy_calendar"><div class="vimofy_txt_bold vimofy_calendar_h">'.$value.'</div></th>';
				}
				else
				{
					$calendar .= '<th class="vimofy_calendar"><div class="vimofy_txt_lighter vimofy_calendar_h">'.$value.'</div></th>';
				}
				
			}
			$calendar .= '</tr>';
			/*===================================================================*/	
			
			/**==================================================================
		 	* Construct the calendar (days)
		 	====================================================================*/
			$calendar .= '<tr class="vimofy_calendar">';
			$day_in_line = 1;
			
			// Go to the first day
			for($day = 1; $day < $first_day ;$day++)
			{
				$calendar .= '<td class="vimofy_calendar"></td>';
				$day_in_line++;
			}
			
			
			for($day = 1; $day <= $nbr_day_of_month ;$day++)
			{
				if($day_in_line > 7)
				{
					$calendar .= '</tr>';
					$calendar .= '<tr>';
					$day_in_line = 1;
				}
				
				if($day == $actual_day)
				{
					$calendar .= '<td '.$this->hover_out_lib(69,69).' class="vimofy_calendar"><div onclick="vimofy_insert_date(\''.$this->c_id.'\','.$p_column.',\''.$day.'\');" class="vimofy_calendar_actual_day vimofy_calendar_day">'.$day.'</div></td>';
				}
				else
				{
					$calendar .= '<td '.$this->hover_out_lib(69,69).' class="vimofy_calendar"><div onclick="vimofy_insert_date(\''.$this->c_id.'\','.$p_column.',\''.$day.'\');" class="vimofy_calendar_day">'.$day.'</div></td>';
				}
				$day_in_line++;
			}
			$calendar .= '</tr>';
			/*===================================================================*/	
			
			
			
			
			$calendar .= '</table>';
			$calendar .= '<div class="__'.$this->c_theme.'_calendar_footer">
							<table>
								<tr class="vimofy_calendar">
									<td class="vimofy_calendar"><div style="width:130px;font-family: arial, sans-serif;font-size: 11px;overflow: hidden;height:15px;">'.$day_of_week[$this->c_lng][$day_of_date].' '.$actual_day.' '.$month_of_year[$this->c_lng][$actual_month].' '.$actual_year.'</div></td>
									<td class="vimofy_calendar"><div class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_cancel hover" '.$this->hover_out_lib(32,32).' style="float:right;margin-right:5px;" onclick="vimofy_close_calendar(\''.$this->c_id.'\')"></div></td>
								</tr>
							</table>
						</div>';
			echo $calendar;
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Setter
		 ====================================================================*/	
		/**
		 * Set default input focus
		 */		
		public function define_input_focus($column_name)
		{
			$this->c_default_input_focus = $column_name;
		}
		
		public function set_recordser_line($nbr)
		{
			$this->c_recordset_line = $nbr;
		}
		
		public function define_param_adv_filter($p_param_adv_filter)
		{
			$this->c_param_adv_filter = $p_param_adv_filter;
		}
		
		public function define_active_page($page)
		{
			$this->c_active_page = $page;
		}
		
		public function define_parent($p_parent,$p_column)
		{
			$this->c_id_parent = $p_parent;
			$this->c_id_parent_column = $p_column;
		}

		/**
		 * Define height and with of the Vimofy
		 * @param int $p_width width of the vimofy
		 * @param int $p_w_unity width unity of the vimofy (px,%)
		 * @param int $p_height height of the vimofy
		 * @param int $p_h_unity height unity of the vimofy (px,%)
		 */
		public function define_size($p_width,$p_w_unity,$p_height,$p_h_unity)
		{
			$this->c_width = $p_width;
			$this->c_w_unity = $p_w_unity;
			$this->c_height = $p_height;
			$this->c_h_unity = $p_h_unity;
		}
		
		
		/**
		 * Define color property for a line
		 * 
		 * @param unknown_type $p_color_hex	Background color of a line
		 * @param unknown_type $p_color_hover_hex Background color of a line on hover
		 * @param unknown_type $p_color_selected Background color of a line when his selected
		 * @param unknown_type $p_color_text Color for the text
		 * @param unknown_type $p_color_text_selected Color for the text when it is selected
		 */
		public function define_color_mask($p_color_hex,$p_color_hover_hex,$p_color_selected,$p_color_text,$p_color_text_selected)
		{
			$this->c_color_mask[] = array("color_code" => $p_color_hex,"color_hover_code" => $p_color_hover_hex,"color_selected_code" => $p_color_selected,"color_text" => $p_color_text,"color_text_selected" => $p_color_text_selected);
		}
		
			
		/**
		 * Enable or Disable the columns and rows separation
		 * @param boolean $p_cols true to enable, false to disable
		 * @param boolean $p_rows true to enable, false to disable
		 */
		public function define_sep_col_row($p_cols,$p_rows)
		{
			$this->c_cols_sep_display = $p_cols;
			$this->c_rows_sep_display = $p_rows;
		}
		
		public function define_page_selection_display($p_header,$p_footer)
		{
			$this->c_page_selection_display['header'] = $p_header;
			$this->c_page_selection_display['footer'] = $p_footer;
		}
		
			
		public function define_title_display($display)
		{
			$this->c_title_display = $display;
		}
		
		/**
		 * Define Vimofy title
		 * @param string $p_title Titre de la vimofy
		 */
		public function define_title($p_title)
		{
			$this->c_title = $p_title;
		}
		
		/**
		 * Define state of the Vimofy (readonly or read and write)
		 * @param boolean $p_readonly true : readonly, false : read/write
		 */
		public function define_readonly($p_readonly)
		{
			$this->c_readonly = $p_readonly;
		}
		
		/**
		 * Define vimofy theme
		 * @param string $p_theme vimofy theme
		 */
		public function define_theme($p_theme)
		{
			$this->c_theme = $p_theme;
			$this->c_dir_img = $this->c_img_obj.$this->c_software_version.'/images/'.$this->c_theme;
			
		}
		
			
		/**
		 * Define the state of the text on the navbar
		 * @param boolean $p_state true : display / false : hidden
		 */
		public function define_navbar_txt_activate($p_state)
		{
			$this->c_navbar_txt_activate = $p_state;
		}
		
		/**
		 * Define the state of the refresh button on the navbar
		 * @param boolean $p_state true : display / false : hidden
		 */
		public function define_navbar_refresh_button_activate($p_state)
		{
			$this->c_navbar_refresh_button_activate = $p_state;
		}
		
		/**
		 * Define the state of the nav button on the navbar
		 * @param boolean $p_state true : display / false : hidden
		 */
		public function define_navbar_nav_button_activate($p_state)
		{
			$this->c_navbar_nav_button_activate = $p_state;
		}
		
		/**
		 * Define the state of the line per page text on the navbar
		 * @param boolean $p_state true : display / false : hidden
		 */
		public function define_navbar_txt_line_per_page_activate($p_state)
		{
			$this->c_navbar_txt_line_per_page_activate = $p_state;
		}
		
		public function define_toolbar_delete_button($p_state)
		{
			$this->c_toolbar_delete_btn = $p_state;
		}
		
		public function define_toolbar_add_button($p_state)
		{
			$this->c_toolbar_add_btn = $p_state;
		}
		
		/**
		 * Define number of line per page
		 * @param int $p_nb_line number of line per page
		 */
		public function define_nb_line($p_nb_line)
		{
			$this->c_nb_line = $p_nb_line;
			$this->c_limit_max = $this->c_nb_line;
		}
		
		/**
		 * Define a background logo
		 * @param string $logo
		 * @param string $repeat no-repeat,repeat-x,repeat-y
		 */
		public function define_background_logo($logo,$repeat)
		{
			$this->c_background_logo = $logo;
			$this->c_background_repeat = $repeat;
		}
		
		public function define_limit_min($page)
		{
			$this->c_limit_min = $page;
		}
		
		public function define_limit_max($page)
		{
			$this->c_limit_max = $page;
		}
		
		public function define_lmod_width($p_width)
		{
			$this->c_lmod_specified_width = $p_width;
		}
		
		public function define_mode($vimofy_mode,$return_mode = __MULTIPLE__)
		{
			$this->c_mode = $vimofy_mode;
			$this->c_return_mode = $return_mode;
		}
		
		public function define_col_return($p_col_return)
		{
			$this->c_col_return = $p_col_return;
		}
		
		
		public function define_c_color_mask($p_color_mask)
		{
			$this->c_color_mask = $p_color_mask;
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Internal methods
		 ====================================================================*/	
		private function lib($id)
		{
			return $_SESSION['vimofy'][$this->c_ssid]['lib'][$id];
		}
		
		/**
		 * Return the number of order clause of the vimofy
		 */
		private function get_nbr_order()
		{
			$qtt = 0;
			foreach ($this->c_columns as $value)
			{
				if($value['order_by'] != false)
				{
					$qtt = $qtt + 1;
				}
			}
			return $qtt;
		}

		private function convertBBCodetoHTML($txt)
		{
			$remplacement=true;
			while($remplacement)
			{
				$remplacement=false;
				$oldtxt=$txt;
				$txt = preg_replace('`\[BBTITRE\]([^\[]*)\[/BBTITRE\]`i','<b><u><span class="bbtitre">\\1</span></u></b>',$txt);
				$txt = preg_replace('`\[EMAIL\]([^\[]*)\[/EMAIL\]`i','<a href="mailto:\\1">\\1</a>',$txt);
				$txt = preg_replace('`\[b\]([^\[]*)\[/b\]`i','<b>\\1</b>',$txt);
				$txt = preg_replace('`\[i\]([^\[]*)\[/i\]`i','<i>\\1</i>',$txt);
				$txt = preg_replace('`\[u\]([^\[]*)\[/u\]`i','<u>\\1</u>',$txt);
				$txt = preg_replace('`\[s\]([^\[]*)\[/s\]`i','<label style="text-decoration:line-through;">\\1</label>',$txt);
				$txt = preg_replace('`\[br\]`','<br>',$txt);
				$txt = preg_replace('`\[center\]([^\[]*)\[/center\]`','<div style="text-align: center;">\\1</div>',$txt);
				$txt = preg_replace('`\[left\]([^\[]*)\[/left\]`i','<div style="text-align: left;">\\1</div>',$txt);
				$txt = preg_replace('`\[right\]([^\[]*)\[/right\]`i','<div style="text-align: right;">\\1</div>',$txt);
				$txt = preg_replace('`\[img\]([^\[]*)\[/img\]`i','<img src="\\1" alt=""/>',$txt);
				$txt = preg_replace('`\[color=([^[]*)\]([^[]*)\[/color\]`i','<span style="color:\\1;">\\2</span>',$txt);
				$txt = preg_replace('`\[bg=([^[]*)\]([^[]*)\[/bg\]`i','<span style="background-color: \\1;">\\2</span>',$txt);
				$txt = preg_replace('`\[size=([^[]*)\]([^[]*)\[/size\]`i','<span style="size:"\\1px;">\\2</span>',$txt);
				$txt = preg_replace('`\[font=([^[]*)\]([^[]*)\[/font\]`i','<font face="\\1">\\2</font>',$txt);
				//$txt = preg_replace('`\[url\]([^\[]*)\[/url\]`i','<a  target="_blank" href="\\1">\\1</a>',$txt);
				$txt = preg_replace('`\[url\]([^\[]*)\[/url\]`i','<a target="_blank" href="\\1">\\1</a>',$txt);
				$txt = preg_replace('`\[url=([^[]*)\]([^[]*)\[/url\]`i','<a target="_blank" href="\\1">\\2</a>',$txt);
				
				if ($oldtxt<>$txt)
				{
					$remplacement=true;
				}
			}
			return $txt;
		}
		
		/*===================================================================*/	
	}
?>