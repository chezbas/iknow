/**
 * Get the user event type
 * @param p_event
 * @returns
 */
function vimofy_user_event_get_type(p_event)
{
	if(p_event == __LMOD_OPEN__)
	{
		return __VIMOFY_INTERNAL__;
	}
	else
	{
		return __VIMOFY_EXTERNAL__;
	}
}

/**
 * Vimofy internal action
 * @param p_action
 * @param vimofy_id
 */
function vimofy_user_action(p_action,vimofy_id)
{
	switch(p_action) 
	{
		case __LMOD_OPEN__:
			vimofy_lmod_click(vimofy_id);
			break;
	}
}


function vimofy_execute_event(p_event,p_moment,vimofy_id)
{
	/* Get the event to do */
	if(typeof(eval('Vimofy.'+vimofy_id+'.event')) != 'undefined')
	{
		for(var vimofy_dest in eval('Vimofy.'+vimofy_id+'.event.evt'+p_event))
		{
			for(var vimofy_to_event in eval('Vimofy.'+vimofy_id+'.event.evt'+p_event+'.'+vimofy_dest))
			{
				for(var actions in eval('Vimofy.'+vimofy_id+'.event.evt'+p_event+'.'+vimofy_dest+'.'+vimofy_to_event))
				{
					var action_to_do = eval('Vimofy.'+vimofy_id+'.event.evt'+p_event+'.'+vimofy_dest+'.'+vimofy_to_event+'.'+actions);
					if(vimofy_user_event_get_type(action_to_do.exec) == __VIMOFY_INTERNAL__)
					{
						/* Internal action to do */
						if(action_to_do.moment == p_moment)
						{
							vimofy_user_action(action_to_do.exec,vimofy_dest);
						}
					}
					else
					{
						/* External action to do */
						if(action_to_do.moment == p_moment)
						{
							/* #URL SIBY */
							eval(action_to_do.exec);
						}
					}
				}
			}
		}
	}
}