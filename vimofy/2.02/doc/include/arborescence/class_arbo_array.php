<?php

	class class_arbo_array
	{
		
		/**
		 * Attributs de la classe arborescence
		 */
		private $link;					// Connexion à la base de données
		private $html_result;			// Contient le code html de l'arborescence.
		private $code_offset;			// Décalage à droite du code source
		private $internal_id;			// Identifiant tableau pour JS
		private $branch_id;				// Numéro de branche de l'arbre
		/**
		 * 
		 */
		/**
		 * class constructor of tree
		 * $version : Mandatory Current version
		 */
		public function __construct($version,$identifiant)
		{
			
			// Constantes
			define ("__retour_chariot", chr(10));
			define ("__tabulation", chr(9));
			define ("__current_version", $version);
			
			// Connexion à la base de données
			$this->link = mysql_connect('localhost','root','toto');
			mysql_select_db('galerie_photo',$this->link);
		
			// Reset properties
			$this->html_result = '';					
			$this->code_offset = '';
			$this->internal_id = $identifiant;
			$this->branch_id = 0;
		}
		/**
		 * Display tree
		 * $nb_tab : offset for source code
		 */
		public function draw_tree($nb_tab)
		{
			// Need a numeric, if not, force zero
			if(!is_numeric($nb_tab) or $nb_tab == '') $nb_tab = 0;
			
			// Compute offset code position
			while ($nb_tab > 0) {
				$this->code_offset = $this->code_offset.chr(9);
				$nb_tab = $nb_tab - 1;
			}
			
			// Call build function
			$this->build_tree();
			
			// Return result;
			return $this->html_result;	
		}
		
		/**
		 * Build tree and load result into html_result
		 */
		private function build_tree()
		{
			// Start Table
			$this->html_result = __retour_chariot.$this->code_offset.'<table class="tableau_arbre">'.__retour_chariot;
			
			$this->offset_push();
						
			if ($this->get_any_child(0)) 
				$this->scan_level(0,true,'','',0,'');
			else 
				$this->scan_level(0,false,'','',0,'');	

			// End Table
			$this->html_result = $this->html_result.$this->code_offset.'</table>'.__retour_chariot.__retour_chariot;
		}
		
		/**
		 * Parcours la branche ayant comme parent $id
		 * @param unknown_type $id
		 * @param unknown_type $is_parent
		 * @param unknown_type $value_parent
		 */
		private function scan_level($id,$is_parent,$id_quick,$id_empty,$value_parent,$description_parent)
		{

			if ($is_parent) // Parent's => More deeper in tree
			{ 
				
				$this->html_result .= '
				<tr id="'.$this->internal_id.'_'.$id.'">
					<td class="_cell_dec"
						onclick="javascript:expand_reduce(\''.$this->internal_id.'_EXPAND_'.$id.'\',\'\');"
						>
						<img id="'.$this->internal_id.'_EXPAND_'.$id.'_IMG" src="images/reduce.png">
					</td>
					<td class="_cell_parent">
						<span 	class="line" 
								id="'.$id_quick.'" 
								onmousedown="javascript:mousedown(this.id,\''.$value_parent.'\');"
								OnContextMenu="return false;"
								onmouseup="javascript:mouseup(this.id);"
								onmouseover="javascript:mouseover(this.id,true);"
								onmouseout="javascript:mouseout(this.id,true);"
								>
								('.$id.') : '.$value_parent.'
						</span>
						<span id="'.$this->internal_id.'_EXPAND_'.$id.'" style="display: bloc;">
							<table class="tableau_arbre">
								<tr class="_row_arbre">
									<td class="_cell_empty" 
										id="'.$id_empty.'"
										onmouseover="javascript:mouseover(this.id,false);" 
										onmouseout="javascript:mouseout(this.id,false);"		
										onmouseup="javascript:mouseup(this.id);"				
										colspan=2
										>
										&nbsp;
									</td>
								</tr>
							';
			}
			
			/****************************************************
			* Sort and browse all nodes with id parent = $id
			****************************************************/
			$sql = 'SELECT `id`,`parent`,`order`,`description`,`value`,
						CONCAT(\''.$this->internal_id.'\',\'_I_\',`id`,\'_Order_\',`order`) as MyItem,
						CONCAT(\''.$this->internal_id.'\',\'_P_\',`id`,\'_Order_\',`order`) as MyParent,
						CONCAT(\''.$this->internal_id.'\',\'_E_\',`id`,\'_Order_-1\') as MyEmpty 
			   		FROM `configuration` 
			   		WHERE 1 = 1
			   		AND `parent` = '.$id.' 
			   		AND `version` = '.__current_version.'
			   		ORDER BY `order` ASC';

			$resultat = mysql_query($sql,$this->link);
			 			
			while($row = mysql_fetch_array($resultat, MYSQL_ASSOC)) 
			{
				if ($this->get_any_child($row['id'])) 
				{ 	// A child exist
					// Deeper in tree, Call itself
					$this->scan_level($row['id'], true,$row['MyParent'],$row['MyEmpty'],$row['value'],$row['description']);
				}
				else 
				{ // No more child
					// Just build a solitaire item
					
					$this->html_result .= '
					<tr class="_row_arbre" id="'.$this->internal_id.'_'.$row['id'].'">
						<td class="_cell_dec">
							<img src="images/page.png"/>
						</td>
						<td class="_cell_solitaire">
							<span 	class="line"
									id="'.$row['MyItem'].'" 
									OnContextMenu="return false;" 
									onmouseover="javascript:mouseover(this.id,true);" 
									onmouseout="javascript:mouseout(this.id,true);"
									onmouseup="javascript:mouseup(this.id);"
									onmousedown="javascript:mousedown(this.id,\''.$row['value'].'\');"
									>
							('.$row['id'].') : '.$row['value'].'
							</span>
						</td>
					</tr>
					<tr class="_row_arbre">
						<td class="_cell_empty" 
							id="'.$this->internal_id.'_E_'.$row['parent'].'_Order_'.$row['order'].'"
							onmouseover="javascript:mouseover(this.id,false);" 
							onmouseout="javascript:mouseout(this.id,false);"		
							onmouseup="javascript:mouseup(this.id);"				
							colspan=2
							>
							&nbsp;
						</td>
					</tr>
					';
				}
				
			}					
			
			//$this->offset_pull();
			
			if ($is_parent) { // End bloc for all children of $id's parent
				$this->html_result .= '</table></span></td></tr>';
			}
		}
		
		/**
		 * Remove 1 level
		 */
		private function offset_pull() {
			$this->code_offset= substr($this->code_offset,0,-1);	
		}

		/**
		 * Add 1 level
		 */
		private function offset_push() {
			$this->code_offset = $this->code_offset.__tabulation;	
		}
		
		/**
		 * Permet de savoir si l'id possède au moins 1 enfant
		 * Si il existe au moins 1 enfant : Retour = 1 sinon Retour = false
		 * @param $id
		 */
		private function get_any_child($id)
		{
			$sql_enfants = 'SELECT 1 FROM `configuration` `level_n`,`configuration` `level_n+1`
							WHERE 1 = 1
							AND `level_n`.`id` = `level_n+1`.`parent`
							AND `level_n`.`version` = `level_n+1`.`version`
							AND `level_n`.`version` = '.__current_version.'
							AND `level_n`.`id` = '.$id.' LIMIT 1';
			
			$resultat_enfants = mysql_query($sql_enfants,$this->link);
						
			return mysql_num_rows($resultat_enfants);		
		}
	}
	// Fin de la classe

?>