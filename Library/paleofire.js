function visibilite(thingId)
{
	var targetElement;
	targetElement = document.getElementById(thingId);
	
	if (targetElement.style.display == "none")
	{
		targetElement.style.display = "" ;
	}
	else
	{
		targetElement.style.display = "none" ;
	}
}

function getHtmlVer(){
  var CName  = navigator.appCodeName;
  var UAgent = navigator.userAgent;
  var HtmlVer= 0.0;
  // Remove start of string in UAgent upto CName or end of string if not found.
  UAgent = UAgent.substring((UAgent+CName).toLowerCase().indexOf(CName.toLowerCase()));
  // Remove CName from start of string. (Eg. '/5.0 (Windows; U...)
  UAgent = UAgent.substring(CName.length);
  // Remove any spaves or '/' from start of string.
  while(UAgent.substring(0,1)==" " || UAgent.substring(0,1)=="/"){
    UAgent = UAgent.substring(1);
  }
  // Remove the end of the string from first characrer that is not a number or point etc.
  var pointer = 0;
  while("0123456789.+-".indexOf((UAgent+"?").substring(pointer,pointer+1))>=0){
    pointer = pointer+1;
  }
  UAgent = UAgent.substring(0,pointer);
 
  if(!isNaN(UAgent)){
    if(UAgent>0){
    HtmlVer=UAgent;
    }
  }
  return HtmlVer;
}

function redirection (current_page) {

	location.href = current_page;
}