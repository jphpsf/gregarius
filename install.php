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
define('REQUIRED_VERSION', '4.3.0');

define('SQL_SERVER_DEFAULT', 'localhost');
define('SQLITE_DEFAULT', '/tmp/gregarius.sqlite');
define('WEB_SERVER_DEFAULT', 'localhost');
define('DATABASE_DEFAULT', 'rss');

define('TYPE_HELP', 'The type of database being used.');
define('SQL_SERVER_HELP', 'The location of the database. If in doubt, leave the default. Default: ' . SQL_SERVER_DEFAULT . '');
define('SQLITE_HELP', 'The path to the database.  If in doubt, leave the default. Default: ' . SQLITE_DEFAULT . '');
define('DATABASE_HELP', 'The name of the database. Default: ' . DATABASE_DEFAULT . '');
define('USERNAME_HELP', 'The username to connect to the database. <br/>Make sure this user has INSERT,UPDATE,DELETE,CREATE,ALTER permission to the database!');
define('PASSWORD_HELP', 'The password used to connect to the database.');
define('PREFIX_HELP', 'The string to prefix the tables with. Example: A table called rss_item should have rss as the prefix.');
define('ADMIN_USERNAME_HELP', 'The administrator username to use for database creation.');
define('ADMIN_PASSWORD_HELP', 'The administrator password used to connect to the database. Make sure this user has GRANT privileges!');
define('WEBSERVER_HELP', 'The location of the webserver. If in doubt, leave the default. Default: ' . WEB_SERVER_DEFAULT . '');

function install_main() {
    $hasXML    = function_exists('xml_parser_create');
    $hasMySQL  = function_exists('mysql_connect');
    $hasSQLite = function_exists('sqlite_open');
    $hasSocket = function_exists('fsockopen');

//    $hasSQLite = true;
    // If the server is running safe mode, try writing a temp file.
    if(ini_get('safe_mode')) {
        define ('TMPINIT', DBINIT . GREGARIUS_CODENAME . "tmp");
        $fp = @fopen(TMPINIT, 'w');
        if ($fp) {
            $hasWritePerm = true;
            fclose($fp);
            unlink (TMPINIT);
        } else {
            $hasWritePerm = false;
        }
    } else { // else, just check to see if it's writable.
        $hasWritePerm = is_writable(dirname(__FILE__));
    }

    if($hasMySQL && $hasSQLite) {
        $sql = "MySQL & SQLite";
    } else if($hasMySQL) {
        $sql = "MySQL";
    } else if($hasSQLite) {
        $sql = "SQLite";
    } else {
        $sql = "None!";
    }

    echo ""
    . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n"
    . "<head>\n"
    . "	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />"
    . " <title>Gregarius " . GREGARIUS_RELEASE . " " . GREGARIUS_CODENAME . " Installer</title>\n"
    . "	<link rel=\"stylesheet\" type=\"text/css\" href=\"themes/default/css/layout.css\" />\n"
    . "	<link rel=\"stylesheet\" type=\"text/css\" href=\"themes/default/css/look.css\" />\n"
    . "<style type=\"text/css\">\n"
    . "  .install {\n"
    . "    display: block;\n"
    . "    text-align: left;\n"
    . "  }\n"
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
    . "  h2 {\n"
    . "    margin: 2px;\n"
    . "  }\n"
    . "  label { display:block; }\n"
    . "</style>\n" 
    . "<script type=\"text/javascript\">\n"
    . "//<![CDATA[\n"
    . "  function ValidInput(str) {\n"
    . "    return true;\n"
    . "  }\n"
    . "\n"
    . "  function ToggleHelp(name) {\n"
    . "    var i=document.getElementById(name);\n"
    . "    if('block' == i.style.display) {\n"
    . "      i.style.display='none';\n"
    . "    } else {\n"
    . "      i.style.display='block';\n"
    . "    }\n"
    . "  }\n"
    . "\n"
    . "  function ToggleType(rad) {\n"
    . "    if('mysql' == rad.value) {\n"
    . "      document.getElementById('database').disabled = false;\n"
    . "      document.getElementById('username').disabled = false;\n"
    . "      document.getElementById('password').disabled = false;\n"
    . "      document.getElementById('admin_username').disabled = false;\n"
    . "      document.getElementById('admin_password').disabled = false;\n"
    . "      document.getElementById('prefix').disabled = false;\n"
    . "      document.getElementById('web_server').disabled = false;\n"
    . "      document.getElementById('server').value = '" . SQL_SERVER_DEFAULT . "';\n"
    . "      document.getElementById('server_help').innerHTML = '" . SQL_SERVER_HELP . "';\n"
    . "    } else if ('sqlite' == rad.value) {\n"
    . "      document.getElementById('database').disabled = true;\n"
    . "      document.getElementById('username').disabled = true;\n"
    . "      document.getElementById('password').disabled = true;\n"
    . "      document.getElementById('admin_username').disabled = true;\n"
    . "      document.getElementById('admin_password').disabled = true;\n"
    . "      document.getElementById('prefix').disabled = true;\n"
    . "      document.getElementById('web_server').disabled = true;\n"
    . "      document.getElementById('server').value = '" . SQLITE_DEFAULT . "';\n"
    . "      document.getElementById('server_help').innerHTML = '" . SQLITE_HELP . "';\n"
    . "    }\n"
    . "  }\n"
    . "\n"
    . "  function ValidateData() {\n"
    . "    var ret = false;\n"
    . "    if('mysql' == document.getElementById('type').value) {\n"
    . "      if(document.getElementById('server').value.length < 1) {\n"
    . "        alert('A server location is required.');\n"
    . "        document.getElementById('server').focus();\n"
    . "      } else if(document.getElementById('database').value.length < 1) {\n"
    . "        alert('A database name is required.');\n"
    . "        document.getElementById('database').focus();\n"
    . "      } else if(document.getElementById('username').value.length < 1) {\n"
    . "        alert('A username is required.');\n"
    . "        document.getElementById('username').focus();\n"
    . "      } else if(document.getElementById('password').value.length < 1) {\n"
    . "        alert('A password is required.');\n"
    . "        document.getElementById('password').focus();\n"
    . "      } else {\n"
    . "        ret = true;\n"
    . "      }\n"
    . "    } else if('sqlite' == document.getElementById('type').value) {\n"
    . "      if(document.getElementById('server').value.length < 1) {\n"
    . "        alert('A server path is required.');\n"
    . "        document.getElementById('server').focus();\n"
    . "      } else {\n"
    . "        ret = true;\n"
    . "      }\n"
    . "    }\n"
    . "\n"
    . "    return ret;\n"
    . "  }\n"
    . "//]]>\n"
    . "</script>\n"
    . "</head>\n"
    . "<body>\n"
    . "<div id=\"nav\" class=\"frame\">"
    . "<h1>Gregarius Database Setup</h1>\n"
    . "<fieldset class=\"install\" style=\"text-align:center\">\n"
    . "<legend>Version " . GREGARIUS_RELEASE . " - " . GREGARIUS_CODENAME . "</legend>\n"
    . "<p><img src=\"themes/default/media/installer/codename.jpg\" alt=\"".GREGARIUS_CODENAME."\" /></p>\n"
    . "</fieldset>\n"
    . "</div>"
    . "<div id=\"install\" class=\"frame\">\n"
    . "<h2>Step 1: Verify Environment</h2>\n"
    . "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\" onsubmit=\"return ValidateData();\">\n"
    . "<fieldset class=\"install\">\n"
    . "<legend>Diagnostics</legend>\n"
    . "<p>Below are some of the requirements to run Gregarius. If any are not found, please fix them before continuing.</p>\n"
    . "<p class=\"" . (version_compare(REQUIRED_VERSION, PHP_VERSION) <= 0 ? "found" : "not_found") . "\"><label>PHP Version: " . phpversion() . "</label></p>\n"
    . "<p class=\"" . ($hasSocket ? "found" : "not_found") . "\"><label>Sockets: " . ($hasSocket ? "Found" : "Not Found!") . "</label></p>\n"
    . "<p class=\"" . ($hasXML ? "found" : "not_found") . "\"><label>XML: " . ($hasXML ? "Found" : "Not Found!") . "</label></p>\n"
    . "<p class=\"" . ($hasMySQL || $hasSQLite ? "found" : "not_found") . "\"><label>Database: " . $sql . "</label></p>\n"
    . "</fieldset>\n"
    . "<h2>Step 2: Provide Database Settings</h2>\n"
    . "<fieldset class=\"install\">\n"
    . "<legend>Database Settings</legend>\n"
    . "<p>The settings below are for the database Gregarius will keep its data.</p>\n"
    . "<p><label for=\"type\">Server Type <a href=\"#\" onclick=\"ToggleHelp('type_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"radio\" style=\"display:inline\" name=\"type\" id=\"type\" value=\"mysql\" onchange=\"ToggleType(this); return false;\" " . ($hasMySQL ? "checked=\"checked\"" : "disabled=\"disabled\"") . "/>MySQL"
    . "<input type=\"radio\" style=\"display:inline\" name=\"type\" value=\"sqlite\" onchange=\"ToggleType(this); return false;\" " . ($hasSQLite ? ($hasMySQL ? "" : "checked=\"checked\"") : "disabled=\"disabled\"") . "/>SQLite"
    . "<span class=\"help\" id=\"type_help\">" . TYPE_HELP . "</span></p>\n"
    . "<p><label for=\"server\">Server Location <a href=\"#\" onclick=\"ToggleHelp('server_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"text\" name=\"server\" id=\"server\" value=\"" . SQL_SERVER_DEFAULT . "\" />"
    . "<span class=\"help\" id=\"server_help\">" . SQL_SERVER_HELP . "</span></p>\n"
    . "<p><label for=\"database\">Database Name <a href=\"#\" onclick=\"ToggleHelp('database_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"text\" name=\"database\" id=\"database\" value=\"" . DATABASE_DEFAULT . "\" />"
    . "<span class=\"help\" id=\"database_help\">" . DATABASE_HELP . "</span></p>\n"
    . "<p><label for=\"username\">Database UserName <a href=\"#\" onclick=\"ToggleHelp('username_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"text\" name=\"username\" id=\"username\" value=\"\" />"
    . "<span class=\"help\" id=\"username_help\">" . USERNAME_HELP . "</span></p>\n"
    . "<p><label for=\"password\">Database Password <a href=\"#\" onclick=\"ToggleHelp('password_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"password\" name=\"password\" id=\"password\" value=\"\" />"
    . "<span class=\"help\" id=\"password_help\">" . PASSWORD_HELP . "</span></p>\n"
    . "<p><label for=\"prefix\">Database Table Prefix <a href=\"#\" onclick=\"ToggleHelp('prefix_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"text\" name=\"prefix\" id=\"prefix\" value=\"\" />"
    . "<span class=\"help\" id=\"prefix_help\">" . PREFIX_HELP . "</span></p>\n"
    . "</fieldset>\n"
    . "<h2>Step 3: Provide Admin Settings (optional)</h2>\n"
    . "<fieldset class=\"install\">\n"
    . "<legend>Server Setup</legend>\n"
    . "<p>If you would like Gregarius to create the database and user for you, input the correct settings below.</p>\n"
    . "<p><label for=\"admin_username\">Admin UserName <a href=\"#\" onclick=\"ToggleHelp('admin_username_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"text\" name=\"admin_username\" id=\"admin_username\" value=\"\" />"
    . "<span class=\"help\" id=\"admin_username_help\">" . ADMIN_USERNAME_HELP . "</span></p>\n"
    . "<p><label for=\"admin_password\">Admin Password <a href=\"#\" onclick=\"ToggleHelp('admin_password_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"password\" name=\"admin_password\" id=\"admin_password\" value=\"\" />"
    . "<span class=\"help\" id=\"admin_password_help\">" . ADMIN_PASSWORD_HELP . "</span></p>\n"
    . "<p><label for=\"web_server\">Web Location <a href=\"#\" onclick=\"ToggleHelp('web_server_help'); return false; \">[?]</a></label>\n"
    . "<input type=\"text\" name=\"web_server\" id=\"web_server\" value=\"" . WEB_SERVER_DEFAULT . "\" />"
    . "<span class=\"help\" id=\"web_server_help\">" . WEBSERVER_HELP . "</span></p>\n"
    . "</fieldset>\n"
    . "<h2>Step 4: " . ($hasWritePerm ? "Create database and write dbinit.php" : "Create database and download dbinit.php") . "</h2>\n"
    . "<p><input type=\"submit\" name=\"action\" value=\"" . ($hasWritePerm ? "Setup Database" : "Download dbinit.php file") . "\" /></p>\n"
    . "<p><input type=\"hidden\" name=\"process\" value=\"1\" /></p>\n"
    . "</form>\n"
    . "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">\n"
    . "<h2>Step 5: Activate mod-rewrite (optional)</h2>\n"
    . "<p><input type=\"submit\" name=\"action\" value=\"" . ($hasWritePerm ? "Activate mod-rewrite" : "Download .htaccess file") . "\" /></p>\n"
    . "<p><input type=\"hidden\" name=\"process\" value=\"2\" /></p>\n"
    . "</form>\n"
    . "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">\n"
    . "<h2>Step 6: Goto Admin Section</h2>\n"
    . "<p><input type=\"submit\" name=\"action\" value=\"Start Using Gregarius!\" /></p>\n"
    . "<p><input type=\"hidden\" name=\"process\" value=\"3\" /></p>\n"
    . "</form>\n"
    . "</div>\n"
    . "</body>\n"
    . "</html>\n";
}

if(file_exists(DBINIT)) {
    print("The dbinit.php file already exists in the Gregarius directory! Please remove it if you would like to use this installer.");
} else if(!empty($_POST['process'])){
// process the post data
    if(3 == $_POST['process']) {
        header('Location: admin/');
        exit();
    } else if(2 == $_POST['process']) {
        $success = @rename('.htaccess.disabled', '.htaccess');
        if(false == $success) {
            $htaccess = file('.htaccess.disabled', 'r');
            header('Content-type: application/text');
            header('Content-Disposition: attachment; filename=".htaccess"');
            echo($htaccess);
        }

        exit();
    } else if(1 == $_POST['process']) {
        if(empty($_POST['server']) ||
            empty($_POST['database']) ||
            empty($_POST['username']) ||
            empty($_POST['password']) ||
            empty($_POST['type'])) {

            print("Not all required fields have been filled in!");
        } else {
        // create the database and user
        if(!empty($_POST['admin_username'])) {
            if("mysql" == $_POST['type']) {
                $sql = @mysql_connect($_POST['server'], $_POST['admin_username'], $_POST['admin_password']);

                if(!$sql) {
                    print("Unable to connect to database! Please create manually.");
                } else {
                    mysql_query("CREATE DATABASE " . $_POST['database'] . "", $sql);
                    mysql_query("GRANT ALL ON " . $_POST['database'] . ".* TO '" . $_POST['username'] . "'@'" . $_POST['web_server'] . "' IDENTIFIED BY '" . $_POST['password'] . "'", $sql);
                    mysql_close($sql);
                }
            } else if("sqlite" == $_POST['type']) {
                $sql = @sqlite_open($_POST['server'], 0666);

                if(!$sql) {
                    print("Unable to connect to database! Please create manually.");
                } else {
            }
            } else {
                print("Invalid SQL Type!");
                exit();
            }
        }

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
                header('Content-Disposition: attachment; filename="dbinit.php"');
                echo($out);
                exit();
            } else {
            // write the file
                fwrite($fp, $out);
                fclose($fp);

                exit();
            }
        }
    }
} else { // dbinit.php does not exist and we are not asked to process
// print out the form
    install_main();
}
?>
