var __FOOTER_LEFT__ = '__FOOTER_LEFT__';
var __FOOTER_RIGHT__ = '__FOOTER_RIGHT__';
var __COUNTER_SESSION__ = '__COUNTER_SESSION__';


function iknow_footer(p_dir_object,p_file_presence,p_file_presence_param,p_interval_presence) 
{ 	
	/**
	 * Define attribut
	 */
	
	this.left_element = Array();
	this.left_element_key = 0;
	this.right_element = Array();
	this.right_element_key = 0;
	this.dir_object = p_dir_object;
	this.obj_session = null;
	
	
	if(typeof(p_file_presence) == 'undefined')
	{
		this.file_presence = '';
		this.file_presence_param = '';
		this.interval_presence = 60000;
	}
	else
	{
		this.file_presence = p_file_presence;
		this.file_presence_param = p_file_presence_param;
		this.interval_presence = p_interval_presence;
	}
	this.ssid = '';
	
	this.add_element = function(p_innerHTML,p_pos)
	{
		if(p_innerHTML == __COUNTER_SESSION__)
		{
			this.generate_counter_session(p_pos);	
			footer.obj_session.counter_session_update();
		}
		else
		{
			if(p_pos == __FOOTER_LEFT__)
			{
				this.left_element[this.left_element_key] = p_innerHTML;
				this.left_element_key++;
			}
			else
			{
				this.right_element[this.right_element_key] = p_innerHTML;
				this.right_element_key++;		
			}
		}
	};
	
	
	/**
	 * Generate the footer
	 */
	this.generate = function()
	{
		var html = '';
		
		if(this.left_element.length > 0)
		{
			html += '<div class="footer_left">';
		
			for(var int = 0; int < this.left_element.length; int++) 
			{
				if(int == 0)
				{
					var class_css = 'left_first_el';
				}
				else if(int == this.left_element.length - 1)
				{
					var class_css = 'left_last_el';
				}
				else
				{
					var class_css = 'left_el';
				}
					
				html += '<div class="'+class_css+'">'+this.left_element[int]+'</div>';
			}
		
			html += '</div>';
		}
		
		if(this.right_element.length > 0)
		{
			html += '<div class="footer_right">';
			
			for(var int = 0; int < this.right_element.length; int++) 
			{
				if(int == 0)
				{
					var class_css = 'right_first_el';
				}
				else if(int == this.right_element.length - 1)
				{
					var class_css = 'right_last_el';
				}
				else
				{
					var class_css = 'right_el';
				}
					
				html += '<div class="'+class_css+'">'+this.right_element[int]+'</div>';
			}
			
			html += '<div style="float:right;border-right:1px solid #777;">&nbsp;</div></div>';
		}
		
		document.getElementById('footer').innerHTML = html;
	};
	
	this.set_ssid = function(p_ssid)
	{
		this.ssid = p_ssid;
	};
	
	this.generate_counter_session = function(p_pos)
	{
		this.obj_session = new session_management(this.file_presence,this.ssid,this.dir_object,this.file_presence_param);
		this.obj_session.settimeout_session = setInterval("footer.obj_session.counter_session_update();",this.interval_presence);
		this.add_element('<div id="lifetime">-- : --</div>',p_pos);
		this.add_element('<div id="free_cookie"></div>',p_pos);
	};
}