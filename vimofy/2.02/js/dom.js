/**
 * Set the width of an element
 * @param el element id
 * @param size size of the element
 * @param data_type type of size
 */
function vimofy_set_el_width(el,size,data_type)
{
	document.getElementById(el).style.width = size+data_type;
}

/**
 * Set the height of an element
 * @param el element id
 * @param size size of the element
 * @param data_type type of size
 */
function vimofy_set_el_height(el,size,data_type)
{
	document.getElementById(el).style.height = size+data_type;
}

/**
 * Get the offsetWidth of an element
 * @param el element id
 * @returns
 */
function vimofy_get_el_offsetWidth(el)
{
	return document.getElementById(el).offsetWidth;
}

/**
 * Get the client width of an element
 * @param el element id
 * @returns
 */
function vimofy_get_el_clientWidth(el)
{
	return document.getElementById(el).clientWidth;
}

/**
 * Get the innerHTML of an element
 * @param el element id
 * @returns
 */
function vimofy_get_innerHTML(el)
{
	
	try {
		return document.getElementById(el).innerHTML;
	} catch (e) {
		alert(el);
	}
	
}