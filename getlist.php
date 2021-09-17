<?php

include_once('functions.php');

error_reporting(E_ALL ^ E_WARNING);

header('Cache-Control: no-cache');
header('Content-type: text/html; charset=utf-8');

mb_internal_encoding("UTF-8");

$start = microtime(true);

$configFileName = __DIR__ . DIRECTORY_SEPARATOR . 'config.yaml';
$configData = yaml_parse_file($configFileName);
if ($configData === false) {
    echo "<b>ERROR:</b> No config file found: <u>", $configFileName, "</u>";
    exit(0);
}

array_walk($configData['vendors'], 'normalize');
$configData['vault'] =  truepath($configData['vault'], false, false);

if (! is_dir($configData['vault'])) {
    echo "<b>ERROR:</b> No Vault path exists: <u>", $configData['vault'], "</u>";
    exit(0);
}

$vaultNumDirs = countDirsOffset($configData['vault']);

date_default_timezone_set($configData['timezone']);
echo "<h2 id='title' class='toptitle'>", $configData['title'], "</h2>";
echo "<div class='timestamp'>", date("F j, Y, H:i:s T");

$order = $_GET['order'];

$noerrors = true;

switch ($order) {
    case 'scene':
        echo "<span class='order'><img src='images/clapperboard.png'> SCENES</span></div>";

        echo "<ul id='progress'>";
        $scenelist = walkByScenes();
        echo "</ul>"; # id='progress'

        echo printSceneList($scenelist);

        $count = count($scenelist);
        echo "<p class='total'>", $count, ($count == 1 ? " scene" : " scenes"), "</p>";

        break;

    case 'vendorscene':
        echo "<span class='order'><img src='images/vendor.png'> VENDORS &#8594; SCENES</span></div>";

        echo "<ul id='progress'>";
        $vendorlist = walkByVendorsScenes();
        echo "</ul>"; # id='progress'

        echo "<ul class='listvendors'>";

        ksort($vendorlist, SORT_STRING | SORT_FLAG_CASE);
        foreach ($vendorlist as $vendor => $scenelist) {
            echo "\n<li class='vendor'>";
            $count = count($scenelist);
            echo "<div class='toggleitem'>", explode(DIRECTORY_SEPARATOR, $vendor)[0],
                 "<span class='count'>", $count, ($count > 1 ? " scenes" : " scene"),
                 "</span></div>";
            echo "<div class='li-content'>";

            echo printSceneList($scenelist);

            echo "</div></li>"; # class='li-content' class='vendor'
        }

        echo "</ul>"; # id='listvendors'

        $count = count($vendorlist);
        echo "<p class='total'>", $count, ($count == 1 ? " vendor" : " vendors"), "</p>";

        break;

    case 'date':
        echo "<span class='order'><img src='images/calendar.png'> DATES</span></div>";

        echo "<ul id='progress'>";
        $datelist = walkByDates();
        echo "</ul>"; # id='progress'

        echo printDateList($datelist);

        $c = count($datelist);
        echo "<p class='total'>", $c, $c == 1 ? " date" : " dates ", "</p>";

        break;

    case 'vendordate':
        echo "<span class='order'><img src='images/vendor.png'> VENDORS &#8594; DATES</span></div>";

        echo "<ul id='progress'>";
        $vendorlist = walkByVendorsDates();
        echo "</ul>"; # id='progress'

        echo "<ul class='listvendors'>";

        ksort($vendorlist, SORT_STRING | SORT_FLAG_CASE);
        foreach ($vendorlist as $vendor => $datelist) {
            echo "\n<li class='vendor'>";
            $c = count($datelist);
            echo "<div class='toggleitem'>", explode(DIRECTORY_SEPARATOR, $vendor)[0],
                 "<span class='count'>", $c, ($c > 1 ? " dates" : " date"), "</span></div>";

            echo "<div class='li-content'>";

            echo printDateList($datelist);

            echo "</div></li>"; # class='li-content' class='vendor'
        }

        echo "</ul>"; # id='listscenes'

        $c = count($vendorlist);
        echo "<p class='total'>", $c, ($c == 1 ? " vendor" : " vendors"), "</p>";

        break;

    default:
        echo "<span class='order' style='color:red;'>!!! Wrong options !!!</span></div>";
}
$time_elapsed_secs = microtime(true) - $start;
echo "<p class='copyright'>&copy; 2021 zaserge@gmail.com, v", VERSION,
".&nbsp;&nbsp;&nbsp;Finished in ", number_format($time_elapsed_secs, 3), " sec. Memory usage: ",
round(memory_get_peak_usage(true) / 1048576, 2), " MB.</p>";

if ($noerrors) {
    echo "<div id='done'></div>";
}

?>
