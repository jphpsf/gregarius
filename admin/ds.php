<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
#
###############################################################################
# This program is free software and open source software; you can redistribute
# it and/or modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of the License,
# or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:      mbonetti at users dot sourceforge dot net
# Web page:    http://sourceforge.net/projects/gregarius
#
###############################################################################

function implode_v($glue, $array) {
    $output = array();
    foreach( $array as $key => $item )
      $output[] =  $key;    
    return implode($glue, $output);
}

function PHParrayToJSArray($data) {
    $i=0;
    foreach($data as $tag => $attrs) {
	echo "store[$i] = new Array("
	  ."'$tag'";
	$jsattrs = implode_v("','",$attrs);
	if ($jsattrs) {
	    echo ",'" . $jsattrs . "'";
	}
	echo ");\n";
	$i++;
    }
}

function jsCode($data) {
?>
    var store = new Array();
	<?php PHParrayToJSArray($data); ?>
	var debug_ = false;	
	store.sort();
	function getForm() {
	  return  document.getElementById("cfg");
	}	 

        function populate1() {
	  var box1 = getForm().first;
	  var box2 = getForm().second;
	  box1.options.length = 0;
	  box2.options.length = 0
	  for (i=0;i<store.length;i++) {
	      box1.options[i]= new Option(store[i][0],i);
	  }		
	}	
	
	function add1() {
	  var newtag = getForm().newtag.value.replace(/[^a-zA-Z]*/g,"");
	  if (newtag == '') return;
	  getForm().newtag.value = '';
	  store [ store.length ] = new Array(newtag);
	  store.sort();
	  populate1();
	}	
 
	function delete1() {	
	   var box = getForm().first;
	   var number = box.options[box.selectedIndex].value;
	   if (!number) return;
	   var tmp = new Array();
	   var cntr = 0;
	   for (i=0;i<store.length;i++) {
                if (i != number) {	
	          tmp[cntr++] = store[i];
	        }	
	   }	
	   store = tmp;
	   populate1();
	}
		
	function add2() {
	  var box = getForm().first;
	  var number = box.options[box.selectedIndex].value;
	  if (!number) return;
	  var newattr = getForm().newattr.value.replace(/[^a-zA-Z]*/g,"");
	  if (newattr == '') return;
	  store[number].push(newattr);
	  getForm().newattr.value = "";
	  populate2();
	}	
	
	function debug(msg) {
	 if (!debug_) return;
	 alert(msg);
	}	 
	
	function delete2() {
	  var box1 = getForm().first;
	  var box2 = getForm().second;
	  if (box1.selectedIndex == -1) return;
	  var n1 = box1.options[box1.selectedIndex].value;
	  var tag = box1.options[box1.selectedIndex].text;
	  if (!n1) return;
	  var n2 = box2.selectedIndex;
	  if (n2 == -1) return;
	  var attrToDelete = box2.options[n2].text;
	
	  var list = store[n1];
	  var tmp = new Array();
	  tmp[0] = tag;
	  var cntr = 1;
          for(i=1;i<list.length;i++) {
	      if (list[i] != attrToDelete) {
	        tmp[cntr] = list[i];
	        cntr++;
	      }	
	   }	
	   store[n1] = tmp;
	   populate2();	
	}	
	
	function populate2() {	
	   var box = getForm().first;
	   var number = box.options[box.selectedIndex].value;
	   if (!number) return;
	   var list = store[number];

	   var box2 = getForm().second;
	   box2.options.length = 0;
	   if (list.length <= 1) return;	
	   for(i=1;i<list.length;i++) {	
	       box2.options[i-1] = new Option(list[i],i-1);
	   }	
	}
	
	function pack() {
	 var packed_store = store.join(' ');
	 getForm().packed.value = packed_store;
	}	
    
    
    populate1();
<?php  } ?>
