// folder/category collapsing
var FOLDERCOOKIENAME = "collapsedfolders";
var CATEGORYCOOKIENAME = "collapsedcategories";

document.folders = new Array();
document.categories = new Array();

// src: http://www.javascripter.net/faq/settinga.htm
function setCookie(cookieName,cookieValue,path) {
    //alert(cookieValue);
    var today = new Date();
    var expire = new Date();
    // 1 year
    expire.setTime(today.getTime() + 31536000000);
    document.cookie = cookieName+"="+escape(cookieValue) 
    	+ "; expires="+expire.toGMTString()
    	+ "; path="+path;
}

function getCookie(cookieName) {
    var theCookie=""+document.cookie;
    var ind=theCookie.indexOf(cookieName);
    if (ind==-1 || cookieName=="") return "";
    var ind1=theCookie.indexOf(';',ind);
    if (ind1==-1) ind1=theCookie.length;
    return unescape(theCookie.substring(ind+cookieName.length+1,ind1));
}

function _init() {
    var foldercook=getCookie(FOLDERCOOKIENAME);
    //alert('loaded: ' + foldercook);
	var categorycook=getCookie(CATEGORYCOOKIENAME);
    //alert('loaded: ' + categorycook);
    if (document.folders.length == 0 && foldercook) {
        document.folders = foldercook.split(':');
    }
    if (document.categories.length == 0 && categorycook) {
        document.categories = categorycook.split(':');
    }
}

function _tgl(fid,ftype) {
    _init();
	if(ftype == 'category'){
		x = document.categories;
	}else{
		x = document.folders;
	}
    if (ul = document.getElementById('fc'+fid)) {
        if (ul.className == 'fexpanded') {
            //alert("expanded -> collapsed");
            ul.className = 'fcollapsed';
            ul.style.display = 'none';
            for (i=0;i<x.length;i++) {
                if (x[i] == fid) return;
            }
            x.push(fid);

        } else {
            //alert("collapsed -> expanded");
            ul.className = 'fexpanded';
            ul.style.display = 'block';
            for (i=0;i<x.length;i++) {
                if (x[i] == fid) {
                  x[i] = 0;
                  x.sort();
                  x.shift();
                }
            }
        }
    } else {
        return;
    }
    
    if (strong = document.getElementById('fs'+fid)) {
        if (ul.style.display == 'block') {
            d = 'none';
        } else {
            d = 'inline';
        }
        strong.style.display= d;
    }

    x.sort();
    c = "";
    // home-made join method to filter out zeroes
    for (i=0;i<x.length;i++) {
        if (x[i] > 0) {
            c = c+ x[i];
            if (i < (x.length - 1)) c=c+':'
        }
    }
	if(ftype == 'category'){
		setCookie(CATEGORYCOOKIENAME,c,"/");
	}else{
		setCookie(FOLDERCOOKIENAME,c,"/");
	}
}

