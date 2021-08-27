<!DOCTYPE html>
<html>
<head>
    <style>
        .info {
            display: inline-block;
            margin-left: 20px;   
            color: silver;
        }
    </style>
</head>
<body>

<pre>
    <?php
        $configData = yaml_parse_file(__DIR__ . '/config.yaml');

        $order = $_GET['order'] ?? 'vendor';
        switch ($order) {
            case 'scene':
                $list = walkByScenes($configData);

                echo "<ul>";
                ksort($list, SORT_STRING | SORT_FLAG_CASE);
                foreach ($list as $scene => $data) {
                    echo "\n<li>", $scene;
                    echo "<ul>"; 
                    ksort($data, SORT_STRING | SORT_FLAG_CASE);
                    foreach ($data as $shot) {
                        echo "<li>", $shot['shot'];
                        echo "<div class=info>", explode("/", $shot['vendor'])[0], " ", $shot['date'], "</div>";
                        echo "</li>";
                    }
                    echo "</ul>";    
                    echo "</li>\n";              
                }
                echo "</ul>";

                break;
            case 'date':
                walkByDates($configData);
                break;
            case 'vendor':
                walkByVendors($configData);
                break;
        }
            
        function walkByVendors($configData)
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
                                                "status" => "VALID" 
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
                                                "status" => "WARN"
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
                                                "status" => "ERROR"
                                                ]);
                                }
                            }
                        }
                    }
                }
            }
            return ($shotList);
        }

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
                                                "status" => "WARN"
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
                                                "status" => "WARN"
                                                ];
                                }
                            }
                        }
                    }
                }
            }
            return ($shotList);
        }   
        
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
                                                "status" => "VALID" 
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
                                                "status" => "WARN"
                                                ]);
                                }
                            }
                        }
                    }
                }
            }
            print_r($shotList);
        }
    ?>
    <script>
        var coll = document.getElementsByClassName("collapsible");
        var i;

        for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.maxHeight){
                    content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
            } 
        });
        }
    </script>

</body>
</html>