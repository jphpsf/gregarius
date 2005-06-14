// folder collpasing

var COOKIENAME = "collapsedfolders";
document.cf = new Array();

// src: http://www.javascripter.net/faq/settinga.htm
function setCookie(cookieName,cookieValue) {
    //alert(cookieValue);
    var today = new Date();
    var expire = new Date();
    // 1 year
    expire.setTime(today.getTime() + 31536000000);
    document.cookie = cookieName+"="+escape(cookieValue) + ";expires="+expire.toGMTString();
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
    var c=getCookie(COOKIENAME);
    //alert('loaded: ' + c);
    if (document.cf.length == 0 && c) {
        document.cf = c.split(':');
    }
}

function _tgl(fid) {
    _init();

    if (ul = document.getElementById('fc'+fid)) {
        if (ul.className == 'fexpanded') {
            // expanded -> collapsed
            ul.className = 'fcollapsed';
            ul.style.display = 'none';
            for (i=0;i<document.cf.length;i++) {
                if (document.cf[i] == fid) return;
            }
            document.cf.push(fid);

        } else {
            // collapsed -> expanded
            ul.className = 'fexpanded';
            ul.style.display = 'block';
            for (i=0;i<document.cf.length;i++) {
                if (document.cf[i] == fid) {
                  document.cf[i] = 0;
                  document.cf.sort();
                  document.cf.shift();
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

    document.cf.sort();
    c = "";
    // home-made join method to filter out zeroes
    for (i=0;i<document.cf.length;i++) {
        if (document.cf[i] > 0) {
            c = c+ document.cf[i];
            if (i < (document.cf.length - 1)) c=c+':'
        }
    }
    setCookie(COOKIENAME,c);

}

