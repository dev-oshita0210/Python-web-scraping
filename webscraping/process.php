<?php
require_once 'vendor\autoload.php';

shell_exec("java -jar selenium-server-4.7.1.jar standalone");

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriver;

$scrape_link = 'https://www.maxaroma.com/fragrancemen-fragrances/p4u/cid-3/view';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scrape_link = $_POST['category'];
}

$options = new ChromeOptions();
$options->setExperimentalOption("excludeSwitches", ["enable-automation"]);
$options->setExperimentalOption("useAutomationExtension", FALSE);
$options->addArguments(
    array(
        '--window-size=1920,1080',
        '--disable-blink-features=AutomationControlled',
        'user-agent=Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36'
    )
);

$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
$host = 'http://localhost:4444/wd/hub';
$driver = RemoteWebDriver::create($host, $capabilities);
$driver->manage()->window()->maximize();
$driver->wait(3);

$driver->get($scrape_link);

//$parent_list = $driver->findElement(WebDriverBy::xPath('/html/body/div[25]/main/section[2]/div/div/div[3]/div[2]/ul'));

// get "ul" of product list.
$parent_list = $driver->findElement(WebDriverBy::className('listing_grid'));
// get array of each product.
$child_array = $parent_list->findElements(WebDriverBy::cssSelector('ul.listing_grid > li'));

$links = [];
foreach ($child_array as $element) {
    $each_link = $element->findElement(WebDriverBy::cssSelector('div.product > div.product-name > a'));    
    // echo $each_link->getAttribute('href') . "<br/>";
    array_push($links, $each_link->getAttribute('href'));
    // $driver->get($each_link->getAttribute('href'));
    // $driver->navigate()->back();
}

$product_array = [];

foreach($links as $link) {
    $driver->get($link);
    //$driver->wait(1);    
    $driver->findElement(WebDriverBy::xpath('//*[@id="dtl_tab"]/div/h2[1]'))->click();
    // brand : /html/body/div[25]/main/section[2]/div/div[3]/div[1]/div[1]/h1/a, //*[@id="product_descriptionpart"]/div[1]/h1/a
    $brand = $driver->findElement(WebDriverBy::xpath('//*[@id="product_descriptionpart"]/div[1]/h1/a'))->getText();
    // echo($brand).",";

    // name : /html/body/div[25]/main/section[2]/div/div[3]/div[1]/div[1]/div, //*[@id="product_descriptionpart"]/div[1]/div
    $name = $driver->findElement(WebDriverBy::xpath('//*[@id="product_descriptionpart"]/div[1]/div'))->getText();
    // echo($name).",";
    // price : /html/body/div[25]/main/section[2]/div/div[3]/div[1]/div[3]/strong, //*[@id="show_sort_priceDiv"]/strong
    $price = $driver->findElement(WebDriverBy::xpath('//*[@id="show_sort_priceDiv"]/strong'))->getText();
    // echo($price).",";

    // interest-free : /html/body/div[25]/main/section[2]/div/div[3]/div[1]/div[3]/div/afterpay-placement//p/span[1],     
    // $interest = $driver->findElement(WebDriverBy::xpath('//*[@id="show_sort_priceDiv"]/strong'))->getText();
    // echo($interest).",";

    // detail : /html/body/div[25]/main/section[2]/div/div[3]/div[1]/div[4]/div, //*[@id="psort_descriptn"]
    $detail = $driver->findElement(WebDriverBy::xpath('//*[@id="psort_descriptn"]'))->getText();
    // echo($detail).",";
    $SKU = 0;
    try {
        // //*[@id="addtocart_max2dayreward"]/div/div/div[2]/div[1]/span
        // /html/body/div[25]/main/section[2]/div/div[3]/div[2]/div/div/div[2]/div[1]/span
        $SKU = $driver->findElement(WebDriverBy::xpath('//*[@id="addtocart_max2dayreward"]/div/div/div[2]/div[1]/span'));
        // echo($SKU->getText()).",";
    } catch (Exception $e) {
        // echo 'SKU:0';
    }
    // SKU : /html/body/div[25]/main/section[2]/div/div[3]/div[2]/div/div/div[2]/div[1]/span, //*[@id="addtocart_max2dayreward"]/div/div/div[2]/div[1]/span
    // $SKU = $driver->findElement(WebDriverBy::xpath('//*[@id="addtocart_max2dayreward"]/div/div/div[2]/div[1]/span'));
    // if($SKU)
    //     echo($SKU->getText()).",";

    // More detail : /html/body/div[25]/main/section[2]/div/div[3]/div[8], //*[@id="dtl_tab"]

    //*[@id="dtl_tab"]/div/h2[1]
    
    $driver->executeScript('window.scrollBy(0,1000)');
    $more_detail = $driver->findElement(WebDriverBy::xpath('//*[@id="prodetail"]'))->getText();

    // $driver->findElement(WebDriverBy::xpath('//*[@id="dtl_tab"]/div/h2[2]'))->click();
    // $more_detail .= $driver->findElement(WebDriverBy::xpath('//*//*[@id="dtl_tab"]/div/div[2]'))->getText();

    // $driver->findElement(WebDriverBy::xpath('//*[@id="dtl_tab"]/div/h2[3]'))->click();
    // $more_detail .= $driver->findElement(WebDriverBy::xpath('//*[@id="dtl_tab"]/div/div[3]'))->getText();
    
    //$more_detail = $driver->findElement(WebDriverBy::xpath('//*[@id="dtl_tab"]'))->getText();
    // echo($more_detail)."<br>";

    array_push($product_array,[
        "brand"=>$brand,
        "category"=>$_POST['type'],
        "url"=>$link,
        "name"=>$name,
        "price"=>$price,
        "description"=>$detail,
        "SKU" => $SKU,
        "more_detail" => $more_detail
    ]);
}

// var_dump($product_array);
echo json_encode($product_array);
$driver->quit();
?>
