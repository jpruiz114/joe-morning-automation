<?php require_once('vendor/autoload.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

use Facebook\WebDriver\WebDriverBy;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$username = getenv('JOE_MORNING_USERNAME');
echo "username: $username" . PHP_EOL;

$password = getenv('JOE_MORNING_PASSWORD');
echo "password: $password" . PHP_EOL;

$host = 'http://localhost:4444/wd/hub';
$capabilities = DesiredCapabilities::chrome();
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
$username ->sendKeys($username);

$password = waitUntilElementAvailable(
    $driver, WebDriverBy::cssSelector("div.form-group input[type='password'][name='password']")
);
$password ->sendKeys($password);

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

$lastPageButton = $buttonsList[sizeof($buttonsList) - 1];

$totalPages = intval($lastPageButton->getText());
echo "totalPages: $totalPages" . PHP_EOL;

$currentTimestamp = microtime(true);
$filename = "export/$currentTimestamp.csv";

$fileInstance = fopen($filename, "a");

for ($i=0; $i<$totalPages; $i++) {
    processTable($driver, $fileInstance);

    if ($i < $totalPages - 1) {
        goToNextPage($driver, $i + 1);
    }
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

        try {
            $elementToFind = $driver->findElement($element);
        } catch (Exception $e) {

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
 * @param $fileInstance
 */
function processTable($driver, $fileInstance)
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

    for ($i=0; $i<sizeof($trList); $i++) {
        $currentTR = $trList[$i];

        $th2 = $currentTR->findElement(
            WebDriverBy::cssSelector('th.td-string.td-mapped-title')
        );

        $th2Text = $th2->getText();

        //

        $td1 = $currentTR->findElement(
            WebDriverBy::cssSelector('td.td-string.td-display-title')
        );

        $td1Text = $td1->getText();

        //

        $td2 = $currentTR->findElement(
            WebDriverBy::cssSelector('td.td-string.td-added-by')
        );

        $td2Text = $td2->getText();

        //

        $td3 = $currentTR->findElement(
            WebDriverBy::cssSelector('td.td-datetime.td-date-added')
        );

        $td3Text = $td3->getText();

        //

        $fields = array($th2Text, $td1Text, $td2Text, $td3Text);

        fputcsv($fileInstance, $fields);
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
