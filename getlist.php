<?php

$version = "1.0.2";

header('Cache-Control: no-cache');
header('Content-type: text/html; charset=utf-8');

mb_internal_encoding("UTF-8");

$start = microtime(true);

$configData = yaml_parse_file(__DIR__ . '/config.yaml');
if ($configData == false) {
    echo "No config files";
    exit(-1);
}

date_default_timezone_set($configData['timezone']);
echo "<h2 class='toptitle'>", $configData['title'], "</h2>";
echo "<div class='timestamp'>", date("F j, Y, H:i:s T");

$order = $_GET['order'];

switch ($order) {
    case 'scene':
        echo "<span class='order'><img src='images/clapperboard.png'> SCENES</span></div>";

        echo "<ul id='progress'>";
        $scenelist = walkByScenes();
        echo "</ul>"; # id='progress'

        echo "<ul class='listscenes'>";

        ksort($scenelist, SORT_STRING | SORT_FLAG_CASE);
        foreach ($scenelist as $scene => $data) {
            echo "\n<li class='scene'>";
            $c = count($data);
            echo "<div class='toggleitem'>", $scene, "<span class='count'>", $c, ($c > 1 ? " shots" : " shot"), "</span></div>";

            echo "<div class='li-content'>";
            echo "<ul class='listshots'>";
            ksort($data, SORT_STRING | SORT_FLAG_CASE);
            $index = "";
            $rowclass = false;
            foreach ($data as $shot) {
                if (strcmp($index, $shot['index']) != 0) {
                    $index = $shot['index'];
                    $rowclass = !$rowclass;
                }
                echo "<li class='", ($rowclass ? "raw1" : "raw2"), " ", $shot['status'], "'>";
                echo "<span class='shotname'>", $shot['shot'];

                echo "<div class='infotext'>";
                echo "<p><b>Vendor:</b> ", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "<br>";
                echo "<b>Date:</b> ", $shot['date'], "<br>";
                echo "<b>Path:</b> ", $configData['vendordir'], DIRECTORY_SEPARATOR, $shot['vendor'], DIRECTORY_SEPARATOR, $shot['date'], "</p";
                echo "</div>"; # id='infotext'

                echo "</span>";
                echo "<span class='briefinfo'>", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], " ", $shot['date'], "</span>";
                echo "</li>";
            }
            echo "</ul>"; # id='listshots'  
            echo "</div>"; # id='li-content'

            echo "</li>";  # id='scene'
        }

        echo "</ul>"; # id='listscenes'

        $c = count($scenelist);
        echo "<p class='total'>", $c, ($c == 1 ? " scene" : " scenes"), "</p>";
        break;

    case 'date':
        echo "<span class='order'><img src='images/calendar.png'> DATES</span></div>";

        echo "<ul id='progress'>";
        $datelist = walkByDates();
        echo "</ul>"; # id='progress'

        echo "<ul class='listdates'>";

        krsort($datelist, SORT_STRING | SORT_FLAG_CASE);
        foreach ($datelist as $date => $data) {
            echo "\n<li class='date'>";
            $c = count($data);
            echo "<div class='toggleitem'>", $date, "<span class='count'>", $c, ($c > 1 ? " shots" : " shot"), "</span></div>";

            echo "<div class='li-content'>";
            echo "<ul class='listshots'>";
            ksort($data, SORT_STRING | SORT_FLAG_CASE);
            $index = "";
            $rowclass = false;
            foreach ($data as $shot) {
                if (strcmp($index, $shot['index']) != 0) {
                    $index = $shot['index'];
                    $rowclass = !$rowclass;
                }
                echo "<li class='", ($rowclass ? "raw1" : "raw2"), " ", $shot['status'], "'>";
                echo "<span class='shotname'>", $shot['shot'];

                echo "<div class='infotext'>";
                echo "<p><b>Vendor:</b> ", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "<br>";
                echo "<b>Date:</b> ", $shot['date'], "<br>";
                echo "<b>Path:</b> ", $configData['vendordir'], DIRECTORY_SEPARATOR, $shot['vendor'], DIRECTORY_SEPARATOR, $shot['date'], "</p";
                echo "</div>"; # id='infotext'

                echo "</span>";
                echo "<span class='briefinfo'>", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "</span>";
                echo "</li>";
            }
            echo "</ul>"; # id='listshots'     
            echo "</div>"; # id='li-content'  

            echo "</li>"; # id='date'               
        }

        echo "</ul>"; # id='listdates'  

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
            echo "<div class='toggleitem'>", explode(DIRECTORY_SEPARATOR, $vendor)[0], "<span class='count'>", $c, ($c > 1 ? " dates" : " date"), "</span></div>";

            echo "<div class='li-content'>";
            echo "<ul class='listdates'>";

            krsort($datelist, SORT_STRING | SORT_FLAG_CASE);
            foreach ($datelist as $date => $data) {
                echo "\n<li class='date'>";
                $c = count($data);
                echo "<div class='toggleitem'>", $date, "<span class='count'>", $c, ($c > 1 ? " shots" : " shot"), "</span></div>";

                echo "<div class='li-content'>";
                echo "<ul class='listshots'>";
                ksort($data, SORT_STRING | SORT_FLAG_CASE);
                $index = "";
                $rowclass = false;
                foreach ($data as $shot) {
                    if (strcmp($index, $shot['index']) != 0) {
                        $index = $shot['index'];
                        $rowclass = !$rowclass;
                    }
                    echo "<li class='", ($rowclass ? "raw1" : "raw2"), " ", $shot['status'], "'>";
                    echo "<span class='shotname'>", $shot['shot'];

                    echo "<div class='infotext'>";
                    echo "<p><b>Vendor:</b> ", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "<br>";
                    echo "<b>Date:</b> ", $shot['date'], "<br>";
                    echo "<b>Path:</b> ", $configData['vendordir'], DIRECTORY_SEPARATOR, $shot['vendor'], DIRECTORY_SEPARATOR, $shot['date'], "</p";
                    echo "</div>"; # id='infotext'
                    
                    echo "</span>";
                    echo "<span class='briefinfo'>", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "</span>";
                    echo "</li>";
                }
                echo "</ul>"; # id='listshots'     
                echo "</div>"; # id='li-content'  

                echo "</li>"; # id='date'               
            }

            echo "</ul>"; # id='listdates'  

            echo "</div>"; # id='li-content'
            echo "</li>";  # id='vendor'
        }

        echo "</ul>"; # id='listscenes'

        $c = count($vendorlist);
        echo "<p class='total'>", $c, ($c == 1 ? " vendor" : " vendors"), "</p>";

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
            $c = count($scenelist);
            echo "<div class='toggleitem'>", explode(DIRECTORY_SEPARATOR, $vendor)[0], "<span class='count'>", $c, ($c > 1 ? " scenes" : " scene"), "</span></div>";

            echo "<div class='li-content'>";
            echo "<ul class='listscenes'>";

            ksort($scenelist, SORT_STRING | SORT_FLAG_CASE);
            foreach ($scenelist as $date => $data) {
                echo "\n<li class='scene'>";
                $c = count($data);
                echo "<div class='toggleitem'>", $date, "<span class='count'>", $c, ($c > 1 ? " shots" : " shot"), "</span></div>";

                echo "<div class='li-content'>";
                echo "<ul class='listshots'>";
                ksort($data, SORT_STRING | SORT_FLAG_CASE);
                $index = "";
                $rowclass = false;
                foreach ($data as $shot) {
                    if (strcmp($index, $shot['index']) != 0) {
                        $index = $shot['index'];
                        $rowclass = !$rowclass;
                    }
                    echo "<li class='", ($rowclass ? "raw1" : "raw2"), " ", $shot['status'], "'>";
                    echo "<span class='shotname'>", $shot['shot'];
 
                    echo "<div class='infotext'>";
                    echo "<p><b>Vendor:</b> ", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "<br>";
                    echo "<b>Date:</b> ", $shot['date'], "<br>";
                    echo "<b>Path:</b> ", $configData['vendordir'], DIRECTORY_SEPARATOR, $shot['vendor'], DIRECTORY_SEPARATOR, $shot['date'], "</p";
                    echo "</div>"; # id='infotext'

                    echo "</span>";
                    echo "<span class='briefinfo'>", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "</span>";
                    echo "</li>";
                }
                echo "</ul>"; # id='listshots'     
                echo "</div>"; # id='li-content'  

                echo "</li>"; # id='scene'               
            }

            echo "</ul>"; # id='listscenes'  

            echo "</div>"; # id='li-content'
            echo "</li>";  # id='vendor'
        }

        echo "</ul>"; # id='listvendors'

        $c = count($vendorlist);
        echo "<p class='total'>", $c, ($c == 1 ? " vendor" : " vendors"), "</p>";

        break;

    default:
        echo "<span class='order' style='color:red;'>!!! Wrong options !!!</span></div>";
} 
$time_elapsed_secs = microtime(true) - $start;
echo "<p class='copyright'>&copy; 2021 zaserge@gmail.com, v", $version,
    ".&nbsp;&nbsp;&nbsp;Finished in ", number_format($time_elapsed_secs, 3), " sec.</p>";


function printProgress(string $s): void
{
    echo "<li>", explode(DIRECTORY_SEPARATOR, $s)[0], "</li>";
    ob_flush();
    flush();
}


function walkByScenes(): array
{
    global $configData;
    $shotList = [];

    foreach ($configData['vendors'] as $vendor) {
        printProgress($vendor);

        $venDir = $configData['vendordir'] . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . "[0-9]*";

        foreach (glob($venDir,  GLOB_ONLYDIR) as $datePath) {
            collectByScene($datePath, $vendor, $shotList);
        }
    }
    return ($shotList);
}


function walkByVendorsScenes(): array
{
    global $configData;
    $shotList = [];

    foreach ($configData['vendors'] as $vendor) {
        printProgress($vendor);

        $shotList[$vendor] = [];
        $venDir = $configData['vendordir'] . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . "[0-9]*";

        foreach (glob($venDir, GLOB_ONLYDIR) as $datePath) {
            collectByScene($datePath, $vendor, $shotList[$vendor]);
        }
    }
    return ($shotList);
}


function walkByDates(): array
{
    global $configData;
    $shotList = [];

    foreach ($configData['vendors'] as $vendor) {
        printProgress($vendor);

        $venDir = $configData['vendordir'] . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . "[0-9]*";

        foreach (glob($venDir,  GLOB_ONLYDIR) as $datePath) {
            collectByDate($datePath, $vendor, $shotList);
        }
    }
    return ($shotList);
}


function walkByVendorsDates(): array
{
    global $configData;
    $shotList = [];

    foreach ($configData['vendors'] as $vendor) {
        printProgress($vendor);

        $shotList[$vendor] = [];
        $venDir = $configData['vendordir'] . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . "[0-9]*";

        foreach (glob($venDir,  GLOB_ONLYDIR) as $datePath) {
            collectByDate($datePath, $vendor, $shotList[$vendor]);
        }
    }
    return ($shotList);
}


function collectByDate(mixed $datePath, string $vendor, array &$shotList): void
{
    global $configData;

    $date = mb_substr(basename($datePath), 0, 8);
    $reTypes = $configData['regexp'];

    if (!isset($shotList[$date])) {
        $shotList[$date] = [];
    }

    foreach (scandir($datePath) as $item) {
        if ($item[0] != ".") {
            $m = false;
            foreach ($reTypes as $re) {
                if (preg_match($re['re'], $item, $matches)) {
                    $shotList[$date][$item . $date] =
                        [
                            "shot" => $item,
                            "scene" => $matches['scene'],
                            "index" => $matches['index'],
                            "vendor" => $vendor,
                            "date" => $date,
                            "status" => $re['type']
                        ];
                    $m = true;
                    break;
                }
            }
            if (!$m) {
                $shotList[$date][$item . $date] =
                    [
                        "shot" => $item,
                        "scene" => false,
                        "index" => false,
                        "vendor" => $vendor,
                        "date" => $date,
                        "status" => 'unknown'
                    ];
            }
        }
    }
    return;
}


function collectByScene(string $datePath, string $vendor, array &$list): void
{
    global $configData;

    $date = basename($datePath);
    $reTypes = $configData['regexp'];

    foreach (scandir($datePath) as $item) {
        if ($item[0] != ".") {
            foreach ($reTypes as $re) {
                if (preg_match($re['re'], $item, $matches)) {
                    $matches['scene'] = strtoupper($matches['scene']);
                    if (!isset($list[$matches['scene']])) {
                        $list[$matches['scene']] = [];
                    }
                    $list[$matches['scene']][$item . $date] =
                        [
                            "shot" => $item,
                            "scene" => $matches['scene'],
                            "index" => $matches['index'],
                            "vendor" => $vendor,
                            "date" => $date,
                            "status" => $re['type']
                        ];
                    break;
                }
            }
        }
    }
    return;
}

?>
