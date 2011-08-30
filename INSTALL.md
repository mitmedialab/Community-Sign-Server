
Realtime Community Signage Server : Installation
========

The server codebase is written as a database driven web application in the 
[CakePHP v1.3](http://cakephp.org/) framework.

Requirements
--------

- HTTP Server (preferably Apache with mod_rewrite)
- PHP v4.3.2 or greater
- a database engine (MySQL, PostgreSQL, MS Sql Server, Oracle, SqlLite)
- SVN (to check out the code)

*Tip*: An easy way to set up a development computer is to install the 
[XAMPP program](http://www.apachefriends.org/en/xampp.html).

Installation
--------

The code is stored on Github. This document will assume you're installing on Ubuntu, with Apache2 and MySQL.  We'll also assume you're installing to `/var/www/rcss/`.  On windows you could use `C:\rcss\`.

**Set Up a Apache and MySql**

Add a virtual host for your site by editing the `/etc/apache2/sites-available/default` file on your server,adding an entry to the end like this:

```
<VirtualHost *:88>
    ServerName rcss.myserver.org
    DocumentRoot /var/www/rcss/
</VirtualHost>
```

Restart Apache to load this new setting.
```
sudo /etc/init.d/apache2 restart
```

Create a `rcss` database on your favorite tool (we use 
PhpMyAdmin). Make it utf_unicode so we can handle any language well.  Create a db account called `rcss_user`, add a password (`data4rcss`), and add all permissions for this user on the `rcss` database.

**Get and Run the Code**

1. Go into `/var/www/` and clone the code from Github into `/var/www/rcss/`
2. In that directory create a new `app/config/database.php` file (based on `app/config/database.php.template`). Set the login, password, and database properties of the `default` connection to those for the database you just created above.
3. Create a new `app/config/lost_in_boston.php` file (based on  `app/config/lost_in_boston.php.template`). Open it up and edit variables as you see fit.
4. Make all of the cache directories writable for the web user (or anyone 
if you don't care about security): `chmod -R 777 app/tmp/cache/`
5. Create the database tables by running all the migrations:
```
cd /var/www/rcss/
./cake/console/cake migrate 
```

You now should be able to hit your url (http://rcss.myserver.org) and see a website! Of course, the website will show some errors until you follow the next set of instructions about configuring your installation.

Configure Your Installation
---------

** Import features **

TBD

** Import transit stops **

There is a CakePHP shell task that can help you import transit stops from NextBus.  Simply run this command and it will populate the `stops` table with data from NextBus for a specific agency.  **Warning**: this may take a while, but it will give you status as it processes each route.
```
./cake/console/cake populate_nextbus_stops
```

** Set up con jobs **

You'll want to set up cron jobs to two things:

1. Update all the displays once a minute: `/var/www/rcss/cake/console/cake update_all`
2. Update all the calendar feeds once a day: `/var/www/rcss/cake/console/cake update_calendars`
