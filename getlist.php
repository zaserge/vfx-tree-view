<?php

/*
MIT License

Copyright (c) 2021 zaserge@gmail.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

include_once('vars.php');
include_once('functions.php');

error_reporting(E_ALL ^ E_WARNING);

header('Cache-Control: no-cache');
header('Content-type: text/html; charset=utf-8');

mb_internal_encoding("UTF-8");

$start = microtime(true);

$configFileName = __DIR__ . DIRECTORY_SEPARATOR . 'config.yaml';

if (! file_exists($configFileName)) {
    echo "<ul class='errors'>";
    echo "<b>ERROR:</b> No config file found: <b>", $configFileName, "</b>";
    echo "</ul>";
    exit(0);
}

$configData = yaml_parse_file($configFileName);
if ($configData === false) {
    echo "<ul class='errors'>";
    echo "<b>ERROR:</b> Syntax error in config file";
    echo "</ul>";
    exit(0);
}

if ($configData['version'] != 2) {
    echo "<ul class='errors'>";
    echo "<b>ERROR:</b> Config must be version 2.";
    echo "</ul>";
    exit(0);
}

array_walk($configData['vendors'], 'normalizePath');
$configData['vault'] =  truepath($configData['vault'], false, false);

$dupVendor = checkDupNames($configData['vendors']);
if ($dupVendor != null) {
    echo "<ul class='errors'>";
    echo "<b>ERROR:</b> Vendor name or path duplicated: <b>", $dupVendor['name'],
         " &#8594; ", $dupVendor['path'], "</b>";
    echo "</ul>";
    exit(0);
}

if (! is_dir($configData['vault'])) {
    echo "<ul class='errors'>";
    echo "<b>ERROR:</b> No Vault path exists: <b>", $configData['vault'], "</b>";
    echo "</ul>";
    exit(0);
}

$configData['_vault_depth'] = countDirDepth($configData['vault']);

date_default_timezone_set($configData['timezone']);
echo "<h2 id='title' class='toptitle'>", $configData['title'], "</h2>";
echo "<div class='timestamp'>", date("F j, Y, H:i:s T");

$order = $_GET['order'];

$warnings = [];

switch ($order) {
    case 'scene':
        echo "<span class='order'><img src='images/clapperboard.png'> SCENES</span></div>";

        echo "<ul id='progress'>";
        $scenelist = walkByScenes($warnings);
        echo "</ul>"; # id='progress'

        if (count($warnings) > 0) {
            echo "<ul class='warnings'>";
            foreach ($warnings as $warn) {
                echo "<li>", $warn, "</li>";
            }
            echo "</ul>\n";
        }

        echo printSceneList($scenelist);

        $count = count($scenelist);
        echo "<p class='total'>", $count, ($count == 1 ? " scene" : " scenes"), "</p>";

        break;

    case 'vendorscene':
        echo "<span class='order'><img src='images/vendor.png'> VENDORS &#8594; SCENES</span></div>";

        echo "<ul id='progress'>";
        $vendorlist = walkByVendorsScenes($warnings);
        echo "</ul>"; # id='progress'

        if (count($warnings) > 0) {
            echo "<ul class='warnings'>";
            foreach ($warnings as $warn) {
                echo "<li>", $warn, "</li>";
            }
            echo "</ul>\n";
        }

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
        $datelist = walkByDates($warnings);
        echo "</ul>"; # id='progress'

        if (count($warnings) > 0) {
            echo "<ul class='warnings'>";
            foreach ($warnings as $warn) {
                echo "<li>", $warn, "</li>";
            }
            echo "</ul>\n";
        }

        echo printDateList($datelist);

        $count = count($datelist);
        echo "<p class='total'>", $count, $count == 1 ? " date" : " dates ", "</p>";

        break;

    case 'vendordate':
        echo "<span class='order'><img src='images/vendor.png'> VENDORS &#8594; DATES</span></div>";

        echo "<ul id='progress'>";
        $vendorlist = walkByVendorsDates($warnings);
        echo "</ul>"; # id='progress'

        if (count($warnings) > 0) {
            echo "<ul class='warnings'>";
            foreach ($warnings as $warn) {
                echo "<li>", $warn, "</li>";
            }
            echo "</ul>\n";
        }

        echo "<ul class='listvendors'>";

        ksort($vendorlist, SORT_STRING | SORT_FLAG_CASE);
        foreach ($vendorlist as $vendor => $datelist) {
            echo "\n<li class='vendor'>";
            $count = count($datelist);
            echo "<div class='toggleitem'>", explode(DIRECTORY_SEPARATOR, $vendor)[0],
                 "<span class='count'>", $count, ($count > 1 ? " dates" : " date"), "</span></div>";

            echo "<div class='li-content'>";

            echo printDateList($datelist);

            echo "</div></li>"; # class='li-content' class='vendor'
        }

        echo "</ul>"; # id='listscenes'

        $count = count($vendorlist);
        echo "<p class='total'>", $count, ($countc == 1 ? " vendor" : " vendors"), "</p>";

        break;

    default:
        echo "<span class='order' style='color:red;'>!!! Wrong options !!!</span></div>";
}

$time_elapsed_secs = microtime(true) - $start;

echo "<p class='copyright'>&copy; ", COPYRIGHT, ", v", VERSION,
     ".&nbsp;&nbsp;&nbsp;Finished in ", number_format($time_elapsed_secs, 3), " sec. Memory usage: ",
     round(memory_get_peak_usage(true) / 1048576, 2), " MB.</p>";
