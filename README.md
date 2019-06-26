# Object Oriented oAuth2 Client for PHP
This repository contains a sample oAuth2 client that was written to learn about oAuth2 protocols. By using the simple base class, you can easily add more providers and use their functionalities.

## Requirements ##
* [PHP 7 or higher](http://www.php.net/)
* [mySQL](https://www.mysql.com/) or [MariaDB](https://mariadb.org/)
* [Google APIs Client Library for PHP](https://github.com/googleapis/google-api-php-client) *(if you would like to use Google)*

## Installation ##

After **downloading the release**, you need to do the following changes in order to make the script work.
* Create a database and import `./db.sql`
* Fill in the necessary database details inside `./class/db.php`
* Update the server information in `./class/server.php`
* Fill in the API keys and scopes in `./class/google.php`, `./class/spotify.php` and `./class/strava.php` (if you'd like to use them all)
* Install Google APIs Client Library for PHP if you are planning to use Google under `./class/google/`.

## Usage ##
You can enable or disable any app through the database by changing the `enabled` field of the related app. By default, the 3 providers that are included in this repository are enabled. If enabled, the related class will be included on the website.

The 3 examples in this repository are provided to show variations between different providers, or in the case of Google, how to integrate an external client.

Please note that the user registration and login system was written to make the system easy to use, you should not use them in production.


## Adding Another Provider ##

* Add a new entry on the `apps` table. The `name` field is visible to the users, and the `class` field is the name of the class, and the lowercase version of this file should be the name of the class.
* Create a copy of `./class/sample-derived-class.php` file, and make necessary changes to create the related class. This sample file provides helpful comments on how to do so.

### Example: Adding Slack ###

* The database entry should look like this: `4 | slack | Slack | 1`

* There should be a file named as `slack.php` under the class directory, and that file should have a class named as `Slack`.


## Comments, Pull Requests, Bugs ##

Please create an issue, or directly submit a pull request. They are always appreciated.
