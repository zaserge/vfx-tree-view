<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php
        $configData = yaml_parse_file(__DIR__ . '/config.yaml');

        echo "<h2>", $configData['title'], "</h2>";

        date_default_timezone_set($configData['timezone']);
        echo "<p class='timestamp'>", date("F j, Y, H:i:s"), "</p>";

        $order = $_GET['order'] ?? 'vendor';
        switch ($order) {
            case 'scene':
                $list = walkByScenes($configData);

                echo "<ul class='listscenes'>";
                ksort($list, SORT_STRING | SORT_FLAG_CASE);
                foreach ($list as $scene => $data) {
                    echo "\n<li >";
                    $c = count($data);
                    echo "<div class='toggleitem'>", $scene, "<span class='count'>", $c, $c > 1 ? " shots" : " shot ", "</span></div>";

                    echo "<div class='content'>";
                    echo "<ul class='listshots'>"; 
                    ksort($data, SORT_STRING | SORT_FLAG_CASE);
                    foreach ($data as $shot) {
                        echo "<li class='", $shot['status'], "'><span class='shotname'>", $shot['shot'];
                        echo "<div class='infotext'>";
                        echo "<p>Vendor: ", explode("/", $shot['vendor'])[0], "</p>";
                        echo "<p>Date: ", $shot['date'], "</p>";
                        echo "<p>Path: ", $configData['vendordir'] . '/' . $shot['vendor'] . '/' . $shot['date'], "</p>";
                        echo "</div>";
                        echo "</span>";
                        echo "<span class='info'>", explode("/", $shot['vendor'])[0], " ", $shot['date'], "</span>";
                        echo "</li>";
                    }                    
                    echo "</ul>";    

                    echo "</div>";
                    echo "</li>";              
                }
                echo "</ul>";

                $c = count($list);
                echo "<p class='total'>", $c, $c > 1 ? " scenes" : " scene ", "</p>";

                break;
            case 'date':
                $list = walkByDates($configData);

                echo "<ul class='listdates'>";
                krsort($list, SORT_STRING | SORT_FLAG_CASE);
                foreach ($list as $date => $data) {
                    echo "\n<li >";
                    $c = count($data);
                    echo "<div class='toggleitem'>", $date, "<span class='count'>", $c, $c > 1 ? " shots" : " shot ", "</span></div>";

                    echo "<div class='content'>";
                    echo "<ul class='listshots'>"; 
                    ksort($data, SORT_STRING | SORT_FLAG_CASE);
                    foreach ($data as $shot) {
                        echo "<li class='", $shot['status'], "'><span class='shotname'>", $shot['shot'];
                        echo "<div class='infotext'>";
                        echo "<p>Vendor: ", explode("/", $shot['vendor'])[0], "</p>";
                        echo "<p>Date: ", $shot['date'], "</p>";
                        echo "<p>Path: ", $configData['vendordir'] . '/' . $shot['vendor'] . '/' . $shot['date'], "</p>";
                        echo "</div>";
                        echo "</span>";
                        echo "<span class='info'>", explode("/", $shot['vendor'])[0], "</span>";
                        echo "</li>";
                    }                    
                    echo "</ul>";    

                    echo "</div>";
                    echo "</li>";              
                }
                echo "</ul>";

                $c = count($list);
                echo "<p class='total'>", $c, $c > 1 ? " dates" : " date ", "</p>";

                break;
            case 'vendor':
                walkByVendors($configData);
                break;
        }
            
                
        /**
         * walkByVendors
         *
         * @param  mixed $configData
         * @return array $shotList
         */
        function walkByVendors($configData) : array
        {
            $reValid = $configData['regexp']['valid'];
            $reWarn = $configData['regexp']['warn'];
        
            $shotList = [];

            foreach ($configData['vendors'] as $vendor) {
                $venDir = $configData['vendordir'] . '/' . $vendor;    
                foreach (scandir($venDir) as $date) {
                    $datePath = $venDir . '/' . $date;

                    if (is_dir($datePath) && !str_starts_with($date, ".") && preg_match('/^\d+$/', $date)) {
                        foreach (scandir($datePath) as $item) {
                            if (!str_starts_with($item, ".") ) {
                                if (preg_match($reValid, $item, $matches)) {
                                    if (!isset($shotList[$vendor]))
                                        $shotList[$vendor] = [];
                                    if (!isset($shotList[$vendor][$date]))
                                        $shotList[$vendor][$date] = [];
                                    array_push($shotList[$vendor][$date], 
                                                [ "shot" => $item,
                                                "scene" => $matches['scene'],
                                                "vendor" => $vendor, 
                                                "date" => $date, 
                                                "status" => "valid" 
                                                ]);
                                }
                                elseif (preg_match($reWarn, $item, $matches)) {
                                    if (!isset($shotList[$vendor]))
                                        $shotList[$vendor] = [];
                                    if (!isset($shotList[$vendor][$date]))
                                        $shotList[$vendor][$date] = [];
                                    array_push($shotList[$vendor][$date], 
                                                [ "shot" => $item, 
                                                "scene" => $matches['scene'],
                                                "vendor" => $vendor, 
                                                "date" => $date, 
                                                "status" => "warn"
                                                ]);
                                }
                                else {
                                    if (!isset($shotList[$vendor]))
                                        $shotList[$vendor] = [];
                                    if (!isset($shotList[$vendor][$date]))
                                        $shotList[$vendor][$date] = [];
                                    array_push($shotList[$vendor][$date], 
                                                [ "shot" => $item, 
                                                "scene" => "Bad naming",
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
         * walkByScenes
         *
         * @param  mixed $configData
         * @return array $shotList
         */
        function walkByScenes($configData)
        {
            $reValid = $configData['regexp']['valid'];
            $reWarn = $configData['regexp']['warn'];
        
            $shotList = [];

            foreach ($configData['vendors'] as $vendor) {
                $venDir = $configData['vendordir'] . '/' . $vendor;    
                foreach (scandir($venDir) as $date) {
                    $datePath = $venDir . '/' . $date;

                    if (is_dir($datePath) && !str_starts_with($date, ".") && preg_match('/^\d+$/', $date)) {
                        foreach (scandir($datePath) as $item) {
                            if (!str_starts_with($item, ".") ) {
                                if (preg_match($reValid, $item, $matches)) {
                                    $matches['scene'] = strtoupper($matches['scene']);
                                    if (!isset($shotList[$matches['scene']]))
                                        $shotList[$matches['scene']] = [];
                                    $shotList[$matches['scene']][$item] =  
                                                [ "shot" => $item, 
                                                "scene" => $matches['scene'],
                                                "vendor" => $vendor, 
                                                "date" => $date, 
                                                "status" => "valid"
                                                ];
                                }
                                elseif (preg_match($reWarn, $item, $matches)) {
                                    $matches['scene'] = strtoupper($matches['scene']);
                                    if (!isset($shotList[$matches['scene']]))
                                        $shotList[$matches['scene']] = [];
                                        $shotList[$matches['scene']][$item] =
                                                [ "shot" => $item, 
                                                "scene" => $matches['scene'],
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

            foreach ($configData['vendors'] as $vendor) {
                $venDir = $configData['vendordir'] . '/' . $vendor;    
                foreach (scandir($venDir) as $date) {
                    $datePath = $venDir . '/' . $date;

                    if (is_dir($datePath) && !str_starts_with($date, ".") && preg_match('/^\d+$/', $date)) {
                        foreach (scandir($datePath) as $item) {
                            if (!str_starts_with($item, ".") ) {
                                if (preg_match($reValid, $item, $matches)) {
                                    if (!isset($shotList[$date]))
                                        $shotList[$date] = [];
                                    array_push($shotList[$date], 
                                                [ "shot" => $item,
                                                "scene" => $matches['scene'],
                                                "vendor" => $vendor, 
                                                "date" => $date, 
                                                "status" => "valid" 
                                                ]);
                                }
                                elseif (preg_match($reWarn, $item, $matches)) {
                                    if (!isset($shotList[$date]))
                                        $shotList[$date] = [];
                                    array_push($shotList[$date], 
                                                [ "shot" => $item, 
                                                "scene" => $matches['scene'],
                                                "vendor" => $vendor, 
                                                "date" => $date, 
                                                "status" => "warn"
                                                ]);
                                }
                            }
                        }
                    }
                }
            }
            return ($shotList);
        }
    ?>
    <script src="scripts.js"></script>

</body>
</html>