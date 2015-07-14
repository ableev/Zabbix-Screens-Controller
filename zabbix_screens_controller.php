<?php

@require_once dirname(__FILE__).'/conf/zabbix.conf.php';

# screen map, for getting elementid, http://zabbix.local/db3&mysql -> http://zabbix.local/elementid=2&hostid...
$map_screens = array(
    'default' => 'Linux server',
    'mysql' => 'MySQL Performance',
    'php_fpm' => 'PHP-FPM',
    'nginx' => 'Nginx',
);

# map for shorten names, e.g. http://zabbix.local/db3&m = http://zabbix.local/db3&mysql, etc
$map_shorts = array(
    'm' => 'mysql',
    'p' => 'php_fpm',
    'n' => 'nginx',
);

# if you have CNAMEs or a number of equal records for one hostname, http://zabbix.local/billing2 -> http://zabbix.local/bill2
$rewrites = array(
    '^dbmail' => 'dbm',
    '^exwww' => 'www',
);

# function to get hostid by host name
function get_hostid($host)
{
    $query_get_hostid = "SELECT hostid FROM hosts WHERE host = '$host'";
    $result = mysql_query($query_get_hostid) or die(mysql_error());
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $hostid = $row['hostid'];
    }
    return $hostid;
}

# function to get groupid (of host) by hostid
function get_groupid($hostid)
{
    $query_get_groupid = "SELECT groupid FROM hosts_groups WHERE hostid = $hostid";
    $result = mysql_query($query_get_groupid) or die(mysql_error());
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $groupid = $row['groupid'];
    }
    return $groupid;
}

# function to get screenid (elementid in web interface) by screen name
function get_elementid($screen)
{
    $query_get_elementid = "SELECT screenid FROM screens WHERE name = '$screen'";
    $result = mysql_query($query_get_elementid) or die(mysql_error());
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $screenid = $row['screenid'];
    }
    return $screenid;
}

$debug = isset($_GET['debug']);

if ($debug) echo "<pre>";

# mysql connection using credentials from standard zabbix configuration
$link = mysql_connect($DB['SERVER'], $DB['USER'], $DB['PASSWORD']) or die("Can't connect to MySQL: " . mysql_error());
mysql_select_db($DB['DATABASE'], $link) or die ("Could not open db".mysql_error());

# host manipulation
$host = preg_replace("/^\//", "", $_GET['host']);
$host = mysql_real_escape_string($host);

if ($debug) echo "\nOrigin host: " . $host;

# process of rewriting for hosts, see $rewrites array
foreach ($rewrites as $from => $to) {
    $host = preg_replace("/" . $from . "/", $to, $host);
}

$host = gethostbyaddr(gethostbyname($host));
$hostid = get_hostid($host);

# check if there is short name for screen
foreach ($map_shorts as $short => $screen_name) {
    if (isset($_GET["$short"])) {
        $screen = $screen_name;
        if ($debug) echo "\nGot short screen name: " . $short . ", converted to: " . $screen_name;
        break;
    }
}

if ($debug) echo "\nResult host: " . $host;

# check if there is full name for screen
if (!isset($screen)) {
    foreach ($map_screens as $screen_name => $screen_full_name) {
        if (isset($_GET[$screen_name])) {
            var_dump($screen_name);
            $screen = $screen_name;
            if ($debug) echo "\nGot full screen name: " . $screen_name;
            break;
        }
    }
}

# if short form or full screen name is not set, use default name

if (!isset($screen)) {
    $screen = "default";
}

$screen_full_name = $map_screens[$screen];

$screen_id = get_elementid($screen_full_name);

$debug = isset($_GET['debug']);

$http_host = $_SERVER['HTTP_HOST'];

$url_base = "http://$http_host/screens.php?form_refresh=1&fullscreen=0";

if (!isset($screen_id)) {
    $url = $url_base;
} else {
    if (isset($hostid)) {
        if ($debug) echo "\nhostid: " . $hostid;
        # if didn't get hostid no need to get groupid before this moment
        $groupid = get_groupid($hostid);
        if ($debug) echo "\ngroupid: " . $hostid;
        $url = $url_base . "&elementid=$screen_id&hostid=$hostid&groupid=$groupid";
    } else {
        # if you didn't get host_id, but you have screen_id, you can redirect user to screen with default host
        if ($debug) echo "\nhostid is not set";
        $url = $url_base . "&elementid=$screen_id";
    }
}

if ($debug) echo "\nResult URL: $url";
if (!$debug) header("Location: $url");

if ($debug) echo "</pre>";

?>
