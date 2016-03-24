function iknow_timer(p_time,p_execute) 
{ 	
	this.time = p_time;
	this.execute = p_execute;
	this.timer = null;
	var me = this;
	
	this.start = function()
	{
		this.timer = setTimeout(this.action,this.time);
	};
	
	this.stop = function()
	{
		try 
		{
			clearTimeout(me.timer);
		} 
		catch(e)
		{
			return false;
		}
		
		return true;
	};
	
	this.action = function()
	{
		me.timer = setTimeout(me.action,me.time);
		eval(me.execute);
	};
}