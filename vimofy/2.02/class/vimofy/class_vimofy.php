<?php

	class vimofy extends class_sgbd
	{
		/**==================================================================
		 * Attributes
		 ====================================================================*/		
		private $c_id;								// Vimofy Id
		private $c_ssid;							// Session Id
		private $c_lng;								// Language of the vimofy
		private $c_query;							// Query to execute
		private $c_dir_obj;							// Directory of the vimofy object
		private $c_img_obj;							// Directory of the img
		private $c_ident;							// DB Identification
		private $c_columns;							// Columns (array())
		private $c_readonly;						// Vimofy mode -> true : readonly, false read/write
		private $c_theme;							// Vimofy css theme
		private $c_height;							// Vimofy height
		private $c_h_unity;							// Vimofy unity height (px,%)
		private $c_width;							// Vimofy width
		private $c_w_unity;							// Vimofy unity width (px,%)
		private $c_nb_line;							// Number of line per page
		private $c_default_nb_line;					// Number of line per page
		private $c_column_order;					// Order columns
		private $c_max_line_per_page;				// max line per page
		private $c_title;							// Vimofy title
		private $c_color_mask;						// Color mask (array())
		private $c_obj_graphic;						// Graphic instance of Vimofy
		private $c_software_version;				// Version of the vimofy
		private $c_mode;							// LMOD or NMOD
		private $c_return_mode;						// Mode of return (in LMOD mode)
		private $c_col_return;						// Column to return (in LMOD mode)
		private $c_active_page;						// Current page 
		private $c_limit_min;						// Min value of the limit
		private $c_limit_max;						// Max value of the limit
		private $c_recordset_line;					// Total line of the recordset
		private $c_page_qtt_line;					// Total line of the page
		private $c_background_logo;					// Background logo of the vimofy
		private $c_background_repeat;				// Background repeat of the logo
		private $c_id_parent;
		private $c_id_parent_column;	
		private $c_update_table;
		private $c_db_keys;							// Fields list of Primary key
		private $c_selected_lines;
		private $c_prepared_query;
		private $c_edit_mode;
		private $c_param_adv_filter;
		private $c_type_internal_vimofy;
		private	$c_cols_sep_display;
		private $c_rows_sep_display;
		private $c_page_selection_display;
		private $c_vimofy_action;
		private $c_default_input_focus;
		private $c_title_display;
		private $c_position_mode;					// Type of position for LMOD, relative or absolute
		private $c_time_timer_refresh;				// Timer for refresh auto
		private $c_lmod_specified_width;
		/*===================================================================*/	
		
		/**==================================================================
		 * Constructor
		 ====================================================================*/	
		/**
		 * Builder of Vimofy class
		 * @param decimal $p_id
		 * @param string $p_ssid
		 * @param string $p_bdd_server
		 * @param string $p_bdd_user
		 * @param string $p_bdd_password
		 * @param string $p_bdd_schema
		 */
		public function __construct($p_id,$p_ssid,$p_db_engine,$p_ident,$p_dir_obj,$p_img_obj = null,$p_type_internal_vimofy = false,$p_vimofy_active_version = null)
		{
			if(!isset($_GET['lng']))
			{
				$this->c_lng = 'FRA';
			}
			else
			{
				$this->c_lng = $_GET['lng'];
			}

			$this->c_dir_obj = $p_dir_obj;
			if($p_img_obj == null) 
			{
				$this->c_img_obj = $p_dir_obj;
			}
			else
			{
				$this->c_img_obj = $p_img_obj;
			} 

			if($p_vimofy_active_version == null)
			{
				require($p_dir_obj.'/vimofy_active_version.php');
				$this->c_software_version = $vimofy_active_version;
			}
			else
			{
				$this->c_software_version = $p_vimofy_active_version;
			}

			$this->c_id = $p_id;
			$this->c_ssid = $p_ssid;
			$this->c_ident = $p_ident;

			parent::__construct($p_db_engine,$p_ident,$p_dir_obj);
			$this->db_connect();
			$this->c_type_internal_vimofy = $p_type_internal_vimofy;

			// Instance of vimofy graphics
			$this->c_obj_graphic = new graphic_vimofy($this->c_software_version,$this->c_id,$this->c_ssid,$this,$p_dir_obj,$p_img_obj,$this->c_columns,$this->c_selected_lines,$this->c_type_internal_vimofy,$this->c_lng);

			// Init default values
			$this->define_limit_min(0);
			$this->define_readonly(__RW__);	
			$this->define_theme('default');
			$this->define_nb_line(20);	
			$this->define_size(700,'px',500,'px');	
			$this->define_max_nb_line(200);
			$this->define_title('');
			$this->define_active_page(1);
			$this->define_mode(__NMOD__);
			$this->define_col_return(1);
			$this->c_edit_mode = __DISPLAY_MODE__;
			$this->define_param_adv_filter('f1');
			$this->define_background_logo('','');
			$this->define_sep_col_row(true,true);
			$this->define_page_selection_display(true,true);
			$this->define_input_focus(false);
			$this->define_title_display(true);
			$this->define_c_position_mode(__RELATIVE__);
			$this->c_time_timer_refresh = null;
			$this->c_lmod_specified_width = null;
		}
		/*===================================================================*/

		public function __wakeup()
		{
			// Connect to database
			$this->db_connect();	
		}
	
		/**==================================================================
		 * Initialisation methods for the Vimofy
		 ====================================================================*/	
		/**
		 * Define query to execute and display
		 * @param string $query query of the Vimofy
		 */
		public function define_query($query)
		{
			if($query == '')
			{
				die('Query was empty !');
			}
			
			$this->c_query = $query;
		}
		
		public function define_param_adv_filter($p_param_adv_filter)
		{
			$this->c_param_adv_filter = $p_param_adv_filter;
			$this->c_obj_graphic->define_param_adv_filter($p_param_adv_filter);
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
			$this->c_obj_graphic->define_size($p_width,$p_w_unity,$p_height,$p_h_unity);
		}
		
		public function define_c_position_mode($p_type)
		{
			$this->c_position_mode = $p_type;	
		}
		
		/**
		 * Define number of line per page
		 * @param int $p_nb_line number of line per page
		 */
		public function define_nb_line($p_nb_line,$p_selected_lines = false)
		{
			$this->c_default_nb_line = $p_nb_line;
			$this->change_nb_line($p_nb_line,$p_selected_lines);
		}
		
		public function change_nb_line($p_nb_line,$p_selected_lines = false)
		{
			// Set the selected lines to edit
			if($p_selected_lines != false) $this->define_selected_line($p_selected_lines);
			$this->c_nb_line = $p_nb_line;
			$this->c_limit_max = $this->c_nb_line;
			
			$this->c_obj_graphic->define_nb_line($p_nb_line);
			$this->define_active_page(1);
			$this->define_limit_min(0);
		}
		
		public function define_key($p_array_keys)
		{
			foreach($p_array_keys as $value)
			{
				$column_id = $this->get_id_column($value);
				
				if(!is_null($column_id))
				{
					// The column exist
					$this->c_columns[$column_id]['is_key_part'] = $value;
				}
				else
				{
					// The column does not exist
					$this->define_column($value,$value,__TEXT__,__WRAP__,__CENTER__,__PERCENT__,__HIDE__);
					$col_name = $this->get_id_column($value);
					$this->c_columns[$col_name]['is_key_part'] = $value;
					$this->c_columns[$col_name]['auto_create_column'] = true;
				}
			}
			$this->c_db_keys = $p_array_keys;
		}
		
		/**
		 * Set default input focus
		 */		
		public function define_input_focus($column_name)
		{
			$this->c_default_input_focus = $column_name;
			$this->c_obj_graphic->define_input_focus($column_name);
		}
		
		public function define_toolbar_delete_button($p_state)
		{
			$this->c_obj_graphic->define_toolbar_delete_button($p_state);
		}
		
		public function define_toolbar_add_button($p_state)
		{
			$this->c_obj_graphic->define_toolbar_add_button($p_state);
		}
		
		/**
		 * Define the state of the text on the navbar
		 * @param boolean $p_state true : display / false : hidden
		 */
		public function define_navbar_txt_activate($p_state)
		{
			$this->c_obj_graphic->define_navbar_txt_activate($p_state);
		}
		
		/**
		 * Define the state of the refresh button on the navbar
		 * @param boolean $p_state true : display / false : hidden
		 */
		public function define_navbar_refresh_button_activate($p_state)
		{
			$this->c_obj_graphic->define_navbar_refresh_button_activate($p_state);
		}
		
		/**
		 * Define the state of the nav button on the navbar
		 * @param boolean $p_state true : display / false : hidden
		 */
		public function define_navbar_nav_button_activate($p_state)
		{
			$this->c_obj_graphic->define_navbar_nav_button_activate($p_state);
		}
		
		/**
		 * Define the state of the line per page text on the navbar
		 * @param boolean $p_state true : display / false : hidden
		 */
		public function define_navbar_txt_line_per_page_activate($p_state)
		{
			$this->c_obj_graphic->define_navbar_txt_line_per_page_activate($p_state);
		}
		
		/**
		 * Define max number of line per page
		 * @param int $p_nb_line max number of line per page
		 */
		public function define_max_nb_line($p_nb_line)
		{
			$this->c_max_line_per_page = $p_nb_line;
		}
		
		/**
		 * Define vimofy theme
		 * @param string $p_theme vimofy theme
		 */
		public function define_theme($p_theme)
		{
			$this->c_theme = $p_theme;
			$this->c_obj_graphic->define_theme($p_theme);
			$this->c_dir_img = $this->c_software_version.'/images/'.$this->c_theme;
		}
		
		/**
		 * Define state of the Vimofy (readonly or read and write)
		 * @param boolean $p_readonly true : readonly, false : read/write
		 */
		public function define_readonly($p_readonly)
		{
			$this->c_readonly = $p_readonly;
			$this->c_obj_graphic->define_readonly($p_readonly);
			
			if($p_readonly == __RW__ && $this->c_return_mode == __SIMPLE__)
			{
				//$this->define_mode($this->c_mode,__MULTIPLE__);
			}
		}
		
		/**
		 * Define Vimofy title
		 * @param string $p_title title of the vimofy
		 */
		public function define_title($p_title)
		{
			$this->c_title = $p_title;
			$this->c_obj_graphic->define_title($p_title);
		}
		
		/**
		 * Define a new column
		 * @param itn $p_column_id Id of the columnn in the SQL
		 * @param string $p_name Column title
		 * @param string $p_data_type Data type
		 * @param string $p_nowrap true : nowrap enabled, false nowrap disabled
		 * @param string $p_alignment text alignment into a column
		 */
		public function define_column($p_column_id,$p_name,$p_data_type,$p_nowrap,$p_alignment = __CENTER__,$p_search_mode = __PERCENT__,$p_display = __DISPLAY__)
		{
			$column_id = count($this->c_columns)+1;
			$this->c_columns[$column_id] = array("original_order" => $column_id,"sql_as" => $p_column_id,"name" => $p_name,"data_type" => $p_data_type,"nowrap" => $p_nowrap,"alignment" => $p_alignment,"order_by" => false,"order_priority" => false,"search_mode" => $p_search_mode,"display" => $p_display,"quick_help" => false);
		}
		
		public function define_column_date_format($column,$p_format)
		{
			$this->c_columns[$this->get_id_column($column)]['date_format'] = $p_format;
		}
		
		/**
		 * Define the right to edit column
		 * @param string $column column name
		 * @param constant $rw : __FORBIDEN__,__REQUIRED__
		 */
		public function define_rw_flag_column($p_column,$p_rw)
		{
			$this->c_columns[$this->get_id_column($p_column)]['rw_flag'] = $p_rw;
		}
		
		/**
		 * Define a function for update query, exemple : MD5(__COL_VALUE__) -> MD5(value)
		 * @param string $p_column column name
		 * @param constant $p_function MD5(__COL_VALUE__),SHA1(__COL_VALUE__),ENCODE("__COL_VALUE__","454fdf")...
		 */
		public function define_col_rw_function($p_column,$p_function)
		{
			$this->c_columns[$this->get_id_column($p_column)]['rw_function'] = $p_function;
		}
		
		/**
		 * Define a function for select query, exemple : MD5(__COL_VALUE__) -> MD5(value)
		 * @param string $p_column column name
		 * @param constant $p_function MD5(__COL_VALUE__),SHA1(__COL_VALUE__),ENCODE("__COL_VALUE__","454fdf")...
		 */
		public function define_col_select_function($p_column,$p_function)
		{
			$this->c_columns[$this->get_id_column($p_column)]['select_function'] = $p_function;
		}
		
		public function define_col_value($p_column,$p_value)
		{
			$this->c_columns[$this->get_id_column($p_column)]['predefined_value'] = $p_value;
		}
		
		/**
		 * Define a List Of Value on a column
		 * @param string $p_sql Query to execute
		 * @param string $p_title Title of the LOV
		 * @param integer $p_col_return Id of the column to return
		 */
		public function define_lov($p_sql,$p_title,$p_col_return)
		{
			$column_id = count($this->c_columns);
			$this->c_columns[$column_id]['lov']['sql'] = $p_sql;
			$this->c_columns[$column_id]['lov']['title'] = $p_title;
			$this->c_columns[$column_id]['lov']['col_return'] = $p_col_return;
			
			/**==================================================================
			 *Search if a taglov is present.
			 ====================================================================*/		
			$motif = '#\|\|TAGLOV_([^\|]+)\*\*([^\|]+)\|\|#';
			preg_match_all($motif,$p_sql,$out);
			
			foreach($out[1] as $key => $value) 
			{
				$this->c_columns[$column_id]['lov']['taglov'][$key]['column'] = $value;
				$this->c_columns[$column_id]['lov']['taglov'][$key]['column_return'] = $out[2][$key];
			}
			/*===================================================================*/	
		}
		
		public function define_update_table($p_update_table)
		{
			$this->c_update_table = $p_update_table;
		}
		
		/**
		 * Define a column for a LOV
		 * @param string $p_column_id id of the column (as)
		 * @param string $p_name Title of the column
		 * @param string $p_data_type data type
		 * @param string $p_nowrap nowrap or wrap
		 * @param string $p_alignment alignment of the content
		 * @param string $p_search_mode search mode (strict or not)
		 * @param string $p_display display the column
		 */
		public function define_column_lov($p_column_id,$p_name,$p_data_type = __TEXT__,$p_nowrap = __WRAP__,$p_alignment = __CENTER__,$p_search_mode = __PERCENT__,$p_display = __DISPLAY__)
		{
			$column_id = count($this->c_columns);
			$this->c_columns[$column_id]['lov']['columns'][$p_column_id]['name'] = $p_name;
			$this->c_columns[$column_id]['lov']['columns'][$p_column_id]['data_type'] = $p_data_type;
			$this->c_columns[$column_id]['lov']['columns'][$p_column_id]['nowrap'] = $p_nowrap;
			$this->c_columns[$column_id]['lov']['columns'][$p_column_id]['alignment'] = $p_alignment;
			$this->c_columns[$column_id]['lov']['columns'][$p_column_id]['search_mode'] = $p_search_mode;
			$this->c_columns[$column_id]['lov']['columns'][$p_column_id]['display'] = $p_display;
		}
		
		public function define_column_lov_order($column_name,$priority,$order,$id_column = null)
		{
			// Get the id of the column
			$column_id = count($this->c_columns);
			$this->c_columns[$column_id]['lov']['columns'][$column_name]['order']['order_priority'] = $priority;
			$this->c_columns[$column_id]['lov']['columns'][$column_name]['order']['order_by'] = $order;
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
			$this->c_obj_graphic->define_color_mask($p_color_hex, $p_color_hover_hex, $p_color_selected, $p_color_text, $p_color_text_selected);
		}
		
		/**
		 * Define the active page
		 * @param integer $page Id of the active page
		 */
		private function define_active_page($page)
		{
			$this->c_active_page = $page;
			$this->c_obj_graphic->define_active_page($this->c_active_page);
		}
		
		public function define_limit_min($page)
		{
			$this->c_limit_min = $page;
			$this->c_obj_graphic->define_limit_min($this->c_limit_min);
		}
		
		public function define_limit_max($page)
		{
			$this->c_limit_max = $page;
			$this->c_obj_graphic->define_limit_max($this->c_limit_max);
		}
		
		public function define_lmod_width($p_width)
		{
			$this->c_lmod_specified_width = $p_width;
			$this->c_obj_graphic->define_lmod_width($p_width);
		}
		
		/**
		 * Define the return mode of the vimofy (simple or multiple)
		 * @param constant $vimofy_mode 
		 * @param constant $return_mode
		 */
		public function define_mode($vimofy_mode,$return_mode = __MULTIPLE__)
		{
			$this->c_mode = $vimofy_mode;
			$this->c_return_mode = $return_mode;
			$this->c_obj_graphic->define_mode($vimofy_mode,$return_mode);
		}
		
		/**
		 * Define the return column id (in LMOD)
		 * @param string $p_col_return col to return
		 */
		public function define_col_return($p_col_return)
		{
			$this->c_col_return = $p_col_return;
			$this->c_obj_graphic->define_col_return($p_col_return);
		}

		/**
		 * Define the type of quick help.
		 * @p_column Column number
		 * @p_mode boolean false : percent,true : strict
		 */
		public function define_col_quick_help($p_column,$p_mode)
		{
			$this->c_columns[$this->get_id_column($p_column)]['quick_help'] = $p_mode;
		}
		
		
		public function define_vimofy_action($p_event,$p_moment,$p_vimofy,$p_action)
		{
			$this->c_vimofy_action[$p_event][] = Array('VIMOFY' => $p_vimofy,'MOMENT' => $p_moment,'ACTION' => $p_action);
		}	
		
		public function define_parent($p_parent,$p_column)
		{
			$this->c_id_parent = $p_parent;
			$this->c_id_parent_column = $p_column;
			$this->c_obj_graphic->define_parent($p_parent,$p_column);
		}
		
		public function define_order_column($column_name,$priority,$order,$id_column = null)
		{
			// Get the id of the column
			if(!is_numeric($id_column))
			{
				$id_column = $this->get_id_column($column_name);
			}
			$this->c_columns[$id_column]['order_priority'] = $priority;
			$this->c_columns[$id_column]['order_by'] = $order;
		}
		
		/**
		 * Define a background logo
		 * @param string $logo
		 * @param string $repeat no-repeat,repeat-x,repeat-y
		 */
		public function define_background_logo($logo,$repeat = 'no-repeat')
		{
			$this->c_background_logo = $logo;
			$this->c_background_repeat = $repeat;
			$this->c_obj_graphic->define_background_logo($logo,$repeat);
		}
		
		
		public function define_c_color_mask($p_color_mask)
		{
			$this->c_color_mask = $p_color_mask;
			$this->c_obj_graphic->define_c_color_mask($p_color_mask);
		}
		
		/**
		 * Define a timer to refresh automatically the vimofy
		 * @param integer $p_time time in ms
		 */
		public function define_auto_refresh_timer($p_time)
		{
			if($p_time < 3000)
			{
				$this->c_time_timer_refresh = 3000;
			}
			else
			{
				$this->c_time_timer_refresh = $p_time;
			}
		}
		
		/**
		 * Enable or Disable the columns and rows separation
		 * @param boolean $p_cols true to enable, false to disable
		 * @param boolean $p_rows true to enable, false to disable
		 */
		public function define_sep_col_row($p_cols = true,$p_rows = true)
		{
			$this->c_cols_sep_display = $p_cols;
			$this->c_rows_sep_display = $p_rows;
			$this->c_obj_graphic->define_sep_col_row($p_cols,$p_rows);
		}
		
		public function define_page_selection_display($p_header,$p_footer)
		{
			$this->c_page_selection_display['header'] = $p_header;
			$this->c_page_selection_display['footer'] = $p_footer;
			$this->c_obj_graphic->define_page_selection_display($p_header,$p_footer);
		}
		
		public function define_title_display($display)
		{
			$this->c_title_display = $display;
			$this->c_obj_graphic->define_title_display($display);
		}
		
		
		/**
		 * Define a filter
		 * @param array $post
		 */
		public function define_filter($post)
		{
			// Set the selected lines to edit
			$this->define_selected_line($post['selected_lines']);
			
			/**==================================================================
			 * Browse the updated filter
			 ====================================================================*/				
			$column = $post['filter_col'];
			if($post['filter'] == '')
			{
				unset($this->c_columns[$column]['filter']['input']);
			}
			else
			{
				$this->c_columns[$column]['filter']['input'] = array('filter' => rawurldecode($post['filter']));

				if(isset($this->c_columns[$column]['lov']))
				{
					/**==================================================================
					 * Replace all taglov
					 ====================================================================*/	
					$sql_src = $this->replace_taglov($column,$this->c_columns[$column]['lov']['sql']);
					/*===================================================================*/	
					
					$sql = 'SELECT * FROM ('.$sql_src.') t WHERE '.$this->c_columns[$column]['lov']['col_return'].' LIKE "%'.$this->protect_sql(rawurldecode($post['filter']),$this->link).'%"';

					$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link,false);
					if($this->c_columns[$column]['quick_help'])
					{
						if($this->rds_num_rows($resultat) == 1)
						{
							$this->c_columns[$column]['taglov_possible'] = true;
							
							while($row = $this->rds_fetch_array($resultat))
							{
								$this->c_columns[$column]['filter']['input']['taglov'] = $row;
							}
						}
						else
						{
							unset($this->c_columns[$column]['taglov_possible']);
						}
					}
					else
					{
						$sql = 'SELECT * FROM ('.$sql_src.') t WHERE '.$this->c_columns[$column]['lov']['col_return'].' = "'.$this->protect_sql(rawurldecode($post['filter']),$this->link).'"';
						$res = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link,false);
						
						if(mysql_num_rows($res) > 0)
						{
							$this->c_columns[$column]['taglov_possible'] = true;
							
							while($row = $this->rds_fetch_array($res))
							{
								$this->c_columns[$column]['filter']['input']['taglov'] = $row;
							}
						}
						else
						{
							unset($this->c_columns[$column]['taglov_possible']);
						}
					}
				}
			}
			
			$this->check_column_lovable();
			
			if(isset($this->c_columns[$column]['filter']) && count($this->c_columns[$column]['filter']) == 0)
			{
				unset($this->c_columns[$column]['filter']);
			}
			/*===================================================================*/	
			
			/**==================================================================
			 * Define active page to 1
			 ====================================================================*/		
			$this->define_active_page(1);
			$this->define_limit_min(0);
			/*===================================================================*/	
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$this->prepare_query();
			
			$json = $this->generate_vimofy_json_param();
			$json_line = $this->generate_json_line();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<json_line>".$this->protect_xml($json_line)."</json_line>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		/*===================================================================*/	
		
		
		
		
		/**==================================================================
		 * Methods
		 ====================================================================*/	
		private function replace_taglov($p_column,$p_query)
		{
			if(isset($this->c_columns[$p_column]['lov']['taglov']))
			{
				foreach($this->c_columns[$p_column]['lov']['taglov'] as $value)
				{
					if(isset($this->c_columns[$this->get_id_column($value['column'])]['filter']['input']['filter']))
					{
						$p_query = str_replace('||TAGLOV_'.$value['column'].'**'.$value['column_return'].'||',$this->c_columns[$this->get_id_column($value['column'])]['filter']['input']['taglov'][$value['column_return']],$p_query);
					}
				}
			}
			return $p_query;
		}
		
		
		private function check_column_lovable()
		{
			/**==================================================================
			 * Check if the column is lovable
			 ====================================================================*/	
			foreach($this->c_columns as $key => $value_col) 
		 	{
	 			if(isset($value_col['lov']['taglov']))
	 			{
	 				foreach($value_col['lov']['taglov'] as $value_lov)
	 				{
	 					if(isset($this->c_columns[$this->get_id_column($value_lov['column'])]['taglov_possible']))
	 					{
	 						$sql = $this->replace_taglov($value_col['original_order'],$value_col['lov']['sql']);
	 						$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link,false);

	 						if($this->rds_num_rows($resultat) > 0)
							{
	 							$possible = true;
							}
							else
							{
	 							$possible = false;
	 							break;
							}
	 					}
	 					else 
	 					{
	 						$possible = false;
	 						break;
	 					}
	 				}
	 				
	 				if(isset($possible) && $possible == true)
	 				{
	 					$this->c_columns[$key]['is_lovable'] = true;
	 				}
	 				else
	 				{
	 					$this->c_columns[$key]['is_lovable'] = false;
	 				}
	 			}
	 			else
	 			{
	 				$this->c_columns[$key]['is_lovable'] = true;
	 			}
		 	}
	 		/*===================================================================*/	
		}
		
		
		/**
		 * Generate js color table
		 */
		public function generate_table_color()
		{
			$html = 'array_js_color_selected_code = new Array();';
			$html .= 'array_js_color_text_selected = new Array();';
			foreach ($this->c_color_mask as $key => $value) 
			{
				$html .= 'array_js_color_selected_code['.$key.'] = \''.$value['color_selected_code'].'\';';
				$html .= 'array_js_color_text_selected['.$key.'] = \''.$value['color_text_selected'].'\';';
			}
			
			return $html;
		}

		/**
		 * Generate style 
		 */
		public function generate_style($p_bal_style = true)
		{
			return $this->c_obj_graphic->generate_style($p_bal_style);
		}
		
		/**
		 * Create the vimofy graphic object
		 */
		public function new_graphic_vimofy()
		{
			// Get the filter option
			$this->get_and_set_filter();
			
			// Prepare the query
			$this->prepare_query();
		}
		
		
		/**
		 * This methode verify if a filter is defined in the url.
		 * If yes, get the filter in the database and load it
		 */
		private function get_and_set_filter($filter_name = null)
		{
			//$tset = $var_test;
			
			// Verify if a filter exist in the URL
			if(isset($_GET[$this->c_param_adv_filter]) || !is_null($filter_name))
			{
				
				if(is_null($filter_name))
				{
					$filter_name = $_GET[$this->c_param_adv_filter];
				}

				/**==================================================================
				 * Get filter values from the database
				 ====================================================================*/
				$sql = 'SELECT '.$this->get_quote_col('id_column').','.$this->get_quote_col('type').','.$this->get_quote_col('val1').','.$this->get_quote_col('val2').','.$this->get_quote_col('val3').'
						FROM vimofy_filters 
						WHERE '.$this->get_quote_col('name').' = '.$this->get_quote_string($this->protect_sql($filter_name,$this->link)).' 
						AND '.$this->get_quote_col('vimofy_id').' = '.$this->get_quote_string($this->protect_sql($this->c_id,$this->link));
				
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				if($this->rds_num_rows($this->resultat) > 0)
				{
					/**==================================================================
					 * Clear all specific attribut
					 ====================================================================*/
					foreach($this->c_columns as $key_column => &$val_column)
					{
						/**==================================================================
						 * Display attribut
						 ====================================================================*/
						$val_column['display'] = __DISPLAY__;
						/*===================================================================*/	
						
						/**==================================================================
						 * Order attribut
						 ====================================================================*/
						$val_column['order_by'] = false;
						$val_column['order_priority'] = false;
						/*===================================================================*/	
					}
					/*===================================================================*/	
				
					$column_temp = array();
					
					while($row = $this->rds_fetch_array($this->resultat))
					{
						$result_array[$row['type']][$row['id_column']] = $row;
					}
					
					
					/**==================================================================
					 * Colum position
					 ====================================================================*/
					if(isset($result_array['CPS']))
					{
						foreach($result_array['CPS'] as $key => $row)
						{
							$column_temp[$row['val1']] = $this->c_columns[$this->get_id_column($row['id_column'])];
						}
					}
					/*===================================================================*/	
					
					/**==================================================================
					 * Order attribut
					 ====================================================================*/
					if(isset($result_array['ORD']))
					{
						foreach($result_array['ORD'] as $key => $row)
						{
							$column_temp[$row['val3']]['order_by'] = $row['val2'];
							$column_temp[$row['val3']]['order_priority'] = $row['val1'];
						}
					}
					/*===================================================================*/
					
					/**==================================================================
					 * Display Mode attribut
					 ====================================================================*/
					if(isset($result_array['DMD']))
					{
						foreach($result_array['DMD'] as $key => $row)
						{
							($row['val2'] == '') ? $val_dmd = false : $val_dmd = true;
							$val_dmd = false;
							$column_temp[$row['val1']]['display'] = $val_dmd;
						}
					}
					/*===================================================================*/
					
					/**==================================================================
					 * Quick search attribut
					 ====================================================================*/
					if(isset($result_array['QSC']))
					{
						foreach($result_array['QSC'] as $key => $row)
						{
							$column_temp[$row['val1']]['filter']['input']['filter'] = $row['val2'];
						}
					}
					/*===================================================================*/
					
					/**==================================================================
					 * Search Mode attribut
					 ====================================================================*/
					if(isset($result_array['SMD']))
					{
						foreach($result_array['SMD'] as $key => $row)
						{
							$column_temp[$row['val1']]['search_mode'] = $row['val2'];
						}
					}
					/*===================================================================*/
					
					/**==================================================================
					 * Column alignment
					 ====================================================================*/
					if(isset($result_array['ALI']))
					{
						foreach($result_array['ALI'] as $key => $row)
						{
							$column_temp[$row['val1']]['alignment'] = $row['val2'];
						}
					}
					/*===================================================================*/
					
					/*foreach($result_array as $key => $row)
					{
						switch($row['type'])
						{
							case 'IEQ':
								;
								break;
							case 'IBT':
								;
								break;
							case 'EEQ':
								;
								break;
							case 'EBT':
								;
								break;
							case 'DMD':
								// Display Mode
								($row['val2'] == '') ? $val_dmd = false : $val_dmd = true;
								$val_dmd = false;
								$column_temp[$row['val1']]['display'] = $val_dmd;
								break;
							case 'QSC':
								// Quick search
								$column_temp[$row['val1']]['filter']['input']['filter'] = $row['val1'];
								break;
							case 'SMD':
								// Search mode
								$column_temp[$row['val1']]['search_mode'] = $row['val2'];
								break;
							case 'CPS':
								// Column position
								// For each column set the position
								$column_temp[$row['val1']] = $this->c_columns[$this->get_id_column($row['id_column'])];
								break;
							case 'ORD':
								// Order
								$column_temp[$row['val3']]['order_by'] = $row['val2'];
								$column_temp[$row['val3']]['order_priority'] = $row['val1'];
								break;
							case 'SIZ':
								;
								break;
						}
					}
					/*===================================================================*/	
					ksort($column_temp);

					$this->c_columns = $column_temp;
				}	
			}
		}
		
		
		private function prepare_query($add_where = '')
		{
			/**==================================================================
			 * Get Column filter
			 ====================================================================*/	
			$sql_filter = '';
			foreach($this->c_columns as $column_key => $column_value)
			{
				if(isset($column_value['filter']))
				{
					foreach ($column_value['filter'] as $filter_key => $filter_value)
					{
						$sql_filter .= ' AND '.$this->get_quote_col($column_value['sql_as']).' '.$this->get_like($column_value['search_mode'].$this->protect_sql($this->replace_chevrons($filter_value['filter'],true),$this->link).$column_value['search_mode']);
					}
				}
			}																		
			/*===================================================================*/	
			
			/**==================================================================
			 * Count the number of line of the query								
			 ====================================================================*/	
			$this->exec_sql('SELECT * FROM ('.$this->c_query.') deriv WHERE 1 = 1 '.$add_where.' '.$sql_filter,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$this->c_recordset_line = $this->rds_num_rows($this->resultat);
			$this->c_obj_graphic->set_recordser_line($this->c_recordset_line);
			/*===================================================================*/	
																					
			/**==================================================================
			 * Prepare and execute the query									
			 ====================================================================*/	
			$order = '';
			$i = 0;
			$array_order = array();
			foreach($this->c_columns as $key => $value)
			{
				if($value['order_by'] != false)
				{
					$array_order[$value['order_priority']] = array("column" => $key,"order_by" => $value['order_by']);
				}
			}

			// Order by priority
			ksort($array_order);

			$i = 0;
			foreach($array_order as $key => $value)
			{
				if($i == 0)
				{
					$order .= ' ORDER BY '.$this->get_quote_col($this->c_columns[$value['column']]['sql_as']).' '.$value['order_by'];
				}
				else 
				{
					$order .= ','.$this->get_quote_col($this->c_columns[$value['column']]['sql_as']).' '.$value['order_by'];
				}
				$i = $i + 1;
			}
		
			$key_concat = "''";
			
			if(is_array($this->c_db_keys))
			{
				foreach($this->c_db_keys as $key_value)
				{
					$key_concat .= ',`'.$key_value.'`';
				}
			}
			
			$prepared_query = 'SELECT *,CONCAT('.$key_concat.') as vimofy_internal_key_concat FROM ('.$this->c_query.') deriv WHERE 1 = 1 '.$add_where.' '.$sql_filter.' '.$order.' '.$this->get_limit($this->c_limit_min,$this->c_limit_max);
			$this->c_prepared_query = $prepared_query;
			$this->exec_sql($prepared_query,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$this->c_page_qtt_line = $this->rds_num_rows($this->resultat);
			/*===================================================================*/	
		}
		
		/**
		 * Generate the vimofy
		 */
		public function generate_vimofy()
		{
			// Draw the vimofy
			$this->check_column_lovable();
			return $this->c_obj_graphic->draw_vimofy($this->resultat,false,null,false);
		}
		
		public function generate_lmod_content()
		{
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat))."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
		}
		
		public function generate_lmod_form()
		{
			return $this->c_obj_graphic->generate_lmod_form();
		}
		
		public function generate_vimofy_json_param($p_generate_column = true,$p_generate_line = true)
		{
			$json_base = 'Vimofy.'.$this->c_id;
			
			$json = '';
			
			if($p_generate_column && $p_generate_line) $json  = $json_base.' = new Object();';
			$json .= $json_base.'.software_version = \''.$this->c_software_version.'\';';
			$json .= $json_base.'.dir_obj = \''.$this->c_dir_obj.$this->c_software_version.'\';';
			$json .= $json_base.'.ssid = \''.$this->c_ssid.'\';';
			$json .= $json_base.'.qtt_column = '.$this->get_qtt_column().';';
			$json .= $json_base.'.total_page = '.ceil($this->c_recordset_line / $this->c_nb_line).';';
			$json .= $json_base.'.active_page = '.$this->c_active_page.';';
			$json .= $json_base.'.qtt_color = '.count($this->c_color_mask).';';
			$json .= $json_base.'.theme = \''.$this->c_theme.'\';';
			$json .= $json_base.'.width = '.$this->c_width.';';
			$json .= $json_base.'.width_unity = \''.$this->c_w_unity.'\';';
			$json .= $json_base.'.height = '.$this->c_height.';';
			$json .= $json_base.'.height_unity  = \''.$this->c_h_unity.'\';';
			$json .= $json_base.'.menu_opened_col  = false;';					// Column opened on a menu
			$json .= $json_base.'.menu_quick_search = false;';					// Quick search menu open
			$json .= $json_base.'.menu_quick_search_col = false;';				// Quick search column
			$json .= $json_base.'.menu_left = 0;';								// Left value for menu on display menu
			$json .= $json_base.'.last_checked = 0;';							// Last line checked
			$json .= $json_base.'.mode = "'.$this->c_mode.'";';					// LMOD or NMOD
			$json .= $json_base.'.lmod_opened = true;';						// LMOD or NMOD
			$json .= $json_base.'.return_mode = "'.$this->c_return_mode.'";';	// Mode of return (in LMOD mode)
			$json .= $json_base.'.c_col_return = "'.$this->c_col_return.'";';	
			$json .= $json_base.'.c_col_return_id = "'.$this->get_id_column($this->c_col_return).'";';	
			$json .= $json_base.'.c_position_mode = "'.$this->c_position_mode.'";';
			
			
			if($this->c_default_input_focus != false)
			{
				$json .= $json_base.'.default_input_focus = '.$this->get_id_column($this->c_default_input_focus).';';
			}
			else
			{
				$json .= $json_base.'.default_input_focus = false;';
			}
			
			if($p_generate_column) 
			{
				$json .= $json_base.'.columns = new Object();';
			 	$json .= $json_base.'.selected_column = new Object();';
			}
			
			if($p_generate_line)
			{
				$json .= $json_base.'.selected_line = new Object();';
				$i = 1;
				
				if(isset($this->c_selected_lines) && is_array($this->c_selected_lines)) // SRX
				{
					foreach($this->c_selected_lines['keys'] as $value) 
					{
						$json .= $json_base.'.selected_line.L'.$i.' = new Object();';
						$json .= $json_base.'.selected_line.L'.$i.'.key = new Object();';
						foreach($value as $key_key => $value_key) 
						{
							$json .= $json_base.'.selected_line.L'.$i.'.key.'.$key_key.' = \''.$value_key.'\';';
						}
						$json .= $json_base.'.selected_line.L'.$i.'.selected = true;';
						$i = $i + 1;
					}
				}
			}
			
			$json .= $json_base.'.stop_click_event = false;';
			$json .= $json_base.'.time_input_search = false;';
			$json .= $json_base.'.input_search_selected_line = 0;';
			$json .= $json_base.'.qtt_line = '.$this->c_page_qtt_line.';';
			$json .= $json_base.'.max_line_per_page = '.$this->c_max_line_per_page.';';
			$json .= $json_base.'.vimofy_child_opened = false;';
			$json .= $json_base.'.edit_mode = '.$this->c_edit_mode.';';
			
			
			/**
			 * Event of the vimofy
			 */
			if(is_array($this->c_vimofy_action))
			{
				$json .= $json_base.'.event = new Object();';
				
				foreach($this->c_vimofy_action as $event => $value_event) 
				{
					foreach ($value_event as $value) 
					{
						$json .= $json_base.'.event.evt'.$event.' = new Object();';
						$json .= $json_base.'.event.evt'.$event.'.'.$value['VIMOFY'].' = new Object();';
						$json .= $json_base.'.event.evt'.$event.'.'.$value['VIMOFY'].'.action = new Object();';
						
						$i = 0;
						foreach ($value['ACTION'] as $action)
						{
							$json .= $json_base.'.event.evt'.$event.'.'.$value['VIMOFY'].'.action.a'.$i.' = new Object();';
							$json .= $json_base.'.event.evt'.$event.'.'.$value['VIMOFY'].'.action.a'.$i.'.exec = \''.str_replace("'","\'",$action).'\';';
							$json .= $json_base.'.event.evt'.$event.'.'.$value['VIMOFY'].'.action.a'.$i.'.moment = '.$value['MOMENT'].';';
							
							$i = $i + 1;
						}
					}
				}
			}
			
			if($p_generate_column) $json .= $this->generate_json_column();
			if($p_generate_line) $json .= $this->generate_json_line();
			
			return $json;
		}
		
		private function get_qtt_column()
		{
			$qtt = 0;
			foreach ($this->c_columns as $value) 
			{
				if($value['display'] == __DISPLAY__) $qtt = $qtt + 1;
			}
			
			return $qtt;
		}
		
		public function generate_json_column()
		{
			$this->check_column_lovable();
			
			$json_base = 'Vimofy.'.$this->c_id;
			
			$json = $json_base.'.columns = new Object();';
			$last_col = '';
			foreach($this->c_columns as $key => $value) 
		 	{
		 		if($value['display'] == __DISPLAY__)
		 		{
			 		$json .= $json_base.'.columns.c'.$key.' = new Object();';
			 		$json .= $json_base.'.columns.c'.$key.'.order = "'.$value['order_by'].'";';
			 		$json .= $json_base.'.columns.c'.$key.'.id = '.$key.';';
			 		$json .= $json_base.'.columns.c'.$key.'.search_mode = \''.$value['search_mode'].'\';';
			 		$json .= $json_base.'.columns.c'.$key.'.alignment = \''.$value['alignment'].'\';';
			 		$json .= $json_base.'.columns.c'.$key.'.data_type = \''.$value['data_type'].'\';';
			 		
			 		if(isset($value['filter']) && count($value['filter']) > 0)
			 		{
			 			$json .= $json_base.'.columns.c'.$key.'.advanced_filter = true;';
			 		}
			 		else 
			 		{
			 			$json .= $json_base.'.columns.c'.$key.'.advanced_filter = false;';
			 		}
			 		
			 		if($value['data_type'] == __DATE__)
			 		{
			 			$json .= $json_base.'.columns.c'.$key.'.date_format = \''.$value['date_format'].'\';';
			 		}
			 		
			 		if(isset($value['lov']))
			 		{
			 			$json .= $json_base.'.columns.c'.$key.'.lov_perso = true;';
			 			$json .= $json_base.'.columns.c'.$key.'.lov_title = \''.str_replace("'","\'",$value['lov']['title']).'\';';
			 			
						/**==================================================================
						 * Check if the column is lovable
						 ====================================================================*/	
			 			if($value['is_lovable'])
			 			{
			 				
			 				$json .= $json_base.'.columns.c'.$key.'.is_lovable = true;';
			 				
			 			}
			 			else
			 			{
			 				$json .= $json_base.'.columns.c'.$key.'.is_lovable = false;';
			 			}
			 			/*===================================================================*/	
			 		}
			 		else
			 		{
			 			$json .= $json_base.'.columns.c'.$key.'.is_lovable = true;';
			 		}
			 		
			 		$last_col = $key;
		 		}
		 	}
		 	
		 	$json .= $json_base.'.last_column = '.($this->get_last_column()+1).';';
		 	
		 	return $json;
		}
		
		/**
		 * Get the id of the last column
		 */
		private function get_last_column()
		{
			$last_col = 0;
			
			foreach($this->c_columns as $key_col => $val_col)
			{
				$last_col = $key_col;
			}
			
			return $last_col;
		}
		
		/**
		 * Generate the json for each visible line.
		 */
		public function generate_json_line()
		{
			$json_base = 'Vimofy.'.$this->c_id;
			$json = $json_base.'.lines = new Object();';
			
			if(is_resource($this->resultat))
			{
				$i = 1;
				
				
				// Place the cursor on the first row of the recordset
				if($this->rds_num_rows($this->resultat) > 0)
				{
					$this->rds_data_seek($this->resultat,0);
				}
				
				// Browse the recorset
				while($row = $this->rds_fetch_array($this->resultat))
				{
					$json .= $json_base.'.lines.L'.$i.' = new Object();';
					$json .= $json_base.'.lines.L'.$i.'.key = new Object();';
					
					// Browse the PRIMARY key
					if(is_array($this->c_db_keys))
					{
						foreach($this->c_db_keys as $value)
						{
							$json .= $json_base.'.lines.L'.$i.'.key.'.$value.' = \''.$row[$value].'\';';
						}
					}
					$i = $i + 1;
				}
				
				// Place the cursor on the first row of the recordset
				if($this->rds_num_rows($this->resultat) > 0)
				{
					$this->rds_data_seek($this->resultat,0);
				}
			}
			return $json;
		}
		
		/**
		 * Generate the javascript body
		 */
		public function generate_js_body()
		{
			$js = $this->generate_table_color();
			$js .= $this->generate_vimofy_json_param();
			
			if($this->c_mode == __NMOD__)
			{
				$js .= "document.getElementById('liste_".$this->c_id."').onscroll = function(){vimofy_horizontal_scroll('".$this->c_id."');};";
			}
			
			return $js;
		}
		

		
		public function draw_vimofy_js()
		{
			if($this->c_mode != __LMOD__)
			{
				$js = "size_table('".$this->c_id."');";
			}
			else
			{
				$js = '';
			}
			
			
			return $js;
		}
		
		public function vimofy_generate_header($p_return_str = false)
		{
			if($p_return_str)
			{
				$header = $this->generate_style(false);		// Unique
			}
			else
			{
				$header = $this->generate_style(true);		
			}
			
			$header .= $this->new_graphic_vimofy();	// Unique

			if($p_return_str)
			{
				return $header;
			}
			else
			{
				echo $header;
			}
		}
		
		public function generate_public_header()
		{
			$this->include_stylesheet();	// Doublon
			$this->include_js_files();		// Doublon
			$this->generate_text();			// Doublon
		}
		
		public function vimofy_generate_js_body($p_return_str = false)
		{
			$js = '';
			if(!$p_return_str)
			{
				$js .= '<script type="text/javascript">';
			}
			
			
			$js .= $this->generate_js_body();
			$js .= $this->draw_vimofy_js();
			
			if($this->c_time_timer_refresh != null)
			{
				$js .= 'Vimofy.'.$this->c_id.'.refresh_auto = window.setInterval(\'vimofy_refresh(\\\''.$this->c_id.'\\\');\','.$this->c_time_timer_refresh.');';
			}
			
			if(!$p_return_str)
			{
				$js .= '</script>';
			}
			
			if($p_return_str)
			{
				return $js;
			}
			else
			{
				echo $js;
			}
		}
		
		private function generate_text()
		{
			$html = '<script type="text/javascript">';
            $html .= 'Vimofy = new Object();';
 			$html .= 'vim_lib = new Array();';
 			
 			if(!isset($_GET['lng']))
			{
				$lng = 'FRA';
			}
			else
			{
				$lng = $_GET['lng'];
			}
			
			$sql = 'SELECT id,corps 
					FROM vimofy_textes 
					WHERE id_lang = "'.$lng.'" 
					AND version_active = "'.$this->c_software_version.'"';
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while($row = $this->rds_fetch_array($this->resultat))
			{
				$row['corps'] = str_replace(chr(10),'',$row['corps']);
				$row['corps'] = str_replace(chr(13),'',$row['corps']);
		
				$_SESSION['vimofy'][$this->c_ssid]['lib'][$row['id']] = $row['corps'];
				$html .= '			vim_lib['.$row['id'].'] = \''.str_replace("'","\'",$row['corps']).'\';';
					
			}
	
            $html .='</script>';
            
            echo $html;
		}
		
		public function include_stylesheet()
		{
			$link =  '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/blue/vimofy_blue.css">';
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/blue/vimofy_blue_icon.css">';
			
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/green/vimofy_green.css">';
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/green/vimofy_green_icon.css">';
			
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/grey/vimofy_grey.css">';
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/grey/vimofy_grey_icon.css">';
			
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/red/vimofy_red.css">';
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/red/vimofy_red_icon.css">';
					
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/vimofy_common.css">';
			$link .= '<link rel="stylesheet" type="text/css" href="'.$this->c_dir_obj.$this->c_software_version.'/css/object/vimofy_msgbox.css">';
			
			echo $link;
		}
		
		public function include_js_files()
		{
			$dir = $this->c_dir_obj.$this->c_software_version;
			
			 echo ' <script type="text/javascript" src="'.$dir.'/js/dom.js"></script>
			 		<script type="text/javascript" src="'.$dir.'/js/macro.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/event.js"></script>
			 		<script type="text/javascript" src="'.$dir.'/js/constant.js"></script>
			 		<script type="text/javascript" src="'.$dir.'/js/json2.js"></script>
			 		<script type="text/javascript" src="'.$dir.'/js/affichage.js"></script>
			 		<script type="text/javascript" src="'.$dir.'/js/page.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/table.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/column.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/lmod.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/input_col.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/vimofy_child.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/object/vimofy_menu.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/object/vimofy_calendar.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/object/ajax.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/object/vimofy_msgbox.js"></script>
					<script type="text/javascript" src="'.$dir.'/js/edit.js"></script>';
		}
		
		public function generate_lmod_header($p_display = true)
		{
			if($p_display)
			{
				echo $this->c_obj_graphic->generate_lmod_header();
			}
			else
			{
				return $this->c_obj_graphic->generate_lmod_header();
			}
		}
		
		private function get_id_column($title)
		{
			foreach($this->c_columns as $key => $value) 
			{
				if(isset($value["sql_as"]) && $value["sql_as"] == $title)
				{
					return $key;
				}
			}
			
			// If nothing was find
			return null;
		}
		
		
		private function clear_all_order()
		{
			foreach($this->c_columns as $key => &$value)
			{
				$value["order_by"] = false;
				$value["order_priority"] = false;
			}
			
			$this->c_obj_graphic->clear_all_order();
		}
		/*===================================================================*/	

		/**==================================================================
		 * Ajax call
		 ====================================================================*/
		
		/**
		 * Generate a calendar
		 * @param integer $p_column Id of the colmun
		 * @param integer $p_year 
		 * @param integer $p_month
		 * @param integer $p_day
		 */
		public function vimofy_generate_calendar($p_column,$p_year = null,$p_month = null,$p_day = null)
		{
			if(is_null($p_year)) $p_year = date('Y');
			if(is_null($p_month)) $p_month = date('n');
			if(is_null($p_day)) $p_day = date('j');
			
			return $this->c_obj_graphic->vimofy_generate_calendar($p_column,$p_year,$p_month,$p_day);
		}
		
		
		/**
		 * Edit the selected lines
		 * @param $json_lines selected lines in json format
		 */
		public function edit_lines($json_lines)
		{
			// Define the Vimofy mode
			$this->c_edit_mode = __EDIT_MODE__;

			// Set the selected lines to edit
			$this->define_selected_line($json_lines);
			
			
			// Reset the filters
			$this->reset_filters();
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/
			// Construct the query
			$sql = 'SELECT ';
			$i_sql = 0;
			
			foreach($this->c_columns as $key_col => $val_col)
			{
				if(!isset($val_col['rw_flag']) ||(isset($val_col['rw_flag']) && $val_col['rw_flag'] != __FORBIDEN__ && $val_col['display']))
				{
					if($i_sql > 0)
					{
						$sql .= ',';
					}
					
					if(isset($val_col['select_function']))
					{
						// Special select function defined
						$sql .= str_replace('__COL_VALUE__',$val_col['sql_as'],$val_col['select_function']).' as '.$val_col['sql_as'];
					}
					else
					{
						$sql .= '`'.$val_col['sql_as'].'`';
					}
					
					$i_sql = $i_sql + 1;
				}
			}
			
			$only_selected_lines = ' AND (';

			$i = 0;
			foreach($this->c_selected_lines['keys'] as $value)
			{
				if($i > 0)
				{
					$only_selected_lines .= ' OR (';
				}
				$j = 0;  
				foreach ($value as $key => $value_key)
				{
					if($j == 0)
					{
						$only_selected_lines .= '`'.$key.'` = "'.$value_key.'"';
					}
					else 
					{
						$only_selected_lines .= ' AND `'.$key.'` = "'.$value_key.'"';
					}
					
					$j = $j + 1;
				}
				$only_selected_lines .= ')';
				$i = $i + 1;
			}
			
			$sql .= ' FROM '.$this->c_update_table.' WHERE 1 = 1 '.$only_selected_lines; 
			$p_result_header = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link,false);

			$this->prepare_query($only_selected_lines);
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,$p_result_header,true,$sql))."</content>";
			$xml .= '<toolbar>'.$this->protect_xml($this->c_obj_graphic->generate_toolbar(true)).'</toolbar>';
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		
		/**
		 * Cancel edition 
		 */
		public function vimofy_cancel_edit()
		{
			$this->c_edit_mode = __DISPLAY_MODE__;
			
			/**==================================================================
			 * Reset selected lines
			 ====================================================================*/	
			$this->c_selected_lines = false;
			/*===================================================================*/
			
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,true,true))."</content>";
			$xml .= '<toolbar>'.$this->protect_xml($this->c_obj_graphic->generate_toolbar()).'</toolbar>';
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
		}
		
		/**
		 * Delete the selected lines
		 * @param $json_lines selected lines in json format
		 */
		public function delete_lines($json_lines)
		{
			// Define the Vimofy mode
			$this->c_edit_mode = __DISPLAY_MODE__;
			
			// Set the selected lines to edit
			$this->define_selected_line($json_lines);
		
			$sql_delete = 'DELETE FROM '.$this->c_update_table.' WHERE (';
			$i = 0;
			foreach($this->c_selected_lines['keys'] as $value)
			{
				if($i > 0)
				{
					$sql_delete .= ' OR (';
				}
				$j = 0;  
				foreach ($value as $key => $value_key)
				{
					if($j == 0)
					{
						$sql_delete .= '`'.$key.'` = "'.$value_key.'"';
					}
					else 
					{
						$sql_delete .= ' AND `'.$key.'` = "'.$value_key.'"';
					}
					
					$j = $j + 1;
				}
				$sql_delete .= ')';
				$i = $i + 1;
			}
			
			// Execute the DELETE query
			if($i > 0)
			{
				$this->exec_sql($sql_delete,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			}
			/*===================================================================*/	
			
			unset($this->c_selected_lines);
			
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,true,true))."</content>";
			$xml .= '<toolbar>'.$this->protect_xml($this->c_obj_graphic->generate_toolbar()).'</toolbar>';
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
		}
		
		public function add_line($json)
		{
			// Transform JSON to an array
			$tab_val_col = json_decode($json,true);
			/**==================================================================
			 * Data control
			 ====================================================================*/	
			$ctrl_ok = true;
			$error_str = '<table>';
			$error_line = array();

			foreach($this->c_columns as $key_col => $value_col) 
			{
				if(isset($value_col['rw_flag']) && $value_col['rw_flag'] == __REQUIRED__ && !isset($value_col['auto_create_column']))
				{
					if($tab_val_col['c'.$value_col['original_order']]['value'] == '')
					{
						$ctrl_ok = false;
						$error_str .='<tr><td align=left>'.str_replace('$name','<b>'.$value_col['name'].'</b>',$this->lib(57)).'</td></tr>';
					}
					$error_line['c'.$key_col]['id'] = $key_col;
					$error_line['c'.$key_col]['status'] = __REQUIRED__;
				}
				else
				{
					if(!isset($value_col['auto_create_column']))
					{
						if(isset($value_col['rw_flag']) && $value_col['rw_flag'] == __FORBIDEN__ && $tab_val_col['c'.$value_col['original_order']]['value'] != '')
						{
							$ctrl_ok = false;
							$error_str .='<tr><td align=left>'.str_replace('$name','<b>'.$value_col['name'].'</b>',$this->lib(58)).'</td></tr>';
						}
						if(isset($value_col['rw_flag']))
						{
							$error_line['c'.$key_col]['id'] = $key_col;
							$error_line['c'.$key_col]['status'] = __FORBIDEN__;
						}
					}
				}	
			}
			
			$error_str .= '</table>';
			/*===================================================================*/	
			
			/**==================================================================
			 * Prepare the insert query
			 ====================================================================*/	
			($ctrl_ok) ? $json_err = 'true' : $json_err = 'false';
			if($ctrl_ok)
			{
				// Control line OK, add the line
				$sql_insert = 'INSERT INTO '.$this->c_update_table.'(';
				$sql_insert_values = '';
				
				$i = 0;
				
				// Browse all columns
				foreach($tab_val_col as $value)
				{
					//$value['value'] = htmlentities($value['value'],ENT_QUOTES,'UTF-8');
					$value['value'] = $this->replace_chevrons($value['value'],true);
					if(!isset($this->c_columns[$value['id']]['rw_flag']) || $this->c_columns[$value['id']]['rw_flag'] != __FORBIDEN__)
					{
						if($value['value'] != "")
						{
						// Add a , if necessary
						if($i > 0)
						{
							$sql_insert .= ',';
							$sql_insert_values .= ',';
						}
						

							$sql_insert .= $this->get_quote_col($this->c_columns[$value['id']]['sql_as']);
						
						// Values
						if(isset($this->c_columns[$value['id']]['rw_function']))
						{
							// Special update function defined on the column
							$sql_insert_values .= str_replace('__COL_VALUE__',$this->protect_sql($value['value'],$this->link),$this->c_columns[$value['id']]['rw_function']);
						}
						else
						{
							if($this->c_columns[$value['id']]['data_type'] == __NUMERIC__)
							{
								$sql_insert_values .= $this->protect_sql($value['value'],$this->link);
							}
							else
							{
								$sql_insert_values .= $this->get_quote_string($this->protect_sql($value['value'],$this->link));
							}
						}
						
						$i = $i + 1;
						}
					}
				}
				
				foreach($this->c_columns as $column_key => $column_value)
				{
					if(isset($column_value['predefined_value']))
					{
						$sql_insert .= ','.$this->get_quote_col($column_value['sql_as']);
						$sql_insert_values .= ','.$this->get_quote_string($this->protect_sql($column_value['predefined_value'],$this->link));
					}
				}
				
				$sql_insert .= ') VALUES ('.$sql_insert_values.');';
				// Insert the line
				$this->exec_sql($sql_insert,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				$this->prepare_query();
				$json = $this->generate_vimofy_json_param();
				
				// XML return	
				header("Content-type: text/xml");
				$xml = "<?xml version='1.0' encoding='UTF8'?>";
				$xml .= "<vimofy>";
				$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,true,true))."</content>";
				$xml .= '<toolbar>'.$this->protect_xml($this->c_obj_graphic->generate_toolbar()).'</toolbar>';
				$xml .= "<json>".$this->protect_xml($json)."</json>";
				$xml .= "<error>".$json_err."</error>";
				$xml .= "</vimofy>";
			}
			else
			{
				// XML return	
				header("Content-type: text/xml");
				$xml = "<?xml version='1.0' encoding='UTF8'?>";
				$xml .= "<vimofy>";
				$xml .= "<error>".$json_err."</error>";
				$xml .= "<error_str>".$this->protect_xml($error_str)."</error_str>";
				$xml .= "<error_col>".$this->protect_xml(json_encode($error_line))."</error_col>";				
				$xml .= "</vimofy>";
			}
			
			echo $xml;
			/*===================================================================*/	
		}
		
		/**
		 * Save lines of the vimofy 
		 * @param json $val_col Selected lines to save
		 */
		public function save_lines($val_col)
		{
			// Define the Vimofy mode
			$this->c_edit_mode = __DISPLAY_MODE__;
			
			// Transform JSON to an array
			$tab_val_col = json_decode($val_col,true);
			/**==================================================================
			 * Update the selected lines with the new values
			 ====================================================================*/		

				/**==================================================================
				 * Create the UPDATE clause
				 ====================================================================*/		
				$sql_update = 'UPDATE '.$this->c_update_table.' ';

				$i = 0;

				// Browse all column to update
				foreach($tab_val_col as $key => $value)
				{
					//$value['value'] = htmlentities($value['value'],ENT_QUOTES,'UTF-8');
					$value['value'] = $this->replace_chevrons($value['value'],true);
					// Add a SET if necessary or a ,
					($i > 0) ? $sql_update .= ',' : $sql_update .= 'SET ';

					if(isset($this->c_columns[$value['id']]['rw_function']))
					{
						// Special update function defined on the column
						$sql_update .= $this->get_quote_col($this->c_columns[$value['id']]['sql_as']).' = '.str_replace('__COL_VALUE__',$this->protect_sql($value['value'],$this->link),$this->c_columns[$value['id']]['rw_function']);
					}
					else
					{
						// No special function
						$sql_update .= $this->get_quote_col($this->c_columns[$value['id']]['sql_as']).' = '.$this->get_quote_string($this->protect_sql($value['value'],$this->link));
					}

					unset($this->c_columns[$value['id']]['filter']);

					// Counter of the column
					$i = $i + 1;
				}

				if($i > 0)
				{
					// A column has changed, execute the query update
					/*===================================================================*/	
					
					/**==================================================================
					 * Create the WHERE clause
					 ====================================================================*/		
					$sql_update .= chr(10).' WHERE';
					
					$i = 0;
					// Browse all selected lines for includes in where clause
					foreach($this->c_selected_lines['keys'] as $value)
					{
						$j = 0;
						
						// Add OR if necessary
						($i > 0) ? $sql_update .= ') OR (' : $sql_update .= ' (';
						
						// Browse all keys of the selected lines
						foreach($value as $selected_key => $selected_value)
						{
							// Add AND if necessary
							($j > 0) ? $sql_update .= ' AND ' : $sql_update .= '';
							
							// WHERE clause
							$sql_update .= $this->get_quote_col($this->c_columns[$this->get_id_column($selected_key)]['sql_as']).' = '.$this->get_quote_string($selected_value); 
							
							// Counter of the selected line keys
							$j = $j + 1;
						}
						
						// Counter of the selected line
						$i = $i + 1;
					}
					
					if($i > 0)
					{
						$sql_update .= ')';		// Close the where clause
					}
					/*===================================================================*/	

					// Execute the update query
					$this->exec_sql($sql_update,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}
			/*===================================================================*/	
			
			// Unselect the lines	
			$this->c_selected_lines = null;
				
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,true,true))."</content>";
			$xml .= '<toolbar>'.$this->protect_xml($this->c_obj_graphic->generate_toolbar()).'</toolbar>';
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
		}
		
		/**
		 * Change the alignment of a column
		 * @param integer $p_column ID of the column
		 * @param constant $p_type_alignment Alignment (__CENTER__,__LEFT__ or __RIGHT__)
		 * @param json $p_selected_lines Selected lines
		 */
		public function change_alignment($p_column,$p_type_alignment,$p_selected_lines)
		{
			// Set the selected lines to edit
			$this->define_selected_line($p_selected_lines);
			$this->c_columns[$p_column]['alignment'] = $p_type_alignment;
																																			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,true,true))."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		
		public function generate_page($p_ajax = null)
		{
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,true,$p_ajax))."</content>";
			$xml .= "<total_page>".ceil($this->c_recordset_line / $this->c_nb_line)."</total_page>";
			$xml .= "<active_page>".$this->c_active_page."</active_page>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		
		/**
		 * Display the previous or the next page
		 * @param string $type __PREVIOUS__ to go to the previous page or __NEXT__ to go to the next page
		 */
		public function vimofy_page_change_ajax($type,$p_selected_lines)
		{
			// Set the selected lines to edit
			$this->define_selected_line($p_selected_lines);
			
			switch ($type) 
			{
				case  '__NEXT__':
					// Go to the next page
					if($this->c_recordset_line < $this->c_nb_line)
					{
						$this->define_active_page(1);
					}
					else
					{
						$this->define_active_page($this->c_active_page+1);
					}
					$this->define_limit_min($this->c_limit_min + $this->c_nb_line);
					break;
				case  '__PREVIOUS__':
					// Go to the previous page
					if($this->c_recordset_line < $this->c_nb_line)
					{
						$this->define_active_page(1);
						$this->define_limit_min($this->c_limit_min);
					}
					else
					{
						$this->define_active_page($this->c_active_page-1);
						$this->define_limit_min($this->c_limit_min - $this->c_nb_line);
					}
					
					break;
				case  '__FIRST__':
					// Go to the first page   
					$this->define_active_page(1);
					$this->define_limit_min(0);
					break;
				case  '__LAST__':
					// Go to the last page
					$this->define_active_page(ceil($this->c_recordset_line / $this->c_nb_line));
					$this->define_limit_min((ceil($this->c_recordset_line / $this->c_nb_line) - 1) * $this->c_nb_line);
					break;
				default:
					// Number
					$this->define_active_page($type);
					$this->define_limit_min(($type * $this->c_nb_line) - $this->c_nb_line);
					break;
			}

			$this->define_limit_max($this->c_nb_line);
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$this->prepare_query();
			$json_line = $this->generate_json_line();
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,true,true))."</content>";
			$xml .= "<total_page>".ceil($this->c_recordset_line / $this->c_nb_line)."</total_page>";
			$xml .= "<active_page>".$this->c_active_page."</active_page>";
			$xml .= "<json_line>".$this->protect_xml($json_line)."</json_line>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		
		/**
		 * Refresh the page
		 * @param json $p_selected_lines Selected lines
		 */
		public function refresh_page($p_selected_lines)
		{
			// Define the selected lines
			$this->define_selected_line($p_selected_lines);

			// Go to the first page   
			$this->define_active_page(1);
			$this->define_limit_min(0);
			
			$this->define_limit_max($this->c_nb_line);
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,false,true))."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		
		
		/**
		 * Define the selected lines
		 * @param json $p_selected_lines Selected lines
		 */
		private function define_selected_line($p_selected_lines)
		{
			// Transform JSON to Array
			$tab_selected_var = json_decode($p_selected_lines,true);
			if(is_array($tab_selected_var))
			{
				// Browse the selected lines
				foreach($tab_selected_var as $value_tab_selected_var)
				{
					/**==================================================================
					 * Generate the concatened key of the line
					 ====================================================================*/		
					$key_concat = '';
					foreach($value_tab_selected_var['key'] as $value)
					{
						$key_concat .= $value;
					}
					/*===================================================================*/		
					
					/**==================================================================
					 * Define if the line is selected or not
					 ====================================================================*/		
					if($value_tab_selected_var['selected'])
					{
						// Line selected
						$this->c_selected_lines['keys'][$key_concat] = $value_tab_selected_var['key'];
						$this->c_selected_lines['key_concat'][$key_concat] = $key_concat;
					}
					else
					{
						// Line unselected
						unset($this->c_selected_lines['keys'][$key_concat]);
						unset($this->c_selected_lines['key_concat'][$key_concat]);
					}
					/*===================================================================*/		
				}
			}
		}
		
		/**
		 * Move the column 
		 * @param integer $c_src Source ID of the column
		 * @param integer $c_dst Destination ID of the column
		 * @param json $p_selected_lines Selected lines
		 */
		public function move_column($c_src,$c_dst,$p_selected_lines)
		{
			// Define the selected lines
			$this->define_selected_line($p_selected_lines);
			
			// Move left to right
			if($c_dst > $c_src)
			{
				$c_dst = $c_dst - 1;
			}
			
			// Copy content of the column source and destination
			$temp_src = $this->c_columns[$c_src];
			$temp_dst = $this->c_columns[$c_dst];
			
			// Move each column between the source and destination
			if($c_src > $c_dst)
			{
				$i = $c_src; 
				while($i  > $c_dst){
					$this->c_columns[$i] = $this->c_columns[$i - 1];
					$i--;
				}
			}
			else
			{
				$i = $c_src; 
				while($i  < $c_dst){
					$this->c_columns[$i] = $this->c_columns[$i + 1];
					$i = $i + 1;
				}	
			}
			
			// Copy column source to destination
			$this->c_columns[$c_dst] = $temp_src;
			//$this->c_obj_graphic->c_columns = $this->c_columns;

			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/	
		 	$this->prepare_query();
		 	
			$json_line = $this->generate_json_line();
			$json = $this->generate_vimofy_json_param();
			
		 	// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,false,true))."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "<json_line>".$this->protect_xml($json_line)."</json_line>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		
		/**
		 * Change the order of a column
		 * @param integer $p_column ID of the coluln to order
		 * @param constant $p_order __ASC__,__DESC__ or __NONE__
		 * @param constant $mode __ADD__ or __NEW__
		 * @param json $p_selected_lines Selected lines
		 */
		public function change_order($p_column,$p_order,$mode,$p_selected_lines)
		{
			// Define the selected lines
			$this->define_selected_line($p_selected_lines);
			
			if($mode == __NEW__)
			{
				// New order, delete other clause
				$this->clear_all_order();
			}
			
			/**==================================================================
			 * Define the order priority
			 ====================================================================*/	
			if($this->c_columns[$p_column]['order_priority'] != false)
			{
				$priority = $this->c_columns[$p_column]['order_priority'];
			}
			else
			{
				$priority = $this->get_max_priority() + 1;
			}
			/*===================================================================*/	

			$this->define_order_column(null,$priority,$p_order,$p_column);		
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/	
			$this->prepare_query();
			$json_line = $this->generate_json_line();
			$json = $this->generate_json_column();

			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,false,true))."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "<json_line>".$this->protect_xml($json_line)."</json_line>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		
		/**
		 * Change the search mode on a column
		 * @param integer $p_column Id of the column
		 * @param constant $p_type_search __EXACT__ or __PERCENT__
		 * @param json $p_selected_lines Selected lines
		 */
		public function change_search_mode($p_column,$p_type_search,$p_selected_lines)
		{
			// Define the selected lines
			$this->define_selected_line($p_selected_lines);
			
			// Define the search mode
			$this->c_columns[$p_column]['search_mode'] = $p_type_search;
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,false,true))."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		
		/**
		 * Rapid search on a column, display the result in a float div.
		 * @param integer $column Id of the column
		 * @param string $txt Text to search
		 */
		public function vimofy_input_search_onkeyup($column,$txt,$p_selected_lines)
		{
			// Define the selected lines
			$this->define_selected_line($p_selected_lines);
			
			/**==================================================================
			 * Get Column filter
			 ====================================================================*/	
			$sql_filter = '';
			foreach($this->c_columns as $column_key => $column_value)
			{
				if(isset($column_value['filter']) && $column_key != $column)
				{
					foreach ($column_value['filter'] as $filter_key => $filter_value)
					{
						$sql_filter .= ' AND '.$this->get_quote_col($column_value['sql_as']).' '.$this->get_like($column_value['search_mode'].$this->protect_sql($filter_value['filter'],$this->link).$column_value['search_mode']);
					}
				}
			}
			/*===================================================================*/	
			
			/**==================================================================
			 * Browse the updated filter
			 ====================================================================*/				
			//$txt = $this->escape_special_char($txt);
			$post['filter'] = $txt;
			
			if($post['filter'] == '')
			{
				// No filter defined, clear the filter clause
				unset($this->c_columns[$column]['filter']['input']);
				unset($this->c_columns[$column]['taglov_possible']);
			}
			else
			{
				$this->c_columns[$column]['filter']['input'] = array('filter' => rawurldecode($post['filter']));
				
				if(isset($this->c_columns[$column]['lov']))
				{
					$sql = 'SELECT * FROM ('.$this->c_columns[$column]['lov']['sql'].') as ret WHERE '.$this->c_columns[$column]['lov']['col_return'].' LIKE "%'.$this->protect_sql(rawurldecode($post['filter']),$this->link).'%"';
					
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

					if($this->c_columns[$column]['quick_help'])
					{
						// Quick help : true
						if($this->rds_num_rows($this->resultat) == 1)
						{
							$this->c_columns[$column]['taglov_possible'] = true;
							
							while($row = $this->rds_fetch_array($this->resultat))
							{
								$this->c_columns[$column]['filter']['input']['taglov'] = $row;
							}
						}
						else
						{
							unset($this->c_columns[$column]['taglov_possible']);
						}
					}
					else
					{
						// Quick help : false
						
						// Search if the exact value exist
						$sql = 'SELECT * FROM ('.$this->c_columns[$column]['lov']['sql'].') as ret WHERE '.$this->c_columns[$column]['lov']['col_return'].' = "'.$this->protect_sql(rawurldecode($post['filter']),$this->link).'"';
						$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
						
						
						if(mysql_num_rows($this->resultat) > 0)
						{
							$this->c_columns[$column]['taglov_possible'] = true;
							
							while($row = $this->rds_fetch_array($this->resultat))
							{
								$this->c_columns[$column]['filter']['input']['taglov'] = $row;
							}
						}
						else
						{
							unset($this->c_columns[$column]['taglov_possible']);
						}
					}
				}
			}

			$this->check_column_lovable();
			
			if(isset($this->c_columns[$column]['filter']) && count($this->c_columns[$column]['filter']) == 0)
			{
				unset($this->c_columns[$column]['filter']);
			}
			/*===================================================================*/	

			if($txt != '')
			{
				/**==================================================================
				 * Prepare query
				 ====================================================================*/	
				if(!isset($this->c_columns[$column]['lov']))
				{
					// Count result
					$sql = 'SELECT COUNT(DISTINCT '.$this->get_quote_col($this->c_columns[$column]['sql_as']).') as total FROM ('.$this->c_query.' ) as deriv WHERE '.$this->get_quote_col($this->c_columns[$column]['sql_as']).' '.$this->get_like($this->c_columns[$column]['search_mode'].$this->protect_sql($this->escape_special_char($txt),$this->link).$this->c_columns[$column]['search_mode']).' '.$sql_filter;
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__, $this->link);
					$count = $this->rds_result($this->resultat,0, 'total');

					// Query result
					$sql = 'SELECT DISTINCT '.$this->get_quote_col($this->c_columns[$column]['sql_as']).' FROM ('.$this->c_query.' ) as deriv WHERE '.$this->get_quote_col($this->c_columns[$column]['sql_as']).' '.$this->get_like($this->c_columns[$column]['search_mode'].$this->protect_sql($this->escape_special_char($txt),$this->link).$this->c_columns[$column]['search_mode']).' '.$sql_filter.'ORDER BY 1 ASC LIMIT 6';
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}
				else
				{
					if(isset($this->c_columns[$column]['lov']['taglov_possible']) || !isset($this->c_columns[$column]['lov']['taglov']))
					{
						// Count result
						$sql = 'SELECT COUNT(DISTINCT '.$this->c_columns[$column]['lov']['col_return'].') as total FROM ('.$this->c_columns[$column]['lov']['sql'].') as deriv WHERE '.$this->get_quote_col($this->c_columns[$column]['lov']['col_return']).' '.$this->get_like($this->c_columns[$column]['search_mode'].$this->protect_sql($this->escape_special_char($txt),$this->link).$this->c_columns[$column]['search_mode']);
						$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__, $this->link);
						$count = $this->rds_result($this->resultat,0, 'total');

						// Query result
						$sql = 'SELECT DISTINCT '.$this->c_columns[$column]['lov']['col_return'].' as '.$this->c_columns[$column]['sql_as'].' FROM ('.$this->c_columns[$column]['lov']['sql'].') as deriv WHERE 1 = 1 AND '.$this->get_quote_col($this->c_columns[$column]['lov']['col_return']).' '.$this->get_like($this->c_columns[$column]['search_mode'].$this->protect_sql($this->escape_special_char($txt),$this->link).$this->c_columns[$column]['search_mode']).' ORDER BY 1 ASC LIMIT 6';
						$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					}
					else 
					{
						// Count result
						$sql = 'SELECT COUNT(DISTINCT '.$this->get_quote_col($this->c_columns[$column]['sql_as']).') as total FROM ('.$this->c_query.' ) as deriv WHERE '.$this->get_quote_col($this->c_columns[$column]['sql_as']).' '.$this->get_like($this->c_columns[$column]['search_mode'].$this->protect_sql($this->escape_special_char($txt),$this->link).$this->c_columns[$column]['search_mode']).' '.$sql_filter;
						$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__, $this->link);
						$count = $this->rds_result($this->resultat,0, 'total');

						// Query result
						$sql = 'SELECT DISTINCT '.$this->get_quote_col($this->c_columns[$column]['sql_as']).' FROM ('.$this->c_query.' ) as deriv WHERE '.$this->get_quote_col($this->c_columns[$column]['sql_as']).' '.$this->get_like($this->c_columns[$column]['search_mode'].$this->protect_sql($this->escape_special_char($txt),$this->link).$this->c_columns[$column]['search_mode']).' '.$sql_filter.'ORDER BY 1 ASC LIMIT 6';
						$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					}
				}
				/*===================================================================*/	

				/**==================================================================
				 * Prepare the result
				 ====================================================================*/	
				$html = '<div id="div_input_search_'.$this->c_id.'" class="__'.$this->c_theme.'_div_input_search"><table style="width:100%;" style="border-collapse:collapse;">';
				$i = 1;
				$motif = '#'.$this->protect_expreg($txt).'#i';

				while($row = $this->rds_fetch_array($this->resultat))
				{
					if($this->c_columns[$column]['data_type'] == __BBCODE__)
					{
						$result = $this->clearBBCode($row[$this->c_columns[$column]['sql_as']]);
					}
					else
					{
						$result = $this->clearHTML($row[$this->c_columns[$column]['sql_as']]);
						$result = $this->replace_chevrons($result,false);
					}
					$html .= '<tr style="margin:0;padding:0;border-collapse:collapse;" id="'.$this->c_id.'_rapid_search_l'.$i.'" onclick="vimofy_input_result_click(\''.$this->c_id.'\','.$column.','.$i.',\''.$this->protect_js_txt($result).'\');"><td style="margin:0;padding:0;"><div class="__'.$this->c_theme.'_column_header_input_result" id="vimofy_input_result_'.$i.'_'.$this->c_id.'">'.preg_replace($motif,'<b>$0</b>',$result).'</div></td></tr>';
					$i = $i + 1;
				}

				if($count > 6)
				{
					$qtt = $count - 6;
					($count == 1) ? $lib = $qtt.' '.$this->lib(37) : $lib = $qtt.' '.$this->lib(38);
					
					$html .= '<tr style="margin:0;padding:0;border-collapse:collapse;"><td class="__'.$this->c_theme.'_column_header_menu_line_sep_top"></td></tr>';
					$html .= '<tr style="margin:0;padding:0;border-collapse:collapse;"><td style="margin:0;padding:0;"><div style="font-family: tahoma, arial, helvetica, sans-serif;font-size: 0.7em;">'.$lib.' ...</div></td></tr>';
				}

				if($i == 1) 
				{
					$html .= '<tr style="margin:0;padding:0;border-collapse:collapse;" id="'.$this->c_id.'_rapid_search_l'.$i.'"><td style="margin:0;padding:0;"><div class="__'.$this->c_theme.'_column_header_input_result" id="vimofy_input_result_'.$i.'_'.$this->c_id.'">'.$this->lib(43).'</div></td></tr>';
				}

				$html.= '</table></div>';
			}
			else
			{
				$html = '';
			}

			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$json = $this->generate_vimofy_json_param(true,false);

			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "<content>".$this->protect_xml($html)."</content>";
			$xml .= "</vimofy>";

			echo $xml;
			/*===================================================================*/

			/*===================================================================*/	
		}

		private function protect_js_txt($p_txt)
		{
			$p_txt = str_replace('\\','\\\\',$p_txt);
			$p_txt = str_replace("'","\'",$p_txt);

			return $p_txt;
		}

		public function toggle_column($p_column,$p_selected_lines)
		{
			// Set the selected lines to edit
			$this->define_selected_line($p_selected_lines);

			$this->c_columns[$p_column]['display'] == __DISPLAY__ ? $this->c_columns[$p_column]['display'] = __HIDE__ : $this->c_columns[$p_column]['display'] = __DISPLAY__; 

			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();

			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,false,true))."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";

			echo $xml;
			/*===================================================================*/	
		}

		private function protect_expreg($p_txt)
		{
			// ^ . [ ] $ ( ) * + ? | { } \
			$p_txt = str_replace('\\', '\\\\', $p_txt);
			$p_txt = str_replace('^', '\^', $p_txt);
			$p_txt = str_replace('.', '\.', $p_txt);
			$p_txt = str_replace('[', '\[', $p_txt);
			$p_txt = str_replace(']', '\]', $p_txt);
			$p_txt = str_replace('(', '\(', $p_txt);
			$p_txt = str_replace(')', '\)', $p_txt);
			$p_txt = str_replace('$', '\$', $p_txt);
			$p_txt = str_replace('*', '\*', $p_txt);
			$p_txt = str_replace('+', '\+', $p_txt);
			$p_txt = str_replace('?', '\?', $p_txt);
			$p_txt = str_replace('|', '\|', $p_txt);
			$p_txt = str_replace('{', '\{', $p_txt);
			$p_txt = str_replace('}', '\}', $p_txt);

			return $p_txt;
		}
			
		private function escape_special_char($p_txt)
		{
			$p_txt = str_replace('_', '\_', $p_txt);

			return $p_txt;
		}

		private function reset_filters()
		{
			/**==================================================================
			 * Reset filter on all columns
			 ====================================================================*/		
			foreach ($this->c_columns as $key_column => &$val_column)
			{
				if(isset($val_column['filter']))
				{
					unset($val_column['filter']);
				}
				unset($this->c_columns[$key_column]['taglov_possible']);
			}
			$this->check_column_lovable();
			/*===================================================================*/
		}
		
		public function reset_vimofy()
		{
			/**==================================================================
			 * Reset filter on all columns
			 ====================================================================*/		
			$this->reset_filters();
			/*===================================================================*/
			
			/**==================================================================
			 * Reset selected lines
			 ====================================================================*/	
			$this->c_selected_lines = false;
			/*===================================================================*/
			
			$this->change_nb_line($this->c_default_nb_line);
			
			/**==================================================================
			 * Define active page to 1
			 ====================================================================*/		
			$this->define_active_page(1);
			$this->define_limit_min(0);
			/*===================================================================*/	
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$this->prepare_query();
			$json = $this->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,false,true))."</content>";
			if($this->c_edit_mode == __EDIT_MODE__)
			{
				// Define the Vimofy mode
				$this->c_edit_mode = __DISPLAY_MODE__;
				$xml .= '<toolbar>'.$this->protect_xml($this->c_obj_graphic->generate_toolbar(false)).'</toolbar>';
				$xml .= "<edit_mode>true</edit_mode>";
			}
			else
			{
				$xml .= "<edit_mode>false</edit_mode>";
			}
			
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}


		/**
		 * Define a filter
		 * @param string $p_name name of the filter
		 */
		public function save_filter($p_name)
		{
			/**==================================================================
			 * Test if a filter already exist
			 ====================================================================*/
			$s_sql = 'SELECT 1 
					  FROM vimofy_filters
					  WHERE '.$this->get_quote_col('name').' = '.$this->get_quote_string($p_name).' 
					  AND '.$this->get_quote_col('vimofy_id').' = '.$this->get_quote_string($this->c_id);

			$this->exec_sql($s_sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

			if($this->rds_num_rows($this->resultat) > 0)
			{
				// XML return	
				header("Content-type: text/xml");
				$xml = "<?xml version='1.0' encoding='UTF8'?>";
				$xml .= "<vimofy>";
				$xml .= "<error>true</error>";
				$xml .= "<title>".$this->protect_xml($this->lib(63))."</title>";
				$xml .= "<message>".$this->protect_xml($this->lib(62))."</message>";
				$xml .= "</vimofy>";

				echo $xml;
			}
			else 
			{
			/*===================================================================*/	

				/**==================================================================
				 * Initialisation of method variable
				 ====================================================================*/
				$s_sql = array();
				/*===================================================================*/	

				foreach($this->c_columns as $col_key => &$val_key)
				{
					/**==================================================================
					 * Order clause (ORD - order)
					 ====================================================================*/
					if(is_numeric($val_key['order_priority']))
					{
						$s_sql[] = $this->prepare_query_insert_filter($p_name,$val_key['sql_as'],'ORD',$val_key['order_priority'],$val_key['order_by'],$col_key);
					}
					/*===================================================================*/	

					/**==================================================================
					 * Column organisation (CPS - Column position)
					 ====================================================================*/
					//if($val_key['original_order'] != $col_key)
					//{
						$s_sql[] = $this->prepare_query_insert_filter($p_name,$val_key['sql_as'],'CPS',$col_key);
					//}
					/*===================================================================*/	

					/**==================================================================
					 * Column filter (QSC Quick search)
					 ====================================================================*/
					if(isset($val_key['filter']['input']))
					{
						$s_sql[] = $this->prepare_query_insert_filter($p_name,$val_key['sql_as'],'QSC',$col_key,$val_key['filter']['input']['filter']);
					}
					/*===================================================================*/	
					
					/**==================================================================
					 * Column display (DMD - Display mode)
					 ====================================================================*/
					if($val_key['display'] == __HIDE__)
					{
						$s_sql[] = $this->prepare_query_insert_filter($p_name,$val_key['sql_as'],'DMD',$col_key,$val_key['display']);
					}
					/*===================================================================*/	
					
					/**==================================================================
					 * Column Search mode (SMD - Search Mode)
					 ====================================================================*/
					$s_sql[] = $this->prepare_query_insert_filter($p_name,$val_key['sql_as'],'SMD',$col_key,$val_key['search_mode']);
					/*===================================================================*/	
					
					/**==================================================================
					 * Column Alignment (ALI - Alignment)
					 ====================================================================*/
					$s_sql[] = $this->prepare_query_insert_filter($p_name,$val_key['sql_as'],'ALI',$col_key,$val_key['alignment']);
					/*===================================================================*/	
					
					/**==================================================================
					 * Column size (SIZ - Size)
					 ====================================================================*/
					
					/*===================================================================*/	
					
					/**==================================================================
					 * Column filter (IEQ - Include equal)
					 ====================================================================*/
					
					/*===================================================================*/	
									
					/**==================================================================
					 * Column filter (IBT - Include between)
					 ====================================================================*/
					
					/*===================================================================*/	
									
					/**==================================================================
					 * Column filter (EEQ - Exclude equal)
					 ====================================================================*/
					
					/*===================================================================*/	
									
					/**==================================================================
					 * Column filter (EBT - Exclude between)
					 ====================================================================*/
					
					/*===================================================================*/	
				}
				/*===================================================================*/	
	
				/**==================================================================
				 * Get the filter definition to create the query
				 ====================================================================*/
				foreach($s_sql as $value) 
				{
					$this->exec_sql($value,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}
				/*===================================================================*/	
				
				// XML return	
				header("Content-type: text/xml");
				$xml = "<?xml version='1.0' encoding='UTF8'?>";
				$xml .= "<vimofy>";
				$xml .= "<error>false</error>";
				$xml .= "</vimofy>";
				
				echo $xml;
			}
		}
		
		public function vimofy_internal_adv_filter()
		{
			$id_child = $this->c_id.'_child';
			$_SESSION['vimofy'][$this->c_ssid][$id_child] = new vimofy($id_child, $this->c_ssid, $this->c_db_engine,$this->c_ident,$this->c_dir_obj,'../../../');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_mode(__CMOD__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_parent($this->c_id);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_readonly(__R__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_title($this->lib(41));
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_query('SELECT 1 as id,2 as version UNION SELECT 2,3 UNION SELECT 4,5 UNION SELECT 6,7 UNION SELECT 8,9 UNION SELECT 10,11 UNION SELECT 12,13');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column(1, 'id', 'id',__TEXT__, __WRAP__, __CENTER__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column(2, 'version', 'version',__TEXT__, __WRAP__, __CENTER__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_theme($this->c_theme);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_c_color_mask($this->c_color_mask);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->new_graphic_vimofy();

			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->prepare_query();
			$json = $_SESSION['vimofy'][$this->c_ssid][$id_child]->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($_SESSION['vimofy'][$this->c_ssid][$id_child]->generate_vimofy())."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}

		/**
		 * Display a List Of Value on a column 
		 * @param integer $column Id of the column 
		 */
		public function vimofy_lov($column)
		{
			//$this->c_type_internal_vimofy = __POSSIBLE_VALUES__;
			
			$id_child = $this->c_id.'_child';

			// Create an instance of a Vimofy
			$_SESSION['vimofy'][$this->c_ssid][$id_child] = new vimofy($id_child,$this->c_ssid,$this->c_db_engine,$this->c_ident,$this->c_dir_obj,'../../../',__POSSIBLE_VALUES__,$this->c_software_version);

			if(isset($this->c_columns[$column]['lov']) && $this->c_columns[$column]['lov'])
			{
				// A lov was defined
				$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_col_return($this->c_columns[$column]['lov']['col_return']);
				$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_title($this->c_columns[$column]['lov']['title']);

				if(isset($this->c_columns[$column]['lov']['taglov']))
				{
					$sql = $this->c_columns[$column]['lov']['sql'];
					
					foreach($this->c_columns[$column]['lov']['taglov'] as $value)
					{
						$sql = str_replace('||TAGLOV_'.$value['column'].'**'.$value['column_return'].'||',$this->c_columns[$this->get_id_column($value['column'])]['filter']['input']['taglov'][$value['column_return']],$sql);
					}
					$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_query($sql);
				}
				else
				{
					$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_query($this->c_columns[$column]['lov']['sql']);
				}
				
				foreach($this->c_columns[$column]['lov']['columns'] as $key => $lov_col)
				{
					$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column($key,$lov_col['name'],$lov_col['data_type'],$lov_col['nowrap'], $lov_col['alignment']);
					
					if(isset($lov_col['order']))
					{
						$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_order_column($key,$lov_col['order']['order_priority'],$lov_col['order']['order_by']);
					}
				}
			}
			else
			{
				// No LOV defined
				$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_title($this->lib(44));
				$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_query('SELECT DISTINCT '.$this->c_columns[$column]['sql_as'].' from('.$this->c_query.') der');
				$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column($this->c_columns[$column]['sql_as'], $this->c_columns[$column]['name'],$this->c_columns[$column]['data_type'], __WRAP__, __LEFT__);
				if($this->c_columns[$column]['data_type'] == __DATE__)
				{
					$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column_date_format($this->c_columns[$column]['sql_as'],$this->c_columns[$column]['date_format']);
				}
				$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_order_column($this->c_columns[$column]['sql_as'],1,__ASC__);
			}
			
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_parent($this->c_id,$column);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_mode(__CMOD__,__SIMPLE__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_readonly(__R__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_size(100,'%',100,'%');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_theme($this->c_theme);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_c_color_mask($this->c_color_mask);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_nb_line(12);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_page_selection_display(false,true);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->new_graphic_vimofy();
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_sep_col_row($this->c_cols_sep_display,$this->c_rows_sep_display);
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			// Prepare the query
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->prepare_query();
			
			$html = '<div style="float:right;"><table style="margin:0;padding:0;border-collapse:collapse;"><tr><td style="margin:0;padding:0;"><div onclick="vimofy_child_cancel(\''.$this->c_id.'\','.$column.');" class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_cancel hover" '.$this->hover_out_lib(45,45,'_child').' style="margin-right:5px;"></div></td><td style="margin:0;padding:0;"></td></tr></table></div>';

			$json = $_SESSION['vimofy'][$this->c_ssid][$id_child]->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($_SESSION['vimofy'][$this->c_ssid][$id_child]->generate_vimofy().$html)."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/
		}
		
		/**
		 * Generate the load filter lov
		 * @param integer $column Id of the column 
		 */
		public function vimofy_load_filter_lov()
		{
			$column = 1;
			$id_child = $this->c_id.'_child';
			// Create an instance of a Vimofy
			$_SESSION['vimofy'][$this->c_ssid][$id_child] = new vimofy($id_child, $this->c_ssid, $this->c_db_engine, $this->c_ident,$this->c_dir_obj,'../../../',__LOAD_FILTER__,$this->c_software_version);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_title($this->lib(59).' ('.$this->c_param_adv_filter.')');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_query('SELECT DISTINCT `name`,`date`,`vimofy_id` FROM vimofy_filters WHERE vimofy_id = "'.$this->c_id.'"');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column('name',$this->lib(60),__TEXT__, __WRAP__, __LEFT__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column('date',$this->lib(61),__TEXT__, __WRAP__, __LEFT__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_parent($this->c_id,$column);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_mode(__CMOD__,__MULTIPLE__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_readonly(__RW__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_theme('blue');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_size(100,'%',100,'%');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_theme($this->c_theme);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_c_color_mask($this->c_color_mask);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_page_selection_display(false,true);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_nb_line(17);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->new_graphic_vimofy();
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_sep_col_row($this->c_cols_sep_display,$this->c_rows_sep_display);
			
			/**==================================================================
			 * UPDATE/INSERT
			 ====================================================================*/		
			// Update table
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_update_table('vimofy_filters');
			
			// Columns attribut
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_rw_flag_column('name',__REQUIRED__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_rw_flag_column('date',__REQUIRED__);
			
			// Table key
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_key(Array('name','vimofy_id'));
			/*===================================================================*/	
	
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			// Prepare the query
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->prepare_query();
			
			$html = '<div style="float:right;"><table style="margin:0;padding:0;border-collapse:collapse;"><tr><td style="margin:0;padding:0;"><div onclick="vimofy_child_cancel(\''.$this->c_id.'\','.$column.');" class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_cancel hover" '.$this->hover_out_lib(45,45,'_child').' style="margin-right:5px;"></div></td><td style="margin:0;padding:0;"></td></tr></table></div>';

			$json = $_SESSION['vimofy'][$this->c_ssid][$id_child]->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($_SESSION['vimofy'][$this->c_ssid][$id_child]->generate_vimofy().$html)."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/
		}
		
		/**
		 * Generate the hide / display column menu
		 * @param integer $column Id of the column 
		 */
		public function vimofy_hide_display_col_lov()
		{
			$column = 1;
			$id_child = $this->c_id.'_child';
			// Create an instance of a Vimofy
			$_SESSION['vimofy'][$this->c_ssid][$id_child] = new vimofy($id_child, $this->c_ssid, $this->c_db_engine, $this->c_ident,$this->c_dir_obj,'/vimofy/',__LOAD_FILTER__,$this->c_software_version);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_title($this->lib(64));
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_query('SELECT DISTINCT name,date FROM vimofy_filters WHERE vimofy_id = "'.$this->c_id.'"');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column('name',$this->lib(60),__TEXT__, __WRAP__, __LEFT__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_column('date',$this->lib(61),__TEXT__, __WRAP__, __LEFT__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_parent($this->c_id,$column);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_mode(__CMOD__,__SIMPLE__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_readonly(__R__);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_theme('blue');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_size(100,'%',100,'%');
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_theme($this->c_theme);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_c_color_mask($this->c_color_mask);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->define_nb_line(17);
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->new_graphic_vimofy();
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/		
			// Prepare the query
			$_SESSION['vimofy'][$this->c_ssid][$id_child]->prepare_query();
			
			$html = '<div style="float:right;"><table style="margin:0;padding:0;border-collapse:collapse;"><tr><td style="margin:0;padding:0;"><div onclick="vimofy_child_cancel(\''.$this->c_id.'\','.$column.');" class="__'.$this->c_theme.'_ico __'.$this->c_theme.'_ico_cancel hover" '.$this->hover_out_lib(45,45,'_child').' style="margin-right:5px;"></div></td><td style="margin:0;padding:0;"></td></tr></table></div>';

			$json = $_SESSION['vimofy'][$this->c_ssid][$id_child]->generate_vimofy_json_param();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($_SESSION['vimofy'][$this->c_ssid][$id_child]->generate_vimofy().$html)."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/
		}
		
		public function vimofy_load_filter($filter_name)
		{
			$this->get_and_set_filter($filter_name);
			
			/**==================================================================
			 * Execute the query and display the elements
			 ====================================================================*/	
			$this->prepare_query();
			$json_line = $this->generate_json_line();
			$json = $this->generate_json_column();
			
			// XML return	
			header("Content-type: text/xml");
			$xml = "<?xml version='1.0' encoding='UTF8'?>";
			$xml .= "<vimofy>";
			$xml .= "<content>".$this->protect_xml($this->c_obj_graphic->draw_vimofy($this->resultat,false,true))."</content>";
			$xml .= "<json>".$this->protect_xml($json)."</json>";
			$xml .= "<json_line>".$this->protect_xml($json_line)."</json_line>";
			$xml .= "</vimofy>";
			
			echo $xml;
			/*===================================================================*/	
		}
		/**==================================================================
		 * Getter
		 ====================================================================*/
		public function get_theme()
		{
			return $this->c_theme;
		}
		/*===================================================================*/
		
		/**==================================================================
		 * Internal methods
		 ====================================================================*/
		private function prepare_query_insert_filter($p_name,$p_id_column,$p_type,$p_val1,$p_val2 = '',$p_val3 = '')
		{
			if($p_val1 == '')
			{
				$p_val2 = 'NULL';
				$p_val3 = 'NULL';
			}
			else
			{
				$p_val2 = '"'.$p_val2.'"';
				$p_val3 = '"'.$p_val3.'"';
			}
			
			return 'INSERT INTO vimofy_filters(`name`, `vimofy_id`, `id_column`, `type`, `val1`, `val2`, `val3`) VALUES ("'.$p_name.'","'.$this->c_id.'","'.$p_id_column.'","'.$p_type.'","'.$p_val1.'",'.$p_val2.','.$p_val3.')';
		}
		
		/**
		 * Generate an onmouseover & onmouseout event for help
		 * @param decimal $id_lib Id of the text
		 * @param decimal $id_help Id of the help
		 */
		private function hover_out_lib($id_lib,$id_help,$child = '')
		{
			return 'onmouseout="vimofy_lib_out(\''.$this->c_id.$child.'\');" onmouseover="vimofy_lib_hover('.$id_lib.','.$id_help.',\''.$this->c_id.$child.'\');"';
		}
		
		
		/**
		 * @method protect for xml content
		 * @param string $txt
		 * @return string protected content
		 * @access private
		 */
		private function protect_xml($txt)
		{
			$txt = rawurlencode($txt);
		
			return $txt;
		}
		
		/**
		 * Return the max colum priority
		 */
		private function get_max_priority()
		{
			$priority = 0;
			foreach ($this->c_columns as $value)
			{
				if($value['order_priority'] > $priority)
				{
					$priority = $value['order_priority'];
				}
			}
			return $priority;
		}
		
		private function convertBBCodetoHTML($txt)
		{
			$remplacement=true;
			while($remplacement)
			{
				$remplacement=false;
				$oldtxt=$txt;
				$txt = preg_replace('`\[BBTITRE\]([^\[]*)\[/BBTITRE\]`i','<b><u><font class="bbtitre">\\1</font></u></b>',$txt);
				$txt = preg_replace('`\[EMAIL\]([^\[]*)\[/EMAIL\]`i','<a href="mailto:\\1">\\1</a>',$txt);
				$txt = preg_replace('`\[b\]([^\[]*)\[/b\]`i','<b>\\1</b>',$txt);
				$txt = preg_replace('`\[i\]([^\[]*)\[/i\]`i','<i>\\1</i>',$txt);
				$txt = preg_replace('`\[u\]([^\[]*)\[/u\]`i','<u>\\1</u>',$txt);
				$txt = preg_replace('`\[s\]([^\[]*)\[/s\]`i','<s>\\1</s>',$txt);
				$txt = preg_replace('`\[br\]`','<br>',$txt);
				$txt = preg_replace('`\[center\]([^\[]*)\[/center\]`','<div style="text-align: center;">\\1</div>',$txt);
				$txt = preg_replace('`\[left\]([^\[]*)\[/left\]`i','<div style="text-align: left;">\\1</div>',$txt);
				$txt = preg_replace('`\[right\]([^\[]*)\[/right\]`i','<div style="text-align: right;">\\1</div>',$txt);
				$txt = preg_replace('`\[img\]([^\[]*)\[/img\]`i','<img src="\\1" />',$txt);
				$txt = preg_replace('`\[color=([^[]*)\]([^[]*)\[/color\]`i','<font color="\\1">\\2</font>',$txt);
				$txt = preg_replace('`\[bg=([^[]*)\]([^[]*)\[/bg\]`i','<font style="background-color: \\1;">\\2</font>',$txt);
				$txt = preg_replace('`\[size=([^[]*)\]([^[]*)\[/size\]`i','<font size="\\1">\\2</font>',$txt);
				$txt = preg_replace('`\[font=([^[]*)\]([^[]*)\[/font\]`i','<font face="\\1">\\2</font>',$txt);
				$txt = preg_replace('`\[url\]([^\[]*)\[/url\]`i','<a target="_blank" href="\\1">\\1</a>',$txt);
				$txt = preg_replace('`\[url=([^[]*)\]([^[]*)\[/url\]`i','<a target="_blank" href="\\1">\\2</a>',$txt);
				
				if ($oldtxt<>$txt)
				{
					$remplacement=true;
				}
			}
			return $txt;
			
		}
		
		private function clearBBCode($txt)
		{
			$remplacement=true;
			while($remplacement)
			{
				$remplacement=false;
				$oldtxt=$txt;
				$txt = preg_replace('`\[BBTITRE\]([^\[]*)\[/BBTITRE\]`i','\\1',$txt);
				$txt = preg_replace('`\[EMAIL\]([^\[]*)\[/EMAIL\]`i','\\1',$txt);
				$txt = preg_replace('`\[b\]([^\[]*)\[/b\]`i','\\1',$txt);
				$txt = preg_replace('`\[i\]([^\[]*)\[/i\]`i','\\1',$txt);
				$txt = preg_replace('`\[u\]([^\[]*)\[/u\]`i','\\1',$txt);
				$txt = preg_replace('`\[s\]([^\[]*)\[/s\]`i','\\1',$txt);
				$txt = preg_replace('`\[br\]`','',$txt);
				$txt = preg_replace('`\[center\]([^\[]*)\[/center\]`','\\1',$txt);
				$txt = preg_replace('`\[left\]([^\[]*)\[/left\]`i','\\1',$txt);
				$txt = preg_replace('`\[right\]([^\[]*)\[/right\]`i','\\1',$txt);
				$txt = preg_replace('`\[img\]([^\[]*)\[/img\]`i','\\1',$txt);
				$txt = preg_replace('`\[color=([^[]*)\]([^[]*)\[/color\]`i','\\2',$txt);
				$txt = preg_replace('`\[bg=([^[]*)\]([^[]*)\[/bg\]`i','\\2',$txt);
				$txt = preg_replace('`\[size=([^[]*)\]([^[]*)\[/size\]`i','\\2',$txt);
				$txt = preg_replace('`\[font=([^[]*)\]([^[]*)\[/font\]`i','\\2',$txt);
				$txt = preg_replace('`\[url\]([^\[]*)\[/url\]`i','\\2',$txt);
				$txt = preg_replace('`\[url=([^[]*)\]([^[]*)\[/url\]`i','\\2',$txt);
				
				if ($oldtxt<>$txt)
				{
					$remplacement=true;
				}
			}
			return $txt;
			
		}
		
		private function clearHTML($txt)
		{
			$remplacement=true;
			while($remplacement)
			{
				$remplacement=false;
				$oldtxt=$txt;
				$txt = preg_replace('`<span([^>]*)>`i','',$txt);
				$txt = preg_replace('`</span>`i','',$txt);
				$txt = preg_replace('`<a([^>]*)>`i','',$txt);
				$txt = preg_replace('`</a>`i','',$txt);
				if ($oldtxt<>$txt)
				{
					$remplacement=true;
				}
			}
			return $txt;
		}
		
		private function lib($id)
		{
			return $_SESSION['vimofy'][$this->c_ssid]['lib'][$id];
		}
		
		private function replace_chevrons($p_txt,$p_to_entity = true)
		{
			if($p_to_entity)
			{
				$p_txt = str_replace('&','&amp;',$p_txt);
				$p_txt = str_replace('>','&gt;',$p_txt);
				$p_txt = str_replace('<','&lt;',$p_txt);
			}
			else
			{
				$p_txt = str_replace('&','&amp;',$p_txt);
				$p_txt = str_replace('&gt;','>',$p_txt);
				$p_txt = str_replace('&lt;','<',$p_txt);	
			}
			
			return $p_txt;
		}
		
		/*===================================================================*/
		
		public function debug()
		{
			echo '<pre>';
			print_r($this->c_columns);
			echo '</pre>';
		}
	}
?>