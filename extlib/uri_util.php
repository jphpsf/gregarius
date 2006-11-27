<?php
# uri_util.php - URI utilities
# Version 0.7  Sat, 25 Nov 2006 17:33:25 -0600
#
# Copyright (C) 2006  Dwayne C. Litzenberger <dlitz@dlitz.net>
# 
# Permission is hereby granted, free of charge, to any person obtaining
# a copy of this software and associated documentation files (the
# "Software"), to deal in the Software without restriction, including
# without limitation the rights to use, copy, modify, merge, publish,
# distribute, sublicense, and/or sell copies of the Software, and to
# permit persons to whom the Software is furnished to do so.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
# "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
# LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
# A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
# OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
# SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
# LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
# DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
# THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
# OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


function parse_uri($uri)
{
    if (substr($uri, 0, 2) == '//') {
        # Work around PHP parse_url bug.  PHP doesn't like URIs like
        # "//example.com/foo" or "//example.com:80/foo"
        $parts = parse_url('x-dummy-scheme:'.$uri);
        unset($parts['scheme']);
        return $parts;
    }
    return parse_url($uri);
}

function unparse_url($parts, $loose=false)
{
    return unparse_uri($parts, $loose);
}

# This function is indended to be the inverse of parse_url, with some optional
# sanity checks against RFC 3986.
function unparse_uri($parts, $loose=null) 
{
    if (is_null($loose)) { $loose = true; }

    $p_scheme = @$parts['scheme'];
    $p_host = @$parts['host'];
    $p_port = @$parts['port'];
    $p_user = @$parts['user'];
    $p_pass = @$parts['pass'];
    $p_path = @$parts['path'];
    $p_query = @$parts['query'];
    $p_fragment = @$parts['fragment'];
    
    if (!$loose) {
        $dec_octet = '(?:\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])';
        $IPv4address = "(?:$dec_octet\\.$dec_octet\\.$dec_octet\\.$dec_octet)";
        $h16 = '(?:[[:xdigit:]]{1,4})';
        $ls32 = '(?:'.$h16.':'.$h16.'|'.$IPv4address.')';
        $IPv6address = "(?:".
                                     "(?:$h16:){6}$ls32|".
                                   "::(?:$h16:){5}$ls32|".
                              "$h16?::(?:$h16:){4}$ls32|".
            "(?:(?:$h16:){0,1}$h16)?::(?:$h16:){3}$ls32|".
            "(?:(?:$h16:){0,2}$h16)?::(?:$h16:){2}$ls32|".
            "(?:(?:$h16:){0,3}$h16)?::(?:$h16:){1}$ls32|".
            "(?:(?:$h16:){0,4}$h16)?::" .        "$ls32|".
            "(?:(?:$h16:){0,5}$h16)?::" .        "$h16|".
            "(?:(?:$h16:){0,6}$h16)?::".
            ")";

        $unreserved = '[[:alpha:]\d\-\._~]';
        $sub_delims = "[!\$&'()\*\+,;=]";
        $IPvFuture = "(?:v[[:xdigit:]]+\\.[$unreserved$sub_delims\\:]+)";
        $IP_literal = "(?:\\[(?:$IPv6address|$IPvFuture)\\])";
        $pct_encoded = "(?:%[[:xdigit:]]{2})";
        $reg_name = "(?:$unreserved|$pct_encoded|$sub_delims)*";
        $pchar = "(?:$unreserved|$pct_encoded|$sub_delims|\:@)";
        $segment = "$pchar*";
        $segment_nz = "$pchar+";
        $path_absolute = "(?:/(?:$segment_nz(?:/$segment)*)?)";
        $path_rootless = "$segment_nz(?:/$segment)*";
        
        # Validate the scheme part
        #  scheme        = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." )
        # NB: "file" is hard-coded in PHP
        if (isset($p_scheme) and 
            !preg_match('|^[[:alpha:]][[:alpha:]\d\+\-\.]*$|s', $p_scheme))
        {
            trigger_error('Illegal URI scheme', E_USER_WARNING);
            return false;
        }
        
        # Validate the host part
        if (isset($p_host) and 
            !preg_match("#^(?:$IP_literal|$IPv4address|$reg_name)\$#s",
                $p_host))
        {
            trigger_error('Illegal host part', E_USER_WARNING);
            return false;
        }
        
        # Validate the port part
        if (isset($p_port) and
            !preg_match("#^\d*\$#s", $p_port))
        {
            trigger_error('Illegal port part', E_USER_WARNING);
            return false;
        }    
        
        # Validate the user part
        if (isset($p_user) and
            !preg_match("#^(?:$unreserved|$pct_encoded|$sub_delims)*\$#s",
                $p_user))
        {
            trigger_error('Illegal user part', E_USER_WARNING);
            return false;
        }
        
        # Validate the password part
        if (isset($p_pass) and
            !preg_match("#^(?:$unreserved|$pct_encoded|$sub_delims|:)*\$#s",
                $p_pass))
        {
            trigger_error('Illegal pass part', E_USER_WARNING);
            return false;
        }    
        
        # Validate the path part
        if (isset($p_path) and
            !preg_match("#^$path_absolute|$path_rootless\$#s", $p_path))
        {
            trigger_error('Illegal path part', E_USER_WARNING);
            return false;
        }    
     
        # Validate the query part
        if (isset($p_query) and
            !preg_match("#^(?:$pchar|/|\?)*\$#s", $p_query))
        {
            trigger_error('Illegal query part', E_USER_WARNING);
            return false;
        }    
         
        # Validate the fragment part
        if (isset($p_fragment) and
            !preg_match("#^(?:$pchar|/|\?)*\$#s", $p_fragment))
        {
            trigger_error('Illegal fragment part', E_USER_WARNING);
            return false;
        }    
    }

    # Build the URI
    $retval = "";
    if (isset($p_scheme)) {
        $retval = $p_scheme . ":";
        if (strtolower($p_scheme) == "file" and !isset($p_host)) {
            $retval .= "//";
        }
    }
    if (isset($p_host)) {
        $retval .= "//";
        if (isset($p_user) or isset($p_pass)) {
            $retval .= isset($p_user) ? $p_user : "";
            $retval .= isset($p_pass) ? ":" . $p_pass : "";
            $retval .= '@';
        }
        $retval .= $p_host;
        if (isset($p_port)) {
            $retval .= ':' . $p_port;
        }
    }
    if (isset($p_path)) {
        $retval .= $p_path;
    }
    if (isset($p_query)) {
        $retval .= '?' . $p_query;
    }
    if (isset($p_fragment)) {
        $retval .= '#' . $p_fragment;
    }
    return $retval;
}

function get_current_url()
{
    $host = $_SERVER['SERVER_NAME'];
    $port = $_SERVER['SERVER_PORT'];
    $req_uri = $_SERVER['REQUEST_URI'];
    $https = !empty($_SERVER['HTTPS']);
    $parts = array(
        'scheme' => ($https ? 'https' : 'http'),
        'host' => $host,
        'port' => $port);
    if (($https and $port == 443) or (!$https and $port == 80)) {
        unset($parts['port']);
    }
    $uri = unparse_url($parts) . $req_uri;
    return $uri;
}

function absolute_url($uri, $base_absolute_uri=null)
{
    return absolute_uri($uri, $base_absolute_uri);
}

# See RFC 3986, section 5.2.
# This is a "strict parser" for the purposes of the RFC.
# Note that $base_absolute_uri MUST be an absolute URI, or null
function absolute_uri($uri, $base_absolute_uri=null)
{
    if (is_null($base_absolute_uri)) {
        $base_absolute_uri = get_current_url();
    }
    
    # 5.2.1. Pre-parse the base URI
    $base_absolute_uri = normalize_uri($base_absolute_uri);
    
    # 5.2.2 Transform References
    $base = parse_uri($base_absolute_uri);
    $r = parse_uri($uri);
    $target = array();
    
    if (isset($r['scheme'])) {
        $target['scheme'] = $r['scheme'];
        $target['path'] = remove_dot_segments(remove_multiple_slashes($r['path']));
        $target['query'] = @$r['query'];
        
        // conceptually, $target['authority'] = @$r['authority'];
        $target['host'] = @$r['host'];
        $target['port'] = @$r['port'];
        $target['user'] = @$r['user'];
        $target['pass'] = @$r['pass'];
    } else {
        // conceptually, if (isset($r['authority'))
        if (isset($r['host'])) {
            $target['path'] = remove_dot_segments(remove_multiple_slashes($r['path']));
            $target['query'] = @$r['query'];
            
            // conceptually, $target['authority'] = @$r['authority'];
            $target['host'] = @$r['host'];
            $target['port'] = @$r['port'];
            $target['user'] = @$r['user'];
            $target['pass'] = @$r['pass'];
        } else {
            if (empty($r['path'])) {
                $target['path'] = $base['path'];
                if (isset($r['query'])) {
                    $target['query'] = $r['query'];
                } else {
                    $target['query'] = $base['query'];
                }
            } else {
                if (substr($r['path'], 0, 1) === '/') {
                    $target['path'] = remove_dot_segments(remove_multiple_slashes($r['path']));
                } else {
                    // conceptually, $target['path'] = merge($base['path'], $r['path']);
                    if (isset($base['host']) and empty($base['path'])) {
                        $target['path'] = "/" . $r['path'];
                    } else {
                        $segs = explode('/', $base['path']);
                        array_pop($segs);
                        $target['path'] = implode('/', $segs) . "/" . $r['path'];
                    }
                    
                    $target['path'] = remove_dot_segments(remove_multiple_slashes($target['path']));
                }
                $target['query'] = @$r['query'];
            }
            
            // conceptually, $target['authority'] = @$r['authority'];
            $target['host'] = @$base['host'];
            $target['port'] = @$base['port'];
            $target['user'] = @$base['user'];
            $target['pass'] = @$base['pass'];
        }
        $target['scheme'] = @$base['scheme'];
    }

    $target['fragment'] = @$r['fragment'];

    return unparse_uri($target);
}


# - When $no_empty_uri is true (the default), then zero-length relative URIs
# (which indicate the "current document") will never be returned. This is to
# avoid bugs in some programs.
# - When $no_net_uri is true (the default), then network URIs (e.g.
# "//www.example.com/foo") are never returned.  This is to avoid bugs in some
# programs.

function relative_uri($uri, $base_uri=null,
    $no_empty_uri=null, $no_net_uri=null)
{
    if (is_null($base_uri)) { $base_uri = get_current_url(); }
    if (is_null($no_empty_uri)) { $no_empty_uri = true; }
    if (is_null($no_net_uri)) { $no_net_uri = true; }

    $base_uri = absolute_uri($base_uri);
    $uri = absolute_uri($uri, $base_uri);
    
    $base = parse_uri($base_uri);
    $parts = parse_uri($uri);

    do {
        // scheme
        if ($parts['scheme'] !== $base['scheme']) {
            break;
        }
        if (!$no_net_uri) {
            unset($parts['scheme']);
        }

        // authority
        if ($parts['host'] !== $base['host'] or
            $parts['user'] !== $base['user'] or
            $parts['pass'] !== $base['pass'] or
            $parts['port'] !== $base['port'])
        {
            break;
        }
        unset($parts['scheme']);
        unset($parts['host']);
        unset($parts['user']);
        unset($parts['pass']);
        unset($parts['port']);

        // path
        if ($parts['path'] === $base['path']) {
            // Take just the basename of the path
            $p = explode('/', $parts['path']);
            if ($no_empty_uri) {
                $parts['path'] = $p[count($p)-1];
                if ($parts['path'] == '') {
                    $parts['path'] = './';
                }
            } else {
                $parts['path'] = '';
            }

            // query
            if ($parts['query'] !== $base['query']) {
                break;
            }
            unset($parts['query']);
            
            // fragment
            if ($parts['fragment'] !== $base['fragment']) {
                break;
            }
            unset($parts['fragment']);
            break;
        }

        // Relative path calculation algorithm:
        // We have two paths, the destination path (where we want to go), and
        // the base path (where we are coming from).  So, we need to:
        // 1. Find the deepest common parent between the two paths
        // 2. Determine how many instances of "../" we need to get from the
        // base path to the parent
        // 3. Determine the relative path of the destination path with respect
        // to the parent path, and append this to the "../" sequence.

        // break down the path
        $p = explode('/', $parts['path']);  // destination path
        $bp = explode('/', $base['path']);  // base path
        
        // Determine the tree depth of each of the paths
        $pDepth = count($p)-1;
        $bpDepth = count($bp)-1;
        
        // 1. Find the depth of the deepest common parent (DCP) path between
        // the two paths, noting that the final path element is not a directory.
        $n = min(count($p), count($bp));
        $dcpDepth = 0;
        for($i = 1; $i < $n-1; $i++) {
            if ($p[$i] !== $bp[$i]) {
                break;
            }
            $dcpDepth = $i;
        }
        
        // 2. Determine the number of "../"s needed to get from the base path
        // to the DCP path.
        $go_up = $bpDepth - $dcpDepth - 1;
        if ($go_up > 0) {
            $relpath = str_repeat('../', $go_up);
        } else {
            $relpath = '';
        }
        
        // 3. Determine the relative path of the destination wrt the DCP path,
        // and add it.
        $relpath .= implode('/', array_slice($p, $dcpDepth + 1));
        
        if ($relpath == '') {
            $relpath = './';
        }
        $parts['path'] = $relpath;
        
    } while(0);
    
   
    return unparse_uri($parts);
}

// URI normalization.
// See RFC 3986 section 6 (but note that we don't do everything specified
// there.)
function normalize_uri($uri) {
    // Make sure file URIs have either 0 or 3 slashes after the colon
    if (strtolower(substr($uri, 0, 6)) == 'file:/') {
        $uri = substr($uri, 0, 5) . '///' . substr($uri, 6);
    }

    $u = parse_uri($uri);

    // Convert the scheme name to lowercase
    if (isset($u['scheme'])) {
        $u['scheme'] = strtolower($u['scheme']);
    }
    
    // Convert the host part to lowercase
    if (isset($u['host'])) {
        $u['host'] = strtolower($u['host']);
    }
    
    // Remove multiple slashes (it's technically invalid URI syntax anyway)
    $u['path'] = remove_multiple_slashes($u['path']);
    
    // 6.2.2.3. Path Segment Normalization (of absolute paths)
    $u['path'] = remove_dot_segments($u['path']);

    // 6.2.3. Scheme-Based Normalization
    if (isset($u['scheme'])) {

        if ($u['scheme'] == 'http') {
            if (empty($u['port']) or $u['port'] == 80) {
                unset($u['port']);
            }
            
            if (empty($u['path'])) {
                $u['path'] = '/';
            }
        
        } elseif ($u['scheme'] == 'https') {
            if (empty($u['port']) or $u['port'] == 443) {
                unset($u['port']);
            }
            
            if (empty($u['path'])) {
                $u['path'] = '/';
            }
        
        } elseif ($u['scheme'] == 'ftp') {
            if (empty($u['port']) or $u['port'] == 21) {
                unset($u['port']);
            }
            
            if (empty($u['path'])) {
                $u['path'] = '/';
            }

        }
    }

    return unparse_uri($u);
}

// Remove multiple slashes (it's technically invalid URI syntax anyway)
function remove_multiple_slashes($path)
{
    while (strpos($path, '//') !== FALSE) {
        $path = str_replace('//', '/', $path);
    }
    return $path;
}

// RFC 3986 section 5.2.4 remove_dot_segments algorithm
function remove_dot_segments($uri)
{
    // Create an array of segments-or-slashes for the input buffer
    $inbuf = array();
    foreach(explode('/', $uri) as $seg) {
        $inbuf[] = '/';
        if (!empty($seg)) {
            $inbuf[] = $seg;
        }
    }
    array_shift($inbuf);

    $outbuf = array();
    
    while(!empty($inbuf)) {
        if (($inbuf[0] === '..' or $inbuf[0] === '.') and $inbuf[1] === '/') {
            array_splice($inbuf, 0, 2);
            
        } elseif (array_slice($inbuf, 0, 2) === array('/', '.')) {
            if ($inbuf[2] === '/') {
                array_splice($inbuf, 0, 3, array('/'));
            } else {
                array_splice($inbuf, 0, 2, array('/'));
            }
        
        } elseif (array_slice($inbuf, 0, 2) === array('/', '..')) {
            if ($inbuf[2] === '/') {
                array_splice($inbuf, 0, 3, array('/'));
            } else {
                array_splice($inbuf, 0, 2, array('/'));
            }
            if (!empty($outbuf)) {
                array_pop($outbuf);
                if (!empty($outbuf) and $outbuf[count($outbuf)-1] === '/') {
                    array_pop($outbuf);
                }
            }
        } elseif ($inbuf === array('.') or $inbuf === array('..')) {
            array_pop($inbuf);
        
        } else {
            if ($inbuf[0] === '/') {
                array_splice($outbuf, count($outbuf), 0, array_splice($inbuf, 0, 2));
            } else {
                // equivalent to array_push($outbuf, array_shift($inbuf));
                array_splice($outbuf, count($outbuf), 0, array_splice($inbuf, 0, 1));
            }
        
        }
        
    }
    
    return implode('', $outbuf);
}

// Split and decode query string (foo=bar&blah=baz&...) into its constituent
// parts
function parse_query_string($qs)
{
    $qs = explode('&', $qs);
    $retval = array();
    foreach ($qs as $a) {
        $b = explode('=', $a, 2);
        $k = urldecode($b[0]);
        $v = urldecode($b[1]);
        $retval[$k] = $v;
    }
    return $retval;
}

function unparse_query_string($query)
{
    $qs = array();
    foreach($query as $k => $v) {
        $qs[] = rawurlencode($k) . '=' . rawurlencode($v);
    }
    return implode('&', $qs);
}

function set_query_string_vars($uri, $array)
{
    $parsed_uri = parse_uri($uri);
    if (empty($parsed_uri['query'])) {
        $qs = array();
    } else {
        $qs = parse_query_string($parsed_uri['query']);
    }
    foreach ($array as $key => $value) {
        if (is_null($value)) {
            unset($qs[$key]);
        } else {
            $qs[$key] = $value;
        }
    }
    $parsed_uri['query'] = unparse_query_string($qs);
    return unparse_uri($parsed_uri);
}

function set_query_string_var($uri, $key, $value)
{
    return set_query_string_vars($uri, array($key => $value));
}

function percent_encode($s)
{
    $retval = array();
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        $ch = substr($s, $i, 1);
        $hexch = bin2hex($ch);
        if (strlen($hexch) != 2) {
            die("Multi-byte characters not supported");
        }
        $retval[] = '%' . $hexch;
    }
    return strtoupper(implode('', $retval));
}

function ensure_printable_uri($uri)
{
    $pct_encode_func = create_function('$m', 'return percent_encode($m[1]);');

    # Replace whitespace, unprintable and non-ASCII characters with percent-encoded equivalents
    //Unnecessary due to the subsequent substitution.
    //$uri = preg_replace_callback('#([^\x21-\x7e]+)#s', $pct_encode_func, $uri);
    
    # From RFC 3986:
    #   unreserved = ALPHA / DIGIT / "-" / "." / "_" / "~"
    #   gen-delims = ":" / "/" / "?" / "#" / "[" / "]" / "@"
    #   sub-delims = "!" / "$" / "&" / "'" / "(" / ")" / "*" / "+" / "," / ";" / "="
    # Replace anything that is not
    #   unreserved / gen-delims / sub-delims / "%"
    # that is,
    #   DQUOTE / "<" / ">" / "\" / "^" / "`" / "{" / "|" / "}"
    # with its percent-encoded equivalent.
    $uri = preg_replace_callback('#([^A-Za-z0-9\-._~:/?\#\[\]@!$&\'()*+,;=%]+)#s', $pct_encode_func, $uri);

    return $uri;
}

/* vim:set ts=4 sw=4 sts=4 expandtab: */
