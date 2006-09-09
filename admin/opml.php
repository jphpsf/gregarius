<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2006 Marco Bonetti
#
###############################################################################
# This program is free software and open source software; you can redistribute
# it and/or modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of the License,
# or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at gmail dot com
# Web page:	 http://gregarius.net/
#
###############################################################################

/**
 * renders the opml export form
 */
function opml() {


    // import

    //disable file upload formfields when file_upload is false
    $disableupload = ini_get('file_uploads') ? "":" disabled=\"disabled\" ";
    echo "<h2>". __('OPML:') ."</h2>\n";
    echo "<div id=\"admin_opml\">\n";

    echo "<fieldset id=\"opmlimport\">\n"
    ."<legend>" . __('Import OPML:') . "</legend>";

    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<p><input type=\"hidden\" name=\"". CST_ADMIN_DOMAIN ."\" value=\"".CST_ADMIN_DOMAIN_CHANNEL."\"/>\n";
    echo "<label for=\"opml\">" . __('... from URL:') ."</label>\n";
    echo "<input type=\"text\"	name=\"opml\" id=\"opml\" value=\"http://\" onfocus=\"this.select()\"/></p>\n";


    echo '<p><input type="hidden" name="' . CST_ADMIN_DOMAIN . '" value="' . CST_ADMIN_DOMAIN_CHANNEL . "\" />\n";
    echo '<input type="hidden" name="MAX_FILE_SIZE" value="150000" />' . "\n";
    echo '<label for="opmlfile">' . __('... from File:') . "</label>\n";
    echo '<input name="opmlfile" type="file" id="opmlfile" '.$disableupload.'/></p>' . "\n";

    /*

    define ('CST_ADMIN_OPML_IMPORT_WIPE',1);
    define ('CST_ADMIN_OPML_IMPORT_FOLDER',2);
    define ('CST_ADMIN_OPML_IMPORT_MERGE',3);
    */

    echo "\n"
    ."<p>".__('Import new feeds and:')."</p>"

    ."<p style=\"padding-left:1em;\"><input checked=\"checked\" type=\"radio\" id=\"opml_import_option_merge\" name=\"opml_import_option\" value=\"".CST_ADMIN_OPML_IMPORT_MERGE."\" />\n"
    ."<label for=\"opml_import_option_merge\" >".__('... merge them with the existing ones.')."</label></p>\n"


    ."<p style=\"padding-left:1em;\"><input type=\"radio\" id=\"opml_import_option_folder\" name=\"opml_import_option\" value=\"".CST_ADMIN_OPML_IMPORT_FOLDER."\" />\n"
    ."<label for=\"opml_import_option_folder\" >".__('... add them to the folder:')."</label>"
    .rss_toolkit_folders_combo('opml_import_to_folder',null)
    ."</p>\n"

    ."<p style=\"padding-left:1em;\"><input type=\"radio\" id=\"opml_import_option_wipe\" name=\"opml_import_option\" value=\"".CST_ADMIN_OPML_IMPORT_WIPE."\" />\n"
    ."<label for=\"opml_import_option_wipe\" >".__('... replace all existing feeds and items.')."</label></p>\n"

    ."";


    echo "<p style=\"text-align:center\"><input type=\"hidden\" name=\"". CST_ADMIN_METAACTION ."\" value=\"ACT_ADMIN_IMPORT\" />\n";
    echo "<input type=\"submit\" name=\"action\" value=\"". __('Import') ."\" /></p>\n";




    echo "</form>\n";
    echo "</fieldset>\n";



    // export
    opml_export_form();
	dump_export_form();
    echo "</div>\n";
}

/*************** OPML Export ************/

function opml_export_form() {
    if (getConfig('rss.output.usemodrewrite')) {
        $method ="post";
        $action = getPath() ."opml";
    } else {
        $method ="get";
        $action = getPath() ."opml.php";
    }
    echo "<fieldset style=\"vertical-align:top\">\n<legend>".__('Export OPML:')."</legend>\n";
    echo "<form method=\"$method\" action=\"$action\">\n"
    ."<p><label for=\"action\">". __('Export OPML:'). "</label>\n"
    ."<input type=\"submit\" name=\"act\" id=\"action\" value=\"". __('Export') ."\" />"
    ."</p>\n</form>\n"
    ."</fieldset>\n";
}


/***** DUMP ******/

function dump_export_form() {

    echo "<fieldset style=\"vertical-align:top\">\n<legend>".__('XML Dump:')."</legend>\n";
    echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n"
    ."<p><label for=\"action\">". __('Dump your Gregarius installation to XML'). "</label>\n"
    ."<input type=\"submit\" name=\"dumpact\" id=\"action\" value=\"".__('Dump!')."\" />"
	."<input type=\"hidden\" name=\"". CST_ADMIN_DOMAIN ."\" value=\"".CST_ADMIN_DOMAIN_CHANNEL."\"/>\n"
	."<input type=\"hidden\" name=\"".CST_ADMIN_METAACTION."\" value=\"dump\"/>\n"
    ."</p>\n</form>\n"
    ."</fieldset>\n";
}

?>
