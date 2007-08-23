var _lilina_cDivs = document.getElementsByTagName('div');
var _lilina_divs = new Array();
document._lilinaCollapsed = true;

j=0;
for (i=0;i<_lilina_cDivs.length; i++){
	if (_lilina_cDivs[i].className == 'content') {
		_lilina_divs[j] = _lilina_cDivs[i]; 		
		_lilina_divs[j].lilinaArray = j;
		j++;
	}
}

// Mmm delicious cookies, let us find out what they want us to do. 

if(getCookie("_lilinaSidebarCollapser") == "collapsed") {
document.sidecollapsed = false;
_lilina_channels_collapse(document.getElementById("collapser").childNodes[0]);
}


if(getCookie("_lilinaItemsCollapser") == "uncollapsed") {
_lilina_expandAlldivs(1);
}
