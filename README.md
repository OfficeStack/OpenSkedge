# OpenSkedge
[![Build Status](https://travis-ci.org/maxfierke/OpenSkedge.png)](https://travis-ci.org/maxfierke/OpenSkedge)
[![githalytics.com alpha](https://cruel-carlota.pagodabox.com/d3ee5bf3c0818d24e5e13a4219cf5f2e "githalytics.com")](http://githalytics.com/maxfierke/OpenSkedge)

Created by Max Fierke

# Deployment on a Local, VPS/Cloud, Fortrabbit, or Dedicated host
## Requirements
1.  Nginx, Apache, or another web server on *nix/BSD with rewrite functionality. May work on Windows and Mac, but has not been tested and is not supported.
    * Nginx users, see [this wiki article](https://github.com/maxfierke/OpenSkedge/wiki/Setting-up-on-Nginx-with-PHP-FPM-on-Linux) for setup.
    * Apache users, point your document root to web/. The .htaccess should take care of everything.
2.  PHP 5.3.10+ (Tested on 5.3.10, 5.3.18, and 5.4.6)
3.  PDO-supported database. MySQL/MariaDB suggested.
4.  [Composer](http://getcomposer.org) for installing dependencies
5.  (optional) Memcached and PHP memcache extension.

## Installation
1.  Run `php app/check.php` and resolve any errors before doing ANYTHING else.
2.  Run `cp app/config/parameters.yml.dist app/config/parameters.yml`
    * `sender_email` is the email address of the automated email account you want to use.
    * `secret` is used for CSRF validation. Set this to some random characters. An ideal value would be a random sha256 hash.
    * The rest of the settings should be pretty self-explainatory.
3.  Setup permissions. This will require ACL support of some kind on your file system. Replace `www-data` with your web server user.
    * If under a host that supports `chmod +a`:<pre>
        $ rm -rf app/cache/*
        $ rm -rf app/logs/*
        $ sudo chmod +a "www-data allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs
        $ sudo chmod +a "\`whoami\` allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs</pre>
    * If under a host that does not, enable ACL support on the filesystem and run the following:<pre>
        $ sudo setfacl -R -m u:www-data:rwX -m u:\`whoami\`:rwX app/cache app/logs
        $ sudo setfacl -dR -m u:www-data:rwx -m u:\`whoami\`:rwx app/cache app/logs</pre>
    * If none of the above are available options, add `umask(0002);` to the beginning of app/console, web/app.php, and web/app_dev.php
4.  Run `php composer.phar install --prefer-dist`
5.  Run `php app/console doctrine:database:create` if you have not already created a database for OpenSkedge.
6.  Run `php app/console doctrine:schema:update --force`
7.  Run `php app/console doctrine:fixtures:load` to bootstrap the application with some needed information (groups) and a default admin account with the username `admin` and the password `admin`.
8.  Run `php app/console --env=prod cache:clear` to clear and warmup the application's cache. `prod` should be replaced with `dev` if you're running in a development environment.
9.  Run `php app/console --env=prod assets:install` to install Assetic assets into the web root. `prod` should be replaced with `dev` if you're running in a development environment.
10.  Navigate to the OpenSkedge installation in a browser, login as the bootstrapped admin and **change the password**.
11.  Add employees, areas, positions, and schedule periods and get to scheduling!

## Upgrading
1.  Run `git pull` to fetch the latest changes to OpenSkedge. If you've made changes to OpenSkedge, you'll either want to stash them or commit them and use `git pull --rebase`.
2.  Run `php composer.phar install`
3.  Run by using `php app/console doctrine:migrations:migrate`. NOTE: **Only supports MySQL**. This should be pretty safe but if issues occur, you should be able to roll back by migrating down. That said, it's probably best to test the migration on your development server before pushing it to production. Read more about using migrations at [the Doctrine project's docs](http://docs.doctrine-project.org/projects/doctrine-migrations/en/latest/index.html).
    *   If you're on a database other than MySQL such as PostgreSQL, you'll have to adapt the migrations yourself, or risk **potential data loss** and/or application breakage by running `php app/console doctrine:schema:update --force`.
4.  Run `php app/console --env=prod cache:clear` to clear and warmup the application's cache. `prod` should be replaced with `dev` if you're running in a development environment.
5.  Run `php app/console --env=prod assets:install` to install Assetic assets into the web root. `prod` should be replaced with `dev` if you're running in a development environment.

# Deployment on Pagoda Box
Pagoda Box is a PaaS provider that allows the creation of scalable instances of web applications.
See README.Pagoda.md for Pagoda Box specific installation instructions.
I have also [created a quickstart](https://pagodabox.com/cafe/m4xm4n/openskedge) that can be used as well.

## FAQ
### What is OpenSkedge?
OpenSkedge is a reinvention of [Employee Scheduler](http://empscheduler.sourceforge.net), a flexible employee scheduling application designed for companies and organizations (such as education institutions with student workers) which require fluid shift scheduling.

### What is OpenSkedge not?
OpenSkedge is not a general purpose, or a one-stop everything-but-the-kitchen-sink scheduling application. It's for assigning positions to people at certain times during certain time periods that they have marked themselves available. It won't schedule your meetings (at least, not yet).

### Who is behind OpenSkedge?
[Max Fierke](http://www.maxfierke.com), an open-source developer and student at the University of Minnesota. He does [a lot of other stuff](https://github.com/maxfierke/) too. He created OpenSkedge during his winter break. He also has [a site you can go to](http://www.maxfierke.com).

### Why was OpenSkedge created?
Max's boss, Garreth, was getting very annoyed at the fact that the version of Employee Scheduler they were running was severely broken. Time clock functionality was broken. Employees couldn't be scheduled past certain hours. Max mentioned that he knew PHP and MySQL. Garreth told him to look at it to see if he could figure out why everything was falling apart. Max spent two months picking the application apart and re-implemented parts of the aged PHP 4 application using PHP 5 constructs such as object-oriented classes and PHP Data Objects. The first weekend of winter break, Max got fed up with the archaic codebase and began re-implementing the application in Symfony2.

### It sounds like OpenSkedge is basically an entirely different application than Employee Scheduler. What is left of it in OpenSkedge?
Max isn't a JavaScript developer or a UI designer, so much of the JavaScript used in the interface of the application is nearly unmodified from the JavaScript included in Employee Scheduler. The database design is similar to Employee Scheduler but simpler and smarter (sorry!).

### Who is Carlnater McStrangelove?
The origin of Carlnater McStrangelove is not completely clear. He was present in some of the code for Lab Scheduler (Carlson School of Management's version of Employee Scheduler) and was the sender of all automated Lab Scheduler emails.

## License and Copyright
### License
OpenSkedge, technically a derivative work of Employee Scheduler, is available under the GNU General Public License version 3 or later.
See src/OpenSkedge/AppBundle/Resources/meta/LICENSE for more details.

### Authors and Contributors
* Copyright 2012-2013 Max Fierke (@maxfierke)
* Copyright 2012-2013 University of Minnesota - Carlson School of Manangement
* Copyright 2003 Brigham Young University (For the bits of Employee Scheduler present)

### Projects that OpenSkedge uses
* [Twitter Bootstrap](http://twitter.github.com/bootstrap/index.html) by @twitter
* [TODC Bootstrap](https://github.com/todc/todc-bootstrap) by @todc
* [TODC Datepicker](https://github.com/todc/todc-datepicker) by @todc
* [Colorpicker for Bootstrap](http://www.eyecon.ro/bootstrap-colorpicker/) by Stefan Petre
* [TableSorter](http://tablesorter.com/) by [@lovepeacenukes](https://twitter.com/lovepeacenukes)
* [Symfony 2.2](http://symfony.com/), [Doctrine](http://www.doctrine-project.org/) and dependencies specified in composer.json

### Thanks to the following Employee Scheduler developers
* John Finlay (Developer of Employee Scheduler)
* Carl Allen (Developed time clock functionality for the UofMN with Joe Krall)
* Joe Krall (Developed time clock functionality for the UofMN with Carl Allen)

## Contact
**Max Fierke**  
Homepage: [www.maxfierke.com](http://www.maxfierke.com)  
Twitter: [@m4xm4n](http://twitter.com/m4xm4n)  
Github: [@maxfierke](https://github.com/maxfierke)  


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/maxfierke/openskedge/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

