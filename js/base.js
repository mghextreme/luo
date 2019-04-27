$back = function(slk){
	if (document.referrer.indexOf(adm) != -1)
	{ history.go(-1); }
	else window.location = adm + slk;
}

$clicked = function(num){
	if (num == 1 || num == 13 || num == 32)
	{ return true; }
	return false;
}