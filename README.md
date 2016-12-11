# iTunesConnect App Installs Badge

![App Installs](http://sortons.ie/events/github/appinstalls.php)
 
I wanted a badge for my GitHub that would show the number of installs my app has. Apple provides API access to some iTunesConnect data through [Reporter](https://help.apple.com/itc/appsreporterguide/#/itcbe21ac7db) which comes as a Java app. Some kind soul has ported this to PHP over at [mikebarlow/itc-reporter](https://github.com/mikebarlow/itc-reporter). I've used this library to query Apple for my app install data and use it to create a badge using [Sheilds.io](http://shields.io/) API. 

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
Password=ahar-dtog-uess-pass
Account=123456789
SKUs=com.app.relevant
```

SKUs is a comma separated list of Bundle Identifiers that match from the begining. i.e. if you set it as `com.myapp.first,com.myapp.second` then `com.myapp.first.*` and `com.myapp.second.*` will match.

* Upload `appinstalls.php` to your hosting
* Upload `Reporter.properties` to a subdirectory called `reporter`
* chmod 777 the directory so the script can write the `appinstalls.txt` cache file (this works but may be too liberal, I'm open to correction)
* To block anyone from accessing `Reporter.properties`, add a `.htaccess` file to the `reporter` directory  containing:

```
Order deny,allow
Deny from all
```

* In your repo `README.md` add `![App Installs](http://yourwebserver.com/path/appinstalls.php)`

## How it works

It loops through the years previous to the current year until it finds no app installs, then loops through the current year's complete months, then the current month's days, adding everything as it goes. It writes to a cache.txt that refreshes every 24 hours. If the cache is fresh, it returns a 302 redirect to the Shields.io badge image URL.

It's very slow to run so a cron would be approprite (but I've yet to do this myself so can't advise). I think this varies between hosting providers.

## Status

This was something I threw together on a Saturday evening. Calling my PHP rusty would be a compliment. I hope others find this useful and I can see where there might be customisations, so please, if you adapt it, send a pull request. If an actual PHP dev looks uses this, please don't hesitate to rewrite it with modern coding conventions; it should hopefully facilitate contributions. If there's an easier way to set this up, send a PR for the README.

I haven't checked the figures this returns. i.e. it seems to return v1.0 installs and v1.1 installs, but I'm not sure how updates are considered, so there may be double counting.

I'd love to know if people are using it, so star the repo if you do.

## Licence

[MIT to be permissible](https://github.com/BrianHenryIE/iOS-App-Install-Count-Badge/blob/master/LICENCE). 	
