# Zabbix-Screens-Controller
Makes screens navigation easy

`http://zabbix.local/www10.local&nginx` -> `http://zabbix.local/screens.php?elementid=55&hostid=10713&groupid=35`

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

---

Скрипт призван упростить навигацию по динамическим скринам Zabbix'а.

Если у вас несколько сотен/тысяч хостов/групп и на каждый хост существует стандартный скрин, то, вероятнее всего, вы "укликивались до смерти": выбрать скрин, группу, хост; а также огребали проблемы,      когда нужно рассказать, как посмотреть те или иные графики человеку, который Zabbix видит первый раз.

### Как работает
 * Nginx получает 404 ошибку, редиректит запрос в zabbix_screens_controller.php
 * zabbix_screens_controller.php парсит строчку из URL и редиректит на нужный скрин

### Как ипользовать
 * Убедитесь, что `./conf/zabbix.conf.php` доступен для чтения (или поменяйте путь), в конфиге должны быть логин-пароль под которым веб-морда ходит в базу
 * Отредактируйте $map_screens, прописав свои скрины
 * Если необходимо - поправьте $map_shorts, для ещё более быстрого доступа к нужным скринам
 * Положите zabbix_screens_controller.php в директорию с web интерфейсом zabbix
 * Пропишите директиву `error_page 404` в конфиге nginx, чтобы все неизвестные запросы роутились в наш скрипт (см.пример)

### Debug
Используйте аргумент `&debug` на случай, если что-то идёт не так (редирект не происходит, или урл формируется не валидный)

### Алиасы / дополнительные хосты
На случай, если в вашей инфраструктуре встречается несколько доменных имён, ведущих на один сервер (при условии, что дополнительные записи в zabbix не заведены), предусмотрена карта таких исключений: `$rewrites`

### Примеры
 * `http://zabbix.local/www1.local` - редирект на стандартный Linux скрин для хоста www1.local
 * `http://zabbix.local/www1.local&nginx` - редирект на скрин Nginx для того же хоста
 * `http://zabbx.local/db1.local&m` - редирект на скрин MySQL Performance (в виде короткой записи) для хоста db1.local

P.S. Если в [/etc/resolv.conf](http://linux.die.net/man/5/resolv.conf) на Zabbix сервере будут указаны нужные `domain` и `search`, все записи можно сократить до:
 * `http://zabbix/www1`
 * `http://zabbix/www1&nginx`
 * `http://zabbx/db1&m`

