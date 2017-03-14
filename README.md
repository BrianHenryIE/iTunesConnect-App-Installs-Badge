# iTunesConnect App Installs Badge

![App Installs](http://sortons.ie/events/github/appinstalls/appinstalls.svg)
 
I wanted a badge for my GitHub that would show the number of installs my app has. Apple provides API access to some iTunesConnect data through [Reporter](https://help.apple.com/itc/appsreporterguide/#/itcbe21ac7db) which comes as a Java app. Some kind soul has ported this to PHP over at [mikebarlow/itc-reporter](https://github.com/mikebarlow/itc-reporter). I've used this library to query Apple for my app install data and use it to create a badge using [Shields.io](http://shields.io/) API. 

### Requirements: 

* PHP hosting somewhere

### Setup:

* Download and use Reporter.jar to [find your Account ID](https://help.apple.com/itc/appsreporterguide/#/itcccef1d795)
* Clone this repo locally
* Install [Composer](https://getcomposer.org/download/) into the folder
* Run `php composer.phar update` to pull in the dependency
* Create a file `Reporter.properties` with the following format:

```
UserId=myappleid@email.com
Password=uset-wofa-ctor-auth
Account=123456789
SKUs=com.app.relevant
```

SKUs is a comma separated list of Bundle Identifiers that match from the begining. i.e. if you set it as `com.myapp.first,com.myapp.second` then `com.myapp.first.*` and `com.myapp.second.*` will match.

* Create a folder in your hosting
* Chmod 777 the folder
* Upload `appinstalls.svg.php`, `Reporter.properties` and the `vendor` folder
* Rename `appinstalls.svg.php` to `appinstalls.svg`
* To block anyone from accessing `Reporter.properties`, and to enable the `.svg` to run as a script, add a `.htaccess` file to the directory  containing:

```
Order Allow,Deny
<FilesMatch "appinstalls.svg">
Allow from all
</FilesMatch>
AddHandler application/x-httpd-php .svg
```

* In your repo `README.md` add `![App Installs](http://yourwebserver.com/path/appinstalls.svg)`

## How it works

It loops through the years previous to the current year until it finds no app installs, then loops through the current year's complete months, then the current month's days, filtering by [Product Type Identifiers](http://help.apple.com/itc/appssalesandtrends/#/itc2c006e6ff) and adding as it goes. It writes to a cache.txt that refreshes every 24 hours. It then returns a 302 redirect to the Shields.io badge image URL.

## Status

It's curently set to ignore any [Product Type Identifiers](http://help.apple.com/itc/appssalesandtrends/#/itc2c006e6ff) that are redownloads, updates or in-app purchases. I'm not certain that there's no overlap in the remaining – for my app, I get a difference of ~6% depending if I include all the 1* Product Type Identifiers. The count also went down one day, for resaons I'm yet to investigate.

Please send PRs for documentation, code style (I'm not a PHP dev) and customisations.

I'd love to know if people are using it, so star the repo if you do.

## Licence

[MIT to be permissible](https://github.com/BrianHenryIE/iTunesConnect-App-Installs-Badge/blob/master/LICENCE)	
