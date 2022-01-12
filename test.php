<html>

<head>
    <!--    <meta charset="UTF-8">-->
</head>
<body>
<?PHP

ini_set('memory_limit', '10024M'); // or you could use 1G
ini_set('max_execution_time', 30000000); //300 seconds = 5 minutes
session_start();
ob_start();
try {
    include("Models/PaTo.php");
    include("Models/Display.php");
    include("Models/Add.php");
    include("includes/config.php");
    include("includes/Functions.php");
    include("Models/K_GuzzleHttp/vendor/autoload.php");
    $FilesSaveDist = "resources/Gallery/";
    $Connect = new PaTo();
    $Display = new Display($Connect->connect);
} catch (Exception $exc) {
    echo $exc->getMessage();
}

$Pages = 618;
$client = new GuzzleHttp\Client([
    'charset' => 'utf-8'
]);
for ($i = 618; $i <= $Pages; $i++) {
    $res = $client->get("https://www.rosheta.com/ar/search/?category=5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,42,69,74,75,76,77,78,79,81,84,89,92,94?page=1&page=$i");
    $utf8_body = mb_convert_encoding($res->getBody(), 'UTF-8', 'UTF-8');
    $dom = new DOMDocument();
    $dom->loadHTML($utf8_body);
    $xpath = new DOMXPath($dom);
    $classname = "item_content";
    $nodeList = $xpath->query("//div[@class='single-products']");

    foreach ($nodeList as $key => $tag) {

        $ProductRoshetaID = explode("/", $tag->getElementsByTagName('a')->item(0)->getAttribute('href'));
        $ProductLink = "https://www.rosheta.com/{$ProductRoshetaID[1]}/{$ProductRoshetaID[2]}/" . urlencode($ProductRoshetaID[3]);
        $ProductRoshetaID = $ProductRoshetaID[2];
        if ($ProductRoshetaID > 10883) {
            $res1 = $client->get($ProductLink);
            $dom1 = new DOMDocument();
            $dom1->loadHTML($res1->getBody());
            $xpath1 = new DOMXPath($dom1);
            $ProductName = $xpath1->query("//span[@class='med-name']");
            $ProductName = $ProductName->item(0)->textContent;
            $Concentration = $xpath1->query("//span[@class='med-sub-info']");
            $Concentration = $Concentration->item(0)->textContent;
            $ProductCategory = $xpath1->query("//ol[@class='breadcrumb']");
            $ProductCategory = $ProductCategory->item(0)->textContent;

            $ProductULBeforePrepare = $xpath1->query("//ul[@class='arw-list-default']");

            $ProductDes = $xpath1->query("//p[@class='myp']");
            $ProductDes = $ProductDes->item(1)->textContent;

            $ProductPhoto = $xpath1->query("//a[@class='medicine-image-magnificPopup']")->item(0)->getAttribute('href');
            $ProductPhoto = "https://www.rosheta.com" . $ProductPhoto;
            $ProductULBeforePrepare = trim($ProductULBeforePrepare->item(0)->textContent);
            $ProductULBeforePrepare = str_replace("الاسم العلمي:", "||", $ProductULBeforePrepare);
            $ProductULBeforePrepare = str_replace("جرعه الدواء:", "||", $ProductULBeforePrepare);
            $ProductULBeforePrepare = str_replace("النوع:", "||", $ProductULBeforePrepare);
            $ProductULBeforePrepare = str_replace("الشركة المنتجة:", "||", $ProductULBeforePrepare);
            $ProductULBeforePrepare = explode("||", $ProductULBeforePrepare);
            foreach ($ProductULBeforePrepare as $LisKey => $ListValue) {
                if ($LisKey == 1) {
                    $ScientificName = trim($ListValue);
                }
                if ($LisKey == 2) {
                    $ConcentrationDes = trim($ListValue);
                }
                if ($LisKey == 3) {
                    $Type = trim($ListValue);
                }
                if ($LisKey == 4) {
                    $ProducingCompany = trim($ListValue);
                }
            }
            $ProductPrice = str_replace($Type, "", trim($tag->textContent));
            $ProductPrice = str_replace($ProductName, "", $ProductPrice);
            $ProductPrice = str_replace($Concentration, "", $ProductPrice);
            $ProductPrice = str_replace("...", "", $ProductPrice);
            $ProductPrice = trim($ProductPrice);

            $ProductCategory = str_replace($Concentration, "", trim($ProductCategory));
            $ProductCategory = str_replace($ProductName, "", trim($ProductCategory));
            $ProductCategory = str_replace("الرئيسية", "", trim($ProductCategory));

            $ProductPhotoExt = explode(".", $ProductPhoto);
            $ProductOurPhoto = $ProductRoshetaID . "." . end($ProductPhotoExt);

            if ($ProductPhoto !== "/upload/") {
                @file_put_contents($FilesSaveDist . $ProductOurPhoto, file_get_contents($ProductPhoto));
            }
//    pr($ProductRoshetaID);
//    pr($ProductName);
//    pr($Concentration);
//    pr($ScientificName);
//    pr($ConcentrationDes);
//    pr($Type);
//    pr($ProducingCompany);
//    pr($ProductDes);
//    pr($ProductOurPhoto);
//    pr($ProductPrice);

            $AddArray = array();
            $AddArray['CategoryName'] = $ProductCategory;
            $AddArray['Title_ar'] = $ProductName;
            $AddArray['Content_ar'] = $ProductDes;
            $AddArray['Concentration'] = $Concentration;
            $AddArray['ScientificName'] = $ScientificName;
            $AddArray['Type'] = $Type;
            $AddArray['ProducingCompany'] = $ProducingCompany;
            if ($ProductPhoto !== "/upload/") {
                $AddArray['Photo'] = $ProductOurPhoto;
            }

            $FinalPrice = explode("جنية", trim($ProductPrice));
            $AddArray['Price'] = trim($FinalPrice[0]);
            $AddArray['RoshetaID'] = $ProductRoshetaID;
            $AddArray['Date'] = time();
            $AddNewProduct = new Add($AddArray, "medicine");
            unset($ProductCategory);
            unset($ProductName);
            unset($ProductDes);
            unset($Concentration);
            unset($ScientificName);
            unset($Type);
            unset($ProducingCompany);
            unset($ProductOurPhoto);
            unset($ProductPrice);
            unset($ProductRoshetaID);
        }
    }
}
?>


</body>
</html>
