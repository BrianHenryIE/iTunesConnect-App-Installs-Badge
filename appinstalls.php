<?php

// v1.0.1

// Read settings from properties file
$handle = fopen("./reporter/Reporter.properties", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
      if(0 === strpos($line, "UserId")) {
        $userId = trim(substr(strstr($line, "="),1));
      }
      if(0 === strpos($line, "Password")) {
        $password = trim(substr(strstr($line, "="),1));
      }
      if(0 === strpos($line, "Account")) {
        $accountNum = intVal(substr(strstr($line, "="),1));
      }
      if(0 === strpos($line, "SKUs")) {
        $skus = explode(",",trim(substr(strstr($line, "="),1)));
      }
    }
    fclose($handle);
}

// Refresh every 24 hours
$cacheFile = "./reporter/appinstalls.txt";
if (file_exists($cacheFile) && time()-filemtime($cacheFile) < 24 * 3600) {
  $cache = file_get_contents($cacheFile);
  $url = "https://img.shields.io/badge/installs-$cache-green.svg";
  header('Location: '.$url);
  exit();
}

require __DIR__ . '/./vendor/autoload.php';

// We don't really care about timezones, day is as granular as we use.
// I also think iTunesConnect only publishes once/day on their own schedule (California time?)
date_default_timezone_set('UTC');

// The real hero
// https://github.com/mikebarlow/itc-reporter/
$Reporter = new \Snscripts\ITCReporter\Reporter(
    new \GuzzleHttp\Client
);

// A function to filter the SKUs (Bundle Identifier)
function isRelevant($sku, $skus) {
  foreach($skus as $skuToCheck) {
    if(0 === strpos($sku, $skuToCheck)) {
      return true;
    }
  }
  return false;
}

$Reporter->setUserId($userId);
$Reporter->setPassword($password);

$Reporter->setAccountNum($accountNum);

$vendors = $Reporter->getSalesVendors();

$vendorNum = $vendors[0];

// http://help.apple.com/itc/appssalesandtrends/#/itc2c006e6ff
$upgradesInAppPurchasesAndRedownloads = ["FI1","IA1","IA1-M","IA9","IA9-M","IAC","IAC-M","IAY","IAY-M","1E","1EP","1EU","3","3F","3T","F3","IA3","7","7F","7T","F7"];

// Yearly

$yearlyTotal = 0; // count
$currentYearUnits = 1; // stop condition

$yearNumber = date('Y')-1; // 2016

for ($date = $yearNumber; $currentYearUnits != 0; $date--) {

  $currentYearUnits = 0;

  $sales = $Reporter->getSalesReport(
      $vendorNum,
      'Sales',
     'Summary',
     'Yearly',
     $date
  );

  foreach($sales as $skuLocation) {
    if (isRelevant($skuLocation["SKU"], $skus) && !in_array($skuLocation["Product Type Identifier"], $upgradesInAppPurchasesAndRedownloads)) {
       $currentYearUnits += $skuLocation["Units"];
       $yearlyTotal += $skuLocation["Units"];
    }
  }
}


// Monthly

$currentMonth = date('n');

$monthlyTotal = 0;

// No need to run the previous month's report in January because it will be contained in the yearly report
if ($currentMonth > 1) {

  for ($i = date('n'); $i > 0; $i--) {

    $reportDate = date('Y'). str_pad($i,2,"0",STR_PAD_LEFT);

    $sales = $Reporter->getSalesReport(
        $vendorNum,
        'Sales',
       'Summary',
       'Monthly',
       $reportDate
    );

    foreach($sales as $skuLocation) {
      if (isRelevant($skuLocation["SKU"], $skus)) {
         $monthlyTotal += $skuLocation["Units"];
      }
    }
  }
}

// Daily

$currentDay = date('d');

$dailyTotal = 0;

// No need to run the previous month's report in January because it will be contained in the yearly report
if ($currentDay > 1) {

  for ($i = $currentDay; $i > 0; $i--) {

    $reportDate = date('Ym').str_pad($i,2,"0",STR_PAD_LEFT);

    $sales = $Reporter->getSalesReport(
        $vendorNum,
        'Sales',
       'Summary',
       'Daily',
       $reportDate
    );

    foreach($sales as $skuLocation) {
      if (isRelevant($skuLocation["SKU"], $skus)) {
         $dailyTotal += $skuLocation["Units"];
      }
    }
  }
}

$total = $yearlyTotal + $monthlyTotal + $dailyTotal;

$myfile = fopen($cacheFile, "w") or die("Cache file write error");
fwrite($myfile, $total);

$url = "https://img.shields.io/badge/installs-$total-green.svg";
header('Location: '.$url);
