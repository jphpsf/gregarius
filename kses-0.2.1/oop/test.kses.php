<?php
	include_once("class.kses.php");

	$myKses = new kses;
	$myKses->Protocols(array("proto1", "proto2:", "proto3"));   // Add a list of protocols
	$myKses->Protocols("proto4:");  // Add a single protocol (Note ':' is optional at end)
	$myKses->AddProtocol("proto9"); // Another way to add a single protocol

	//	Allows <p>|</p> tag
	$myKses->AddHTML("p");

	//	Allows 'a' tag with href|name attributes,
	//	href has minlen of 10 chars, and maxlen of 25 chars
	//	name has minlen of  2 chars
	$myKses->AddHTML(
		"a",
		array(
			"href" => array('maxlen' => 25, 'minlen' => 10),
			"name" => array('minlen' => 2)
		)
	);

	//	Allows 'td' tag with colspan|rowspan|class|style|width|nowrap attributes,
	//		colspan has minval of   2       and maxval of 5
	//		rowspan has minval of   3       and maxval of 6
	//		class   has minlen of   1 char  and maxlen of   10 chars
	//		style   has minlen of  10 chars and maxlen of 100 chars
	//		width   has maxval of 100
	//		nowrap  is valueless
	$myKses->AddHTML(
		"td",
		array(
			"colspan" => array('minval' =>   2, 'maxval' =>   5),
			"rowspan" => array('minval' =>   3, 'maxval' =>   6),
			"class"   => array("minlen" =>   1, 'maxlen' =>  10),
			"width"   => array("maxval" => 100),
			"style"   => array('minlen' =>  10, 'maxlen' => 100),
			"nowrap"  => array('valueless' => 'y')
		)
	);

	$test_tags = array(
		'<a href="http://www.chaos.org/">www.chaos.org</a>',
		'<a name="X">Short \'a name\' tag</a>',
		'<td colspan="3" rowspan="5">Foo</td>',
		'<td rowspan="2" class="mugwump" style="background-color: rgb(255, 204 204);">Bar</td>',
		'<td nowrap>Very Long String running to 1000 characters...</td>',
		'<td bgcolor="#00ff00" nowrap>Very Long String with a blue background</td>',
		'<a href="proto1://www.foo.com">New protocol test</a>',
		'<img src="proto2://www.foo.com" />',
		'<a href="javascript:javascript:javascript:javascript:javascript:alert(\'Boo!\');">bleep</a>',
		'<a href="proto4://abc.xyz.foo.com">Another new protocol</a>',
		'<a href="proto9://foo.foo.foo.foo.foo.org/">Test of "proto9"</a>',
		'<td width="75">Bar!</td>',
		'<td width="200">Long Cell</td>'
	);

	// Quick hack to see if we're in CLI or not
	$nlbr = ($_SERVER["DOCUMENT_ROOT"] == "") ? "\n" : "<br />\n";
	$spc  = ($_SERVER["DOCUMENT_ROOT"] == "") ? "  " : "&nbsp;&nbsp;";
	$hr   = ($_SERVER["DOCUMENT_ROOT"] == "") ? str_repeat("-", 70) . "\n" : "<hr />\n";

	// Keep only allowed HTML from the form.
	echo "\n{$hr}Testing class.kses.php\n$hr\n";
	foreach($test_tags as $tag)
	{
		$temp  = $myKses->Parse($tag);
		$check = ($temp == $tag) ? "pass" : "kses";
		if($_SERVER["DOCUMENT_ROOT"] != "")
		{
			$final = htmlentities($tag) . $nlbr . "[$check]$spc" . htmlentities($temp) . "$nlbr$nlbr";
		}
		else
		{
			$final = "$tag{$nlbr}[$check]$spc$temp$nlbr$nlbr";
		}

		echo $final;
	}
	echo "{$hr}Testing completed\n$hr\n\n";
?>