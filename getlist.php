<?php

const VERSION = "1.2";

const DATE_MATCH_MARK = "#";
const DATE_MATCH_EXPR = "[0-9]*";
const DATE_MATCH_LEN = 8;

header('Cache-Control: no-cache');
header('Content-type: text/html; charset=utf-8');

mb_internal_encoding("UTF-8");

$start = microtime(true);

$configData = yaml_parse_file(__DIR__ . '/config.yaml');
if ($configData == false) {
    echo "No config files";
    exit(-1);
}

// normalize all path's

function normalize(&$path) {
    $path = truepath($path);
}

array_walk($configData['vendors'], 'normalize');

$configData['vault'] =  truepath($configData['vault'], false, false);

$vaultNumDirs = countDirsOffset($configData['vault']);

date_default_timezone_set($configData['timezone']);
echo "<h2 id='title' class='toptitle'>", $configData['title'], "</h2>";
echo "<div class='timestamp'>", date("F j, Y, H:i:s T");

$order = $_GET['order'];

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


/**
 * printProgress
 *
 * @param  mixed $s
 * @return void
 */
function printVendorInProgress(string $str): void
{
    echo "<li>", explode(DIRECTORY_SEPARATOR, $str)[0], "</li>";
    ob_flush();
    flush();
}


/**
 * walkByScenes
 *
 * @return array
 */
function walkByScenes(): array
{
    global $configData;
    global $vaultNumDirs;

    $shotList = [];

    foreach ($configData['vendors'] as $vendor) {
        printVendorInProgress($vendor);

        $offset = countDirsOffset($vendor) + $vaultNumDirs + 1;
        $vendorDir = $configData['vault'] . DIRECTORY_SEPARATOR
            . str_replace(DATE_MATCH_MARK, DATE_MATCH_EXPR, $vendor) . DIRECTORY_SEPARATOR;

        foreach (glob($vendorDir,  GLOB_ONLYDIR) as $datePath) {
            collectByScene($datePath, $vendor, $shotList, $offset);
        }
    }
    return ($shotList);
}


/**
 * walkByVendorsScenes
 *
 * @return array
 */
function walkByVendorsScenes(): array
{
    global $configData;
    global $vaultNumDirs;

    $shotList = [];

    foreach ($configData['vendors'] as $vendor) {
        printVendorInProgress($vendor);

        $offset = countDirsOffset($vendor) + $vaultNumDirs + 1;
        $shotList[$vendor] = [];
        $vendorDir = $configData['vault'] . DIRECTORY_SEPARATOR
            . str_replace(DATE_MATCH_MARK, DATE_MATCH_EXPR, $vendor) . DIRECTORY_SEPARATOR;

        foreach (glob($vendorDir, GLOB_ONLYDIR) as $datePath) {
            collectByScene($datePath, $vendor, $shotList[$vendor], $offset);
        }
    }
    return ($shotList);
}


/**
 * walkByDates
 *
 * @return array
 */
function walkByDates(): array
{
    global $configData;
    global $vaultNumDirs;

    $shotList = [];

    foreach ($configData['vendors'] as $vendor) {
        printVendorInProgress($vendor);

        $offset = countDirsOffset($vendor) + $vaultNumDirs + 1;
        $vendorDir = $configData['vault'] . DIRECTORY_SEPARATOR
            . str_replace(DATE_MATCH_MARK, DATE_MATCH_EXPR, $vendor) . DIRECTORY_SEPARATOR;

        foreach (glob($vendorDir,  GLOB_ONLYDIR) as $datePath) {
            collectByDate($datePath, $vendor, $shotList, $offset);
        }
    }
    return ($shotList);
}


/**
 * walkByVendorsDates
 *
 * @return array
 */
function walkByVendorsDates(): array
{
    global $configData;
    global $vaultNumDirs;

    $shotList = [];

    foreach ($configData['vendors'] as $vendor) {
        printVendorInProgress($vendor);

        $offset = countDirsOffset($vendor) + $vaultNumDirs + 1;
        $shotList[$vendor] = [];
        $vendorDir = $configData['vault'] . DIRECTORY_SEPARATOR
            . str_replace(DATE_MATCH_MARK, DATE_MATCH_EXPR, $vendor) . DIRECTORY_SEPARATOR;

        foreach (glob($vendorDir,  GLOB_ONLYDIR) as $datePath) {
            collectByDate($datePath, $vendor, $shotList[$vendor], $offset);
        }
    }
    return ($shotList);
}


/**
 * collectByDate
 *
 * @param  mixed $datePath
 * @param  mixed $vendor
 * @param  mixed $shotList
 * @param  mixed $offset
 * @return void
 */
function collectByDate(mixed $datePath, string $vendor, array &$shotList, int $offset): void
{
    global $configData;

    $reTypes = $configData['regexp'];

    $date = getDateNthDir($datePath, $offset);

    if (!isset($shotList[$date])) {
        $shotList[$date] = [];
    }

    foreach (scandir($datePath) as $item) {
        if ($item[0] != "." && is_dir($datePath . DIRECTORY_SEPARATOR . $item)) {
            $match = false;
            foreach ($reTypes as $re) {
                if (preg_match($re['re'], $item, $matches)) {
                    $shotList[$date][$item . $date] =
                        [
                            "shot" => $item,
                            "scene" => $matches['scene'],
                            "index" => $matches['index'],
                            "vendor" => $vendor,
                            "date" => $date,
                            "path" => $datePath,
                            "status" => $re['type']
                        ];
                    $match = true;
                    break;
                }
            }
            if (!$match) {
                $shotList[$date][$item . $date] =
                    [
                        "shot" => $item,
                        "scene" => false,
                        "index" => false,
                        "vendor" => $vendor,
                        "date" => $date,
                        "path" => $datePath,
                        "status" => 'unknown'
                    ];
            }
        }
    }
}


/**
 * collectByScene
 *
 * @param  mixed $datePath
 * @param  mixed $vendor
 * @param  mixed $list
 * @param  mixed $offset
 * @return void
 */
function collectByScene(string $datePath, string $vendor, array &$list, int $offset): void
{
    global $configData;

    $reTypes = $configData['regexp'];

    $date = getDateNthDir($datePath, $offset);

    foreach (scandir($datePath) as $item) {
        if ($item[0] != "." && is_dir($datePath . DIRECTORY_SEPARATOR . $item)) {
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
                            "path" => $datePath,
                            "status" => $re['type']
                        ];
                    break;
                }
            }
        }
    }
}

/**
 * printShot
 *
 * @param  mixed $shot
 * @param  mixed $row
 * @return void
 */
function printShot(array $shot, bool $row): string
{
    $buf  = "\n<li class='" . ($row ? "raw1" : "raw2") . " " . $shot['status'] . "'>";
    $buf .= "<span class='shotname'>" . $shot['shot'];

    $buf .= "<div class='infotext'>";
    $buf .= "<span class='shot'>" . $shot['shot'] . "</span>";
    $buf .= "<p><b>Vendor:</b> " . explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0] . "<br>";
    $buf .= "<b>Date:</b> " . $shot['date'] . "<br>";
    $buf .= "<b>Path:</b> " . $shot['path'] . "</p";
    $buf .= "</div>"; # class='infotext'

    $buf .= "</span>";
    $buf .= "<span class='briefinfo'>" . explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0] . "</span>";
    $buf .= "</li>\n";

    return $buf;
}


function printSceneList(array $scenelist): string
{
    $buf = "\n<ul class='listscenes'>";

    ksort($scenelist, SORT_STRING | SORT_FLAG_CASE);
    foreach ($scenelist as $scene => $shots) {
        $count = count($shots);
        $buf .= "\n<li class='scene'><div class='toggleitem'>" . $scene .
                "<span class='count'>" . $count . ($count > 1 ? " shots" : " shot") .
                "</span></div>";
        $buf .= "<div class='li-content'><ul class='listshots'>";
        ksort($shots, SORT_STRING | SORT_FLAG_CASE);
        $index = "";
        $rowclass = false;
        foreach ($shots as $shot) {
            if (strcmp($index, $shot['index']) != 0) {
                $index = $shot['index'];
                $rowclass = !$rowclass;
            }
            $buf .= printShot($shot, $rowclass);
        }
        $buf .= "</ul></div></li>\n"; # class='listshots'  class='li-content'  class='scene'
    }

    $buf .= "</ul>\n"; # class='listscenes'

    return $buf;
}

function printDateList(array $datelist): string
{
    $buf = "\n<ul class='listdates'>";

    krsort($datelist, SORT_STRING | SORT_FLAG_CASE);
    foreach ($datelist as $date => $data) {
        $c = count($data);
        $buf .= "\n<li class='date'><div class='toggleitem'>" . $date .
                "<span class='count'>" . $c . ($c > 1 ? " shots" : " shot") .
                "</span></div>";
        $buf .= "<div class='li-content'><ul class='listshots'>";
        ksort($data, SORT_STRING | SORT_FLAG_CASE);
        $index = "";
        $rowclass = false;
        foreach ($data as $shot) {
            if (strcmp($index, $shot['index']) != 0) {
                $index = $shot['index'];
                $rowclass = !$rowclass;
            }
            $buf .= printShot($shot, $rowclass);
        }
        $buf .= "</ul></div></li>\n"; # class='listshots'  class='li-content'  class='date'
    }

    $buf .= "</ul>\n"; # id='listdates'

    return $buf;
}

function countDirsOffset(string $path): int
{
    $count = 0;
    $len = strlen($path);
    for ($i = 0; $i < $len; $i++) {
        if ($path[$i] == DATE_MATCH_MARK) {
            break;
        }
        if ($path[$i] == DIRECTORY_SEPARATOR) {
            $count++;
        }
    }
    return $count;
}

function getDateNthDir(string $path, int $nth): string
{
    $len = strlen($path);
    $pos = 0;

    for ($i = 0; $i < $len; $i++) {
        if ($path[$i] == DIRECTORY_SEPARATOR) {
            $pos++;
            if ($pos >= $nth) {
                break;
            }
        }
    }
    return substr(explode(DIRECTORY_SEPARATOR, substr($path, $i + 1, $len))[0], 0, DATE_MATCH_LEN);
}


/**
 * This function is to replace PHP's extremely buggy realpath().
 * @param string The original path, can be relative etc.
 * @return string The resolved path, it might not exist.
 */
function truepath(string $path, ?bool $symlink = false, ?bool $relative = false): string
{
    // whether $path is unix or not
    $unipath = strlen($path) == 0 || $path[0] != '/';

    // attempts to detect if path is relative in which case, add cwd
    if ($relative && strpos($path, ':') === false && $unipath) {
        $path = getcwd() . DIRECTORY_SEPARATOR . $path;
    }

    // resolve path parts (single dot, double dot and double delimiters)
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = [];

    foreach ($parts as $part) {
        if ('.'  == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }

    $path = implode(DIRECTORY_SEPARATOR, $absolutes);

    // resolve any symlinks if needed
    if ($symlink && file_exists($path) && linkinfo($path) > 0) {
        $path = readlink($path);
    }
    // put initial separator that could have been lost
    $path = !$unipath ? '/' . $path : $path;

    return $path;
}
