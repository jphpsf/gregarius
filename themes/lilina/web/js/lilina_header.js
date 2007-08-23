function toggleItemByID(id) {
 var fld=document.getElementById("c" + id);
 if (fld.style.display == "none") {
 	fld.style.display = "block";
 } else {
 	fld.style.display = "none";
 }
}

function _lilina_expandAlldivs(flag) {
	if (flag) { // then expand
		for(i=0;i<_lilina_divs.length; i++){
			_lilina_divs[i].style.display = "block";
			document._lilinaCollapsed = false;
		}
		setCookie("_lilinaItemsCollapser", "uncollapsed", _lilinaCookiePath);
	} else { // else contract
		for(i=0;i<_lilina_divs.length; i++){
			_lilina_divs[i].style.display = "none";
			document._lilinaCollapsed = true;
		}
		setCookie("_lilinaItemsCollapser", "collapsed", _lilinaCookiePath);
	}
}

function _lilina_toggleAlldivs() {
	_lilina_expandAlldivs(document._lilinaCollapsed);
}

// This function below taken from code that mbonetti sent me. 

function _lilina_channels_collapse(o) {
	var items = o.parentNode.parentNode;
	if (items && document.sidecollapsed) {
		document.getElementById('channels').style.display="block";
		document.getElementById('sidemenu').style.display="block";
		items.style.marginLeft="290px";
		document.sidecollapsed = false;
		o.innerHTML = '&nbsp;&laquo;&nbsp;';
		setCookie("_lilinaSidebarCollapser", "uncollapsed", _lilinaCookiePath);
	} else {
		document.getElementById('channels').style.display="none";
		document.getElementById('sidemenu').style.display="none";
		document.sidecollapsed = true;
		items.style.marginLeft="0px";
		o.innerHTML = '&nbsp;&raquo;&nbsp;';
		setCookie("_lilinaSidebarCollapser", "collapsed", _lilinaCookiePath);
	}
}

