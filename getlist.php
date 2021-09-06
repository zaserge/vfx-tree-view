<?php
    header('Cache-Control: no-cache'); 
    header('Content-type: text/html; charset=utf-8');

    $configData = yaml_parse_file(__DIR__ . '/config.yaml');
        
    function printProgress($s)
    {
        echo "<li>", explode(DIRECTORY_SEPARATOR, $s)[0], "</li>";
        ob_flush();
        usleep(10000);
    }

    $order = $_GET['order'];

    switch ($order) {
        case 'scene':
            echo "<ul id='progress'>";
            $scenelist = walkByScenes($configData);
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
                foreach ($data as $shot)
                {
                    if (strcmp($index, $shot['index']) != 0)
                    {
                        $index = $shot['index'];
                        $rowclass =! $rowclass;
                    }
                    echo "<li class='", ($rowclass ? "raw1" : "raw2"), " ", $shot['status'], "'>";
                    echo "<span class='shotname'>", $shot['shot'];
                    echo "<div class='infotext'>";
                    echo "<p>Vendor: ", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "</p>";
                    echo "<p>Date: ", $shot['date'], "</p>";
                    echo "<p>Path: ", $configData['vendordir'], DIRECTORY_SEPARATOR, $shot['vendor'], DIRECTORY_SEPARATOR, $shot['date'], "</p>";
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
            echo "<ul id='progress'>";
            $datelist = walkByDates($configData);
            echo "</ul>"; # id='progress'

            echo "<ul class='listdates'>";

            krsort($datelist, SORT_STRING | SORT_FLAG_CASE);
            foreach ($datelist as $date => $data)
            {
                echo "\n<li class='date'>";
                $c = count($data);
                echo "<div class='toggleitem'>", $date, "<span class='count'>", $c, ($c > 1 ? " shots" : " shot"), "</span></div>";

                echo "<div class='li-content'>";
                echo "<ul class='listshots'>"; 
                ksort($data, SORT_STRING | SORT_FLAG_CASE);
                $index = "";
                $rowclass = false;
                foreach ($data as $shot)
                {
                    if (strcmp($index, $shot['index']) != 0)
                    {
                        $index = $shot['index'];
                        $rowclass =! $rowclass;
                    }                        
                    echo "<li class='", ($rowclass ? "raw1" : "raw2"), " ", $shot['status'], "'>";
                    echo "<span class='shotname'>", $shot['shot'];
                    echo "<div class='infotext'>";
                    echo "<p>Vendor: ", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "</p>";
                    echo "<p>Date: ", $shot['date'], "</p>";
                    echo "<p>Path: ", $configData['vendordir'], DIRECTORY_SEPARATOR, $shot['vendor'], DIRECTORY_SEPARATOR, $shot['date'], "</p>";
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
            echo "<ul id='progress'>";
            $vendorlist = walkByVendorsDates($configData);
            echo "</ul>"; # id='progress'
/*            echo "<pre>";
            print_r($list);
            echo "</pre>";
*/

            echo "<ul class='listvendors'>";

            ksort($vendorlist, SORT_STRING | SORT_FLAG_CASE);
            foreach ($vendorlist as $vendor => $datelist)
            {
                echo "\n<li class='vendor'>";
                $c = count($datelist);
                echo "<div class='toggleitem'>", explode(DIRECTORY_SEPARATOR, $vendor)[0], "<span class='count'>", $c, ($c > 1 ? " dates" : " date"), "</span></div>";

                echo "<div class='li-content'>";
                echo "<ul class='listdates'>";

                krsort($datelist, SORT_STRING | SORT_FLAG_CASE);
                foreach ($datelist as $date => $data)
                {
                    echo "\n<li class='date'>";
                    $c = count($data);
                    echo "<div class='toggleitem'>", $date, "<span class='count'>", $c, ($c > 1 ? " shots" : " shot"), "</span></div>";
    
                    echo "<div class='li-content'>";
                    echo "<ul class='listshots'>"; 
                    ksort($data, SORT_STRING | SORT_FLAG_CASE);
                    $index = "";
                    $rowclass = false;
                    foreach ($data as $shot)
                    {
                        if (strcmp($index, $shot['index']) != 0)
                        {
                            $index = $shot['index'];
                            $rowclass =! $rowclass;
                        }                        
                        echo "<li class='", ($rowclass ? "raw1" : "raw2"), " ", $shot['status'], "'>";
                        echo "<span class='shotname'>", $shot['shot'];
                        echo "<div class='infotext'>";
                        echo "<p>Vendor: ", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "</p>";
                        echo "<p>Date: ", $shot['date'], "</p>";
                        echo "<p>Path: ", $configData['vendordir'], DIRECTORY_SEPARATOR, $shot['vendor'], DIRECTORY_SEPARATOR, $shot['date'], "</p>";
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
            echo "<ul id='progress'>";
            $vendorlist = walkByVendorsScenes($configData);
            echo "</ul>"; # id='progress'
/*             echo "<pre>";
            print_r($vendorlist);
            echo "</pre>";
 */
            echo "<ul class='listvendors'>";

            ksort($vendorlist, SORT_STRING | SORT_FLAG_CASE);
            foreach ($vendorlist as $vendor => $scenelist)
            {
                echo "\n<li class='vendor'>";
                $c = count($scenelist);
                echo "<div class='toggleitem'>", explode(DIRECTORY_SEPARATOR, $vendor)[0], "<span class='count'>", $c, ($c > 1 ? " scenes" : " scene"), "</span></div>";

                echo "<div class='li-content'>";
                echo "<ul class='listscenes'>";

                krsort($scenelist, SORT_STRING | SORT_FLAG_CASE);
                foreach ($scenelist as $date => $data)
                {
                    echo "\n<li class='scene'>";
                    $c = count($data);
                    echo "<div class='toggleitem'>", $date, "<span class='count'>", $c, ($c > 1 ? " scenes" : " scene"), "</span></div>";
    
                    echo "<div class='li-content'>";
                    echo "<ul class='listshots'>"; 
                    ksort($data, SORT_STRING | SORT_FLAG_CASE);
                    $index = "";
                    $rowclass = false;
                    foreach ($data as $shot)
                    {
                        if (strcmp($index, $shot['index']) != 0)
                        {
                            $index = $shot['index'];
                            $rowclass =! $rowclass;
                        }                        
                        echo "<li class='", ($rowclass ? "raw1" : "raw2"), " ", $shot['status'], "'>";
                        echo "<span class='shotname'>", $shot['shot'];
                        echo "<div class='infotext'>";
                        echo "<p>Vendor: ", explode(DIRECTORY_SEPARATOR, $shot['vendor'])[0], "</p>";
                        echo "<p>Date: ", $shot['date'], "</p>";
                        echo "<p>Path: ", $configData['vendordir'], DIRECTORY_SEPARATOR, $shot['vendor'], DIRECTORY_SEPARATOR, $shot['date'], "</p>";
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
            echo "Wrong options";
    }


     /**
     * walkByScenes
     *
     * @param  mixed $configData
     * @return array $shotList
     */
    function walkByScenes($configData)
    {
        $reValid=$configData['regexp']['valid'];
        $reWarn = $configData['regexp']['warn'];
    
        $shotList = [];

        foreach ($configData['vendors'] as $vendor)
        {
            printProgress($vendor);

            $venDir = $configData['vendordir'] . DIRECTORY_SEPARATOR . $vendor;    
            foreach (scandir($venDir) as $date)
            {
                $datePath = $venDir . DIRECTORY_SEPARATOR . $date;

                if (is_dir($datePath) && !str_starts_with($date, ".") && preg_match('/^\d+$/', $date))
                {
                    foreach (scandir($datePath) as $item)
                    {
                        if (!str_starts_with($item, ".") )
                        {
                            if (preg_match($reValid, $item, $matches))
                            {
                                $matches['scene'] = strtoupper($matches['scene']);
                                if (!isset($shotList[$matches['scene']]))
                                {
                                    $shotList[$matches['scene']] = [];
                                }
                                $shotList[$matches['scene']][$item] =  
                                    [ "shot" => $item, 
                                    "scene" => $matches['scene'],
                                    "index" => $matches['index'],
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "valid"
                                    ];
                            }
                            elseif (preg_match($reWarn, $item, $matches))
                            {
                                $matches['scene'] = strtoupper($matches['scene']);
                                if (!isset($shotList[$matches['scene']]))
                                {
                                    $shotList[$matches['scene']] = [];
                                }
                                $shotList[$matches['scene']][$item] =
                                    [ "shot" => $item, 
                                    "scene" => $matches['scene'],
                                    "index" => $matches['index'],
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "warn"
                                    ];
                            }
                        }
                    }
                }
            }
        }
        return ($shotList);
    }   
        
    /**
     * walkByDates
     *
     * @param  mixed $configData
     * @return array $shotList
     */
    function walkByDates($configData)
    {
        $reValid = $configData['regexp']['valid'];
        $reWarn = $configData['regexp']['warn'];
    
        $shotList = [];

        foreach ($configData['vendors'] as $vendor)
        {
            printProgress($vendor);

            $venDir = $configData['vendordir'] . DIRECTORY_SEPARATOR . $vendor;    

            foreach (scandir($venDir) as $date) {
                $datePath = $venDir . DIRECTORY_SEPARATOR . $date;

                if (is_dir($datePath) && !str_starts_with($date, ".") && preg_match('/^\d+$/', $date))
                {
                    $shotList[$date] = [];

                    foreach (scandir($datePath) as $item)
                    {
                        if (!str_starts_with($item, ".") )
                        {
                            if (preg_match($reValid, $item, $matches))
                            {
                                array_push($shotList[$date], 
                                    [ "shot" => $item,
                                    "scene" => $matches['scene'],
                                    "index" => $matches['index'],
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "valid" 
                                    ]);
                            }
                            elseif (preg_match($reWarn, $item, $matches))
                            {
                                array_push($shotList[$date], 
                                    [ "shot" => $item, 
                                    "scene" => $matches['scene'],
                                    "index" => $matches['index'],
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "warn"
                                    ]);
                            }
                            else
                            {
                                array_push($shotList[$date], 
                                    [ "shot" => $item, 
                                    "scene" => "",
                                    "index" => "",
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "error"
                                    ]);
                            }
                        }
                    }
                }
            }
        }
        return ($shotList);
    }

    /**
     * walkByVendors
     *
     * @param  mixed $configData
     * @return array $shotList
     */
    function walkByVendorsDates($configData) : array
    {
        $reValid = $configData['regexp']['valid'];
        $reWarn = $configData['regexp']['warn'];
    
        $shotList = [];

        foreach ($configData['vendors'] as $vendor)
        {
            printProgress($vendor);

            $shotList[$vendor] = [];
            $venDir = $configData['vendordir'] . DIRECTORY_SEPARATOR . $vendor;    

            foreach (scandir($venDir) as $date)
            {
                $datePath = $venDir . DIRECTORY_SEPARATOR . $date;

                if (is_dir($datePath) && !str_starts_with($date, ".") && preg_match('/^\d+$/', $date))
                {
                    $shotList[$vendor][$date] = [];

                    foreach (scandir($datePath) as $item)
                    {
                        if (!str_starts_with($item, ".") )
                        {
                            if (preg_match($reValid, $item, $matches))
                            {
                                array_push($shotList[$vendor][$date], 
                                    [ "shot" => $item,
                                    "scene" => $matches['scene'],
                                    "index" => $matches['index'],
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "valid" 
                                    ]);
                            }
                            elseif (preg_match($reWarn, $item, $matches))
                            {
                               array_push($shotList[$vendor][$date], 
                                    [ "shot" => $item, 
                                    "scene" => $matches['scene'],
                                    "index" => $matches['index'],
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "warn"
                                    ]);
                            }
                            else
                            {
                                array_push($shotList[$vendor][$date], 
                                    [ "shot" => $item, 
                                    "scene" => "",
                                    "index" => "",
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "error"
                                    ]);
                            }
                        }
                    }
                }
            }
        }
        return ($shotList);
    }

    /**
     * walkByVendors
     *
     * @param  mixed $configData
     * @return array $shotList
     */
    function walkByVendorsScenes($configData) : array
    {
        $reValid = $configData['regexp']['valid'];
        $reWarn = $configData['regexp']['warn'];
    
        $shotList = [];

        foreach ($configData['vendors'] as $vendor)
        {
            printProgress($vendor);

            $shotList[$vendor] = [];
            $venDir = $configData['vendordir'] . DIRECTORY_SEPARATOR . $vendor;    

            foreach (scandir($venDir) as $date)
            {
                $datePath = $venDir . DIRECTORY_SEPARATOR . $date;

                if (is_dir($datePath) && !str_starts_with($date, ".") && preg_match('/^\d+$/', $date))
                {
                    foreach (scandir($datePath) as $item)
                    {
                        if (!str_starts_with($item, ".") )
                        {
                            if (preg_match($reValid, $item, $matches))
                            {
                                $matches['scene'] = strtoupper($matches['scene']);
                                if (!isset($shotList[$vendor][$matches['scene']]))
                                {
                                    $shotList[$vendor][$matches['scene']] = [];
                                }
                                $shotList[$vendor][$matches['scene']][$item] =  
                                    [ "shot" => $item, 
                                    "scene" => $matches['scene'],
                                    "index" => $matches['index'],
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "valid"
                                    ];
                            }
                            elseif (preg_match($reWarn, $item, $matches))
                            {
                                $matches['scene'] = strtoupper($matches['scene']);
                                if (!isset($shotList[$vendor][$matches['scene']]))
                                {
                                    $shotList[$vendor][$matches['scene']] = [];
                                }
                                $shotList[$vendor][$matches['scene']][$item] =
                                    [ "shot" => $item, 
                                    "scene" => $matches['scene'],
                                    "index" => $matches['index'],
                                    "vendor" => $vendor, 
                                    "date" => $date, 
                                    "status" => "warn"
                                    ];
                            }
                        }
                    }
                }
            }
        }
        return ($shotList);
    }
