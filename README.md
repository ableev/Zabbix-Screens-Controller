# Zabbix-Screens-Controller
Makes screens navigation easy

http://zabbix.local/www10.local&nginx -> http://zabbix.local/screens.php?elementid=55&hostid=10713&groupid=35

The script is created to simplify navigation between Zabbix screens.
If you have hundreds/thousands hosts/groups and you have default screen for every host, you probably have "click until death" problem. There also might be problems with your collegues, who don't know anything about zabbix interface.

### How it works
 * Nginx catches 404 errors, redirects request to `zabbix_screens_controller.php`
 * `zabbix_screens_controller.php` parses URL and redirects to specific screen

### How to use it
 * Make sure that `./conf/zabbix.conf.php` is available for reading (or change the path), it must contain login and password that are used for zabbix web interface
 * Edit `$map_screens`, by writing your screens
 * Edit `$map_shorts`, if necessary, for quicker access to specific screens
 * Put `zabbix_screens_controller.php` into the zabbix web interface directory
 * For route unknown requests to our controller, add directive `error_page 404` to `nginx.conf` (see example)

### Debug
Use argument `&debug` in case something wrong is happening (redirect doesn't work; URL is not valid).

### Aliases / additional hosts
In case your infrastructure has multiple domain records for one server (and these records are not added to zabbix) you can use the map of `$rewrites`

### Examples
 * `http://zabbix.local/www1.local` - redirects to default 'Linux' screen for host www1.local
 * `http://zabbix.local/www1.local&nginx` - redirects to 'Nginx' screen for the same host
 * `http://zabbx.local/db1.local&m` - redirects to 'MySQL Performance' screen (as a shortage) for host db1.local

P.S. If [/etc/resolv.conf](http://linux.die.net/man/5/resolv.conf) on Zabbix server contains correct `domain` and `search`, all requests can be minimized to:
 * `http://zabbix/www1`
 * `http://zabbix/www1&nginx`
 * `http://zabbx/db1&m`
