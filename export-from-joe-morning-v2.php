<?php require_once('vendor/autoload.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

use Facebook\WebDriver\WebDriverBy;

$host = 'http://localhost:4444/wd/hub';
$capabilities = DesiredCapabilities::firefox();
$driver = RemoteWebDriver::create($host, $capabilities, 5000);

$url = 'http://joemorning.com';

$driver->get($url);

$loginButton = waitUntilElementAvailable(
    $driver, WebDriverBy::cssSelector("a.btn-login")
);

$loginButton->click();

$modal = waitUntilElementAvailable(
    $driver, WebDriverBy::id('mdlConfirm')
);

$username = waitUntilElementAvailable(
    $driver, WebDriverBy::cssSelector("div.form-group input[type='text'][name='email']")
);
$username ->sendKeys('mgoodman@thedmsgrp.com');

$password = waitUntilElementAvailable(
    $driver, WebDriverBy::cssSelector("div.form-group input[type='password'][name='password']")
);
$password ->sendKeys('temp123');

$loginButton = waitUntilElementAvailable(
    $driver, WebDriverBy::cssSelector("div.modal-footer button[type='button'].btn.btn-primary.btn-confirm")
);
$loginButton->click();

sleep(3);

$url = "https://www.joemorning.com/admin/jobs/missingActualTitle?processing=1&sortcolumn=Date%20Added&sortorder=DESC&status=1&mappedtitle=&dateadded=&page=1";

$driver->get($url);

sleep(3);

// Get the total amount of pages

// "button.btn.btn-xs.btn-primary"

$buttonsList = $driver->findElements(
    WebDriverBy::cssSelector("button.btn.btn-xs.btn-primary")
);

//echo sizeof($buttonsList) . PHP_EOL;

$lastPageButton = $buttonsList[sizeof($buttonsList) - 1];

$totalPages = intval($lastPageButton->getText());
echo "totalPages: $totalPages" . PHP_EOL;

$currentTimestamp = microtime(true);
$filename = "export/$currentTimestamp.csv";

fopen($filename, "a");

for ($i=0; $i<$totalPages; $i++) {
    //$before = microtime(true);

    processTable($driver, $filename);

    if ($i < $totalPages - 1) {
        goToNextPage($driver, $i + 1);
    }

    //$after = microtime(true);

    //echo ($after - $before) . " sec/page" . PHP_EOL;
}

$driver->quit();

/**
 * @param $driver
 * @param $element
 * @return null
 */
function waitUntilElementAvailable($driver, $element)
{
    $notFound = true;

    $currentAttempt = 1;
    $maxAttempts = 10;

    $elementToFind = null;

    while ($notFound) {
        if ($currentAttempt > $maxAttempts) {
            break;
        }

        //echo "Searching element in DOM. Attempt $currentAttempt of $maxAttempts" . PHP_EOL;

        try {
            $elementToFind = $driver->findElement($element);
        } catch (Exception $e) {
            //echo $e->getMessage() . PHP_EOL;
        }

        if (!empty($elementToFind)) {
            $notFound = false;
        } else {
            $currentAttempt++;
        }

        sleep(1);
    }

    return $elementToFind;
}

/**
 * @param $driver
 * @param $filename
 */
function processTable($driver, $filename)
{
    echo "processing table" . PHP_EOL;

    // We just wait until the table is available

    waitUntilElementAvailable(
        $driver, WebDriverBy::cssSelector("table.table.table-striped.table-bordered")
    );

    $trList = $driver->findElements(
        WebDriverBy::cssSelector(
            "table.table.table-striped.table-bordered tbody tr"
        )
    );

    //echo sizeof($trList) . PHP_EOL;

    for ($i=0; $i<sizeof($trList); $i++) {
        $currentTR = $trList[$i];
        //print_r($currentTR);

        $th1 = $currentTR->findElement(
            WebDriverBy::cssSelector("th div div span")
        );

        $th1Text = str_replace(',', '', $th1->getText());
        //echo "th1Text: $th1Text" . PHP_EOL;

        //

        $th2 = $currentTR->findElement(
            WebDriverBy::cssSelector('th.td-string.td-mapped-title')
        );

        $th2Text = $th2->getText();
        //echo "th2Text: $th2Text" . PHP_EOL;

        //

        $td1 = $currentTR->findElement(
            WebDriverBy::cssSelector('td.td-string.td-display-title')
        );

        $td1Text = $td1->getText();
        //echo "td1Text: $td1Text" . PHP_EOL;

        //

        $td2 = $currentTR->findElement(
            WebDriverBy::cssSelector('td.td-string.td-added-by')
        );

        $td2Text = $td2->getText();
        //echo "td2Text: $td2Text" . PHP_EOL;

        //

        $td3 = $currentTR->findElement(
            WebDriverBy::cssSelector('td.td-datetime.td-date-added')
        );

        $td3Text = $td3->getText();
        //echo "td3Text: $td3Text" . PHP_EOL;

        //

        $lineForFile = $th1Text . "," . $th2Text . "," . $td1Text . "," . $td2Text . "," . $td3Text . "\n";
        //echo $lineForFile;

        file_put_contents($filename, $lineForFile, FILE_APPEND);
    }
}

/**
 * @param $driver
 * @param $currentPage
 */
function goToNextPage($driver, $currentPage)
{
    $nextPage = $currentPage + 1;

    echo "going to the next page from page $currentPage to $nextPage" . PHP_EOL;

    $url = "https://www.joemorning.com/admin/jobs/missingActualTitle?processing=1&sortcolumn=Date%20Added&sortorder=DESC&status=1&mappedtitle=&dateadded=&page=$nextPage";

    $driver->get($url);
}
