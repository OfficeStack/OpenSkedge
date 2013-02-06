# OpenSkedge
[OpenSkedge](http://github.com/maxfierke/OpenSkedge)

Copyright &copy; 2012-2013 Max Fierke

# Deployment on a Local, VPS, Cloud, or Dedicated host
## Requirements
1.  Nginx, Apache, or another web server on *nix/BSD with rewrite functionality. May work on Windows and Mac, but has not been tested and is not supported.
    * Nginx users, see [this wiki article](https://github.com/maxfierke/OpenSkedge/wiki/Setting-up-on-Nginx-with-PHP-FPM-on-Linux) for setup.
    * Apache users, point your document root to web/. The .htaccess should take care of everything.
2.  PHP 5.3.10+ (haven't tested on 5.4 but it should work fine)
3.  PDO-supported database. MySQL/MariaDB suggested.
4.  (optional) Memcached and PHP memcache extension.
## Installation

0.  Run `php app/check.php` and resolve any errors before doing ANYTHING else.
1.  Run `cp app/config/parameters.yml.dist app/config/parameters.yml`
    * `app_name` holds the application branding that is displayed to the user. You     can change it to anything. E.g. CSOM ITSS sets this to "Lab Scheduler".
    * `admin_email` is the email address of the default admin account. This address should be set to whatever the email address of who ever the admin will be (probably you).
    * `week_start_day` is the day of the week which is considered the start of the week in your region
    * `week_start_clock` is the day of the week which is considered the start of the week as far as time
        clock functionality is concerned. This will likely be the same as above. Use the same format as your
        paper time sheets.
    * `secret` is used for CSRF validation. Set this to some random characters. An ideal value would be a random sha256 hash.
2.  Run `php composer.phar install`
3.  Run `php app/console doctrine:database:create`
4.  Run `php app/console doctrine:schema:update --force`
5.  Run `php app/console doctrine:fixtures:load` to bootstrap the application with some needed information (groups) and a default admin account with the username `admin` and the password `admin`.
6.  Navigate to the OpenSkedge installation in a browser, login as the bootstrapped admin and **change the password**.
7. Add employees, areas, positions, and schedule periods and get to scheduling!

## Upgrading
1.  Run `git pull` to fetch the latest changes to OpenSkedge. If you've made changes to OpenSkedge, you'll either want to stash them or commit them and use `git pull --rebase`.
2.  Run `php composer.phar install`
3.  Run by using `php app/console doctrine:migrations:migrate`. NOTE: This should be pretty safe but if issues occur, you should be able to roll back by migrating down. That said, it's probably best to test the migration on your development server before pushing it to production. Read more about using migrations at [the Doctrine project's docs](http://docs.doctrine-project.org/projects/doctrine-migrations/en/latest/index.html).
4.  Run `php app/console --env=prod cache:clear` to clear the application's cache. `prod` should be replaced with `dev` if you're running in a development environment.

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
OpenSkedge, technically a derivative work of Employee Scheduler, is available under the GNU General Public License version 2 or later.
See src/OpenSkedge/AppBundle/Resources/meta/LICENSE for more details.

### Authors and Contributors
* Copyright 2012-2013 Max Fierke (@maxfierke)
* Copyright 2012-2013 University of Minnesota - Carlson School of Manangement
* Copyright 2003 Brigham Young University (For the bits of Employee Scheduler present)

### Projects that OpenSkedge uses
* [Twitter Bootstrap](http://twitter.github.com/bootstrap/index.html) by @twitter
* [ScrollToFixed](https://github.com/bigspotteddog/ScrollToFixed/) by @bigspotteddog
* [Symfony 2.1.6](http://symfony.com/) and dependencies specified in composer.json

### Thanks to the following Employee Scheduler developers
* John Finlay (Developer of Employee Scheduler)
* Carl Allen (Developed time clock functionality for the UofMN with Joe Krall)
* Joe Krall (Developed time clock functionality for the UofMN with Carl Allen)

## Contact
**Max Fierke**  
Homepage: [www.maxfierke.com](http://www.maxfierke.com)  
Twitter: [@m4xm4n](http://twitter.com/m4xm4n)  
Github: [@maxfierke](https://github.com/maxfierke)  
