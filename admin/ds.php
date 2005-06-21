<?php
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
