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
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################

define('GREGARIUS_RELEASE', '0.5.4');
define('GREGARIUS_CODENAME', 'Coots');

define('DBINIT', dirname(__FILE__) . '/dbinit.php');

// NOTE: This _must_ be a standard version string, see:
// php.net/version_compare
define('REQUIRED_VERSION', '4.0.0');

define('SERVER_DEFAULT', 'localhost');
define('DATABASE_DEFAULT', 'rss');

function install_main() {
    $hasXML    = function_exists('xml_parser_create');
    $hasMySQL  = function_exists('mysql_connect');
    $hasSQLite = function_exists('sqlite_open');

    echo ""
    . "<html>\n"
    . "<head>\n"
    . " <title>Gregarius " . GREGARIUS_RELEASE . " " . GREGARIUS_CODENAME . " Installer</title>\n"
    . "	<link rel=\"stylesheet\" type=\"text/css\" href=\"themes/default/css/layout.css\" />\n"
    . "	<link rel=\"stylesheet\" type=\"text/css\" href=\"themes/default/css/look.css\" />\n"
    . "<style>\n"
    . "  .help {\n"
    . "    display: none;\n"
    . "    font-size: 12pt;\n"
    . "    color: red;\n"
    . "  }\n"
    . "  .found {\n"
    . "    color: green;\n"
    . "    font-weight: bold;\n"
    . "  }\n"
    . "  .not_found {\n"
    . "    color: red;\n"
    . "    font-weight: bold;\n"
    . "  }\n"
    . "</style>\n" 
    . "<script type=\"text/javascript\">\n"
    . "  function ToggleHelp(name) {\n"
    . "    var i=document.getElementById(name);\n"
    . "    if('block' == i.style.display) {\n"
    . "      i.style.display='none';\n"
    . "    } else {\n"
    . "      i.style.display='block';\n"
    . "    }\n"
    . "  }\n"
    . "\n"
    . "  function ValidateData() {\n"
    . "    var ret = false;\n"
    . "    if(document.getElementById('server').value.length < 1) {\n"
    . "      alert('A server location is required.');\n"
    . "      document.getElementById('server').focus();\n"
    . "    } else if(document.getElementById('database').value.length < 1) {\n"
    . "      alert('A database name is required.');\n"
    . "      document.getElementById('database').focus();\n"
    . "    } else if(document.getElementById('username').value.length < 1) {\n"
    . "      alert('A username is required.');\n"
    . "      document.getElementById('username').focus();\n"
    . "    } else if(document.getElementById('password').value.length < 1) {\n"
    . "      alert('A password is required.');\n"
    . "      document.getElementById('password').focus();\n"
    . "    } else {\n"
    . "      ret = true;\n"
    . "    }\n"
    . "\n"
    . "    return ret;\n"
    . "  }\n"
    . "</script>\n"
    . "</head>\n"
    . "<body>\n"
    . "<h2 class=\"trigger\">Gregarius Database Setup</h2>\n"
    . "<div id=\"install\" class=\"frame\">\n"
    . "<p><img src=\"themes/default/media/installer/codename.jpg\" alt=\"Coots\" /></p>\n"
    . "<fieldset class=\"install\">\n"
    . "<legend>Diagnostics</legend>\n"
    . "<p class=\"" . (version_compare(REQUIRED_VERSION, PHP_VERSION) <= 0 ? "found" : "not_found") . "\">PHP Version: " . phpversion() . "</p>\n"
    . "<p class=\"" . ($hasXML ? "found" : "not_found") . "\">XML: " . ($hasXML ? "Found" : "Not Found!") . "</p>\n"
    . "<p class=\"" . ($hasMySQL ? "found" : "not_found") . "\">MySQL: " . ($hasMySQL ? "Found" : "Not Found!") . "</p>\n"
    . "<p class=\"" . ($hasSQLite ? "found" : "not_found") . "\">SQLite: " . ($hasSQLite ? "Found" : "Not Found!") . "</p>\n"
    . "</fieldset>\n"
    . "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\" onSubmit=\"return ValidateData();\">\n"
    . "<fieldset class=\"install\">\n"
    . "<legend>SQL Settings</legend>\n"
    . "<p><label for=\"type\">Server Type [<a href=\"#\" onClick=\"ToggleHelp('type_help'); return false; \">?</a>]</label>\n"
    . "<input type=\"radio\" name=\"type\" id=\"type\" value=\"mysql\" " . ($hasMySQL ? "checked=\"1\"" : "disabled=\"1\"") . "/>MySQL"
    . "<input type=\"radio\" name=\"type\" id=\"type\" value=\"sqlite\"" . ($hasSQLite ? ($hasMySQL ? "" : "checked=\"1\"") : "disabled=\"1\"") . "/>SQLite"
    . "<span class=\"help\" name=\"type_help\" id=\"type_help\">The type of server being used.</span></p>\n"
    . "<p><label for=\"server\">Server Location [<a href=\"#\" onClick=\"ToggleHelp('server_help'); return false; \">?</a>]</label>\n"
    . "<input type=\"text\" name=\"server\" id=\"server\" value=\"" . SERVER_DEFAULT . "\" />"
    . "<span class=\"help\" name=\"server_help\" id=\"server_help\">The location of the database. Default: " . SERVER_DEFAULT . "</span></p>\n"
    . "<p><label for=\"database\">Database Name [<a href=\"#\" onClick=\"ToggleHelp('database_help'); return false; \">?</a>]</label>\n"
    . "<input type=\"text\" name=\"database\" id=\"database\" value=\"" . DATABASE_DEFAULT . "\" />"
    . "<span class=\"help\" name=\"database_help\" id=\"database_help\">The name of the database.  Default: " . DATABASE_DEFAULT . "</span></p>\n"
    . "<p><label for=\"username\">Database UserName [<a href=\"#\" onClick=\"ToggleHelp('username_help'); return false; \">?</a>]</label>\n"
    . "<input type=\"text\" name=\"username\" id=\"username\" value=\"\" />"
    . "<span class=\"help\" name=\"username_help\" id=\"username_help\">The username to connect to the database. <br/>Make sure this user has INSERT,UPDATE,DELETE,ALTER permission to the database!</span></p>\n"
    . "<p><label for=\"password\">Database Password [<a href=\"#\" onClick=\"ToggleHelp('password_help'); return false; \">?</a>]</label>\n"
    . "<input type=\"password\" name=\"password\" id=\"password\" value=\"\" />"
    . "<span class=\"help\" id=\"password_help\">The passsword used to connect to the database.</span></p>\n"
    . "<p><label for=\"prefix\">Database Table Prefix [<a href=\"#\" onClick=\"ToggleHelp('prefix_help'); return false; \">?</a>]</label>\n"
    . "<input type=\"text\" name=\"prefix\" id=\"prefix\" value=\"\" />"
    . "<span class=\"help\" name=\"prefix_help\" id=\"prefix_help\">The string to prefix the tables with. Example: m_feeds</span></p>\n"
    . "<p>&nbsp;</p>\n"
    . "</fieldset>\n"
    . "<p><input type=\"submit\" name=\"action\" value=\"Proceed\" /></p>\n"
    . "<p><input type=\"hidden\" name=\"process\" value=\"1\"></p>\n"
    . "</form>\n"
    . "</div>\n"
    . "</body>\n"
    . "</html>\n";
}

if(file_exists(DBINIT)) {
    print("The dbinit.php file already exists in the Gregarius directory!");
} else if(!empty($_POST['process']) && 1 == $_POST['process']){
// process the post data

    if(empty($_POST['server']) ||
       empty($_POST['database']) ||
       empty($_POST['username']) ||
       empty($_POST['password']) ||
       empty($_POST['type'])) {

        print("Not all required fields have been filled in!");
    } else {

$out = "<?php
//
// The type of database server you are using. By default
// Gregarius will look for a MySQL database server. If you
// would like to use an SQLite database, change accordingly
//
define ('DBTYPE','" . $_POST['type'] . "');

//
// The name of your database
//
define ('DBNAME','" . $_POST['database'] . "');

//
// The username to use when connecting to the database. Make sure that
// thus user owns privileges to CREATE database tables on the above
// database!
//
define ('DBUNAME','" . $_POST['username'] . "');

//
// The password to use when connecting to the database
//
define ('DBPASS', '" . $_POST['password'] . "');

//
// If you are using a MySQL database:
// The hostname of your database server. Unless you know
// different this should probably be 'localhost' or '127.0.0.1'
//
// If you are using a SQLite database:
// This constant must contain the full path to your database file, 
// for example: '/tmp/gregarius.db'
// Note that the apache process must have write access privileges 
// on the given directory!
//
define ('DBSERVER', '" . $_POST['server'] . "');

//
// The table name prefix to use. If you specify anything here,
// say 'gregarius', your database table 'channels' will be referred to 
// as 'gregarius_channels'. This is useful to avoid table collisions when 
// your hosting provider only grants you one single database and several
// applications rely on that db.
//
// If this is not the case you can safely ignore this option.
//
";

if(empty($_POST['prefix'])) {
    $out .= "//define ('DB_TABLE_PREFIX','');";
} else {
    $out .= "define('DB_TABLE_PREFIX', '" . $_POST['prefix'] . "');";
}

$out .= "\n?>";
        
        $fp = @fopen(DBINIT, 'w');

        if(!$fp) {
        // unable to open file for writing
            header('Content-type: application/x-httpd-php-source');
            echo($out);
            exit();
        } else  {
        // write the file
            fwrite($fp, $out);
            fclose($fp);

            header('Location: admin/');
            exit();
        }
    }
} else {
// print out the form
    install_main();
}
?>
