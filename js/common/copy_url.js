/**
 * Cette fonction copie dans le presse papier l'url sans le ssid et avec tout les param�tres
 * 
 */
function copier_url(){
		  
	var clip = new ZeroClipboard.Client();
	clip.ext(url_remove('ssid'));
	clip.glue('d_clip_button');
	
}

function url_remove (source) {
	
	var pos;
	var chaine;
	var chaine2;
	var value_source;

	chaine = document.location.href;

	value_source = gup(source);

	chaine2 = chaine;

	pos = chaine.search(source);
	while ( pos != -1 ) {
		chaine2 = chaine.substr(0,pos-1)+chaine.substr(pos+value_source.length+1+source.length,chaine.length);
		chaine = chaine2;
		//alert(pos);
		pos = chaine.search(source);
	}

	return (chaine2);
	
}

// return value of a parameters in get URL
function gup(name)
{
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(document.location.href);
  if(results == null)
  {
    return "";
  }
  else
  {
    return results[1];
  }
}