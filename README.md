# iTunesConnect App Installs Badge

![App Installs](http://sortons.ie/events/github/appinstalls.php)
 
I wanted a badge for my GitHub that would show the number of installs my app has. Apple provides API access to some iTunesConnect data through [Reporter](https://help.apple.com/itc/appsreporterguide/#/itcbe21ac7db) which comes as a Java app. Some kind soul has ported this to PHP over at [mikebarlow/itc-reporter](https://github.com/mikebarlow/itc-reporter). I've used this library to query Apple for my app install data and use it to create a badge using [Shields.io](http://shields.io/) API. 

### Requirements: 

* PHP hosting somewhere

### Setup

* Download and use Reporter.jar to [find your Account ID](https://help.apple.com/itc/appsreporterguide/#/itcccef1d795)
* Clone this repo locally
* Install [Composer](https://getcomposer.org/download/) into the folder
* Run `php composer.phar update` to pull in the dependency
* Create a file `Reporter.properties` with the following format:

```
UserId=myappleid@email.com
Password=uset-wofa-ctora-uth!
Account=123456789
SKUs=com.app.relevant
```

SKUs is a comma separated list of Bundle Identifiers that match from the begining. i.e. if you set it as `com.myapp.first,com.myapp.second` then `com.myapp.first.*` and `com.myapp.second.*` will match.

* Upload `appinstalls.php` and the `vendor` folder to your hosting
* Upload `Reporter.properties` to a subdirectory called `reporter`
* Chmod 777 the `reporter` directory so the script can write the cache file
* To block anyone from accessing `Reporter.properties`, add a `.htaccess` file to the `reporter` directory  containing:

```
Order deny,allow
Deny from all
```

* In your repo `README.md` add `![App Installs](http://yourwebserver.com/path/appinstalls.php)`

## How it works

It loops through the years previous to the current year until it finds no app installs, then loops through the current year's complete months, then the current month's days, adding everything as it goes. It writes to a cache.txt that refreshes every 24 hours. It then returns a 302 redirect to the Shields.io badge image URL.

It's very slow to run so needs to be run on a schedule. You might be able to set up a cron job in your hosting control panel, otherwise use a service like [easycron.com](https://www.easycron.com)

## Status

This was something I threw together on a Saturday evening. Calling my PHP rusty would be a compliment. I hope others find this useful and I can see where there might be customisations wanted, so please, if you adapt it, send a pull request. If an actual PHP dev looks at this, please don't hesitate to rewrite it with modern conventions; it should hopefully facilitate contributions. If there's an easier way to set this up, send a PR for the README. Also, I think my chmod 777 might be too liberal.

I haven't checked the figures this returns. i.e. it seems to return v1.0 installs and v1.1 installs, but I'm not sure how updates are considered, so there may be double counting.

I'd love to know if people are using it, so star the repo if you do.

## Licence

[MIT to be permissible](https://github.com/BrianHenryIE/iTunesConnect-App-Installs-Badge/blob/master/LICENCE)	
