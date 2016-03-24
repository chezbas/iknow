function iknow_tab(p_id) 
{ 	
	/**
	 * Define attribut
	 */
	this.id = p_id;
	this.tab_selected = null;
	this.tab = Array();
	this.tab_id = Array();
	
	/**
	 * Create the tabbar
	 */
	var tab_tab = document.createElement('div');
	tab_tab.id=p_id+"_tab_c";
	tab_tab.className="tabbar_c";
	document.getElementById(p_id).appendChild(tab_tab);
	
	var tab_tab = document.createElement('div');
	tab_tab.id=p_id+"_tab";
	tab_tab.className="tabbar";
	document.getElementById(p_id+"_tab_c").appendChild(tab_tab);
	
	/**
	 * Create the tab content
	 */
	var tab_content = document.createElement('div');
	tab_content.id=p_id+"_content";
	tab_content.className = "tab_container";
	document.getElementById(p_id).appendChild(tab_content);
	
	/**
	 * Add a tab
	 */
	this.addTab = function(p_tab_id,p_title,p_content,p_event)
	{
		try 
		{
			this.tab_selected = p_tab_id;
			this.tab.push(p_tab_id);
			this.tab_id[p_tab_id] = this.tab.length;
			
			// Add Tab
			var newTab=document.createElement('div');
			
			// Tab container
			newTab.className="ikn_tab_tab";
			newTab.id = this.id+p_tab_id;
			newTab.onclick = function(){eval(p_id+'.click("'+p_tab_id+'");'+p_event);};
		    document.getElementById(this.id+"_tab").appendChild(newTab);
		    
		    // Left corner
		    var newTab=document.createElement('div');
		    newTab.className = "ikn_tab_tab_content_l_corn";
		    newTab.id = this.id+'_l_c'+p_tab_id;
		    document.getElementById(this.id+p_tab_id).appendChild(newTab);
		    
		    // Right corner
		    var newTab=document.createElement('div');
		    newTab.className = "ikn_tab_tab_content_r_corn";
		    newTab.id = this.id+'_r_c'+p_tab_id;
		    document.getElementById(this.id+p_tab_id).appendChild(newTab);
		    
		    // Content
		    var newTab=document.createElement('div');
		    newTab.innerHTML=decodeURIComponent(p_title);
		    newTab.id= this.id+'_content_c'+p_tab_id;
		    newTab.className="ikn_tab_tab_content";
		    document.getElementById(this.id+p_tab_id).appendChild(newTab);
		    
		    // Add Tab Content
		    var newTabContent=document.createElement('div');
		    newTabContent.innerHTML=decodeURIComponent(p_content);
		    newTabContent.className="tab_content";
		    newTabContent.id = this.id+'_content'+p_tab_id;
		    newTabContent.style.visibility = 'hidden';
		    
		    document.getElementById(this.id+"_content").appendChild(newTabContent);  
		} 
		catch(e)
		{
			alert('11  -  '+p_content);
		}
	};
	
	/**
	 * Set the activ tab
	 * TODO : have to read current language and force it into url
	 */
	this.setTabActive = function(p_tab_id)
	{
		try 
		{
			// Set the tab background no selected theme
			document.getElementById(this.id+'_content_c'+this.tab_selected).className = 'ikn_tab_tab_content';
			document.getElementById(this.id+'_r_c'+this.tab_selected).className = 'ikn_tab_tab_content_r_corn';
			document.getElementById(this.id+'_l_c'+this.tab_selected).className = 'ikn_tab_tab_content_l_corn';
			document.getElementById(this.id+this.tab_selected).className = 'ikn_tab_tab';
			//document.getElementById(this.id+'_content'+this.tab_selected).style.display = 'none';
			document.getElementById(this.id+'_content'+this.tab_selected).style.visibility = 'hidden';
			
			// Set the tab background selected theme
			document.getElementById(this.id+'_content_c'+p_tab_id).className = 'ikn_tab_tab_content_selected';
			document.getElementById(this.id+'_r_c'+p_tab_id).className = 'ikn_tab_tab_content_r_corn_selected';
			document.getElementById(this.id+'_l_c'+p_tab_id).className = 'ikn_tab_tab_content_l_corn_selected';
			document.getElementById(this.id+p_tab_id).className = 'ikn_tab_tab_selected';
			//document.getElementById(this.id+'_content'+p_tab_id).style.display = 'block';
			document.getElementById(this.id+'_content'+p_tab_id).style.visibility = 'visible';
			
			// Display the content of the selected tab
			document.getElementById(this.id+'_content'+this.tab_selected).style.zIndex = 1;
			
			// Init the tab selected
			this.tab_selected = p_tab_id;
			
			// Hide the last tab selected content
			document.getElementById(this.id+'_content'+p_tab_id).style.zIndex = 2;
			
			// Get the position of the tab
			tab_offLeft = document.getElementById(this.id+p_tab_id).offsetLeft;
			tab_offWidth = document.getElementById(this.id+p_tab_id).offsetWidth;
			tabbar_width = document.getElementById(this.id).offsetWidth;
			
			// Control the position of the tab
			if(tab_offLeft+tab_offWidth > tabbar_width)
			{
				// The tab isn't in the displayed area
				document.getElementById(this.id+'_tab_c').scrollLeft = tab_offLeft;
			}
			else
			{
				// The tab isn't in the displayed area
				if(document.getElementById(this.id+'_tab_c').scrollLeft > tab_offLeft)
				{
					document.getElementById(this.id+'_tab_c').scrollLeft = tab_offLeft;
				}
			}
		} 
		catch(e)
		{
			//alert('erreur '+e.message);
		}
	};
	
	this.getActiveTab = function()
	{
		return this.tab_selected;
	};

	this.next = function()
	{
		var next_tab = this.tab_id[this.tab_selected];
		
		if(document.getElementById(this.id+this.tab[next_tab]))
		{
			this.setTabActive(this.tab[next_tab]);
			return next_tab;
		}
		
		return next_tab-1;
	};

	this.previous = function()
	{
		var previous_tab = this.tab_id[this.tab_selected]-2;

		if(document.getElementById(this.id+this.tab[previous_tab]))
		{
			this.setTabActive(this.tab[previous_tab]);
			return previous_tab;
		}
		
		return previous_tab+1;
		
	};

	/**
	 * Onclick event on a tab
	 */
	this.click = function(p_tab_id)
	{
		this.setTabActive(p_tab_id);
	};
}