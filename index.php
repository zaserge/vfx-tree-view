<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin-left: 100px;
            width: 700px;
        }
        .info {
            display: inline-block;
            margin-left: 20px;   
            color: silver;
            font-style: italic;
        }
        .listscenes {
            font-weight: bold;
            font-size: 1.5em;
            list-style-image: url(images/clapperboard.png);
        } 
        
        .toggleitem {
            cursor: pointer;
        }
        .listshots {
            font-family: monospace;
            font-weight: normal;
            font-size: 1rem;
            list-style-type: square;
            list-style-image: none;
        }
        .active, .toggleitem:hover {
            background-color: #DDD;
        }
        .content {
            display: none;
            background-color: #EEE;
        }
        .count {
            font-size: 0.6em;
            font-weight: normal;
            display: inline-block;
            margin-left: 3em;
        }
        .valid::marker {
            color: green;
            font-size: 1.5em;
        }
        .warn::marker {
            color: yellow;
            font-size: 1.5em;
        }
        .error::marker {
            color: red;
            font-size: 1.5em;
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

                echo "<ul class='listscenes'>";
                ksort($list, SORT_STRING | SORT_FLAG_CASE);
                foreach ($list as $scene => $data) {
                    echo "\n<li >";
                    echo "<div class='toggleitem'>", $scene, "<div class='count'>", count($data)  ," shot(s)</div></div>";

                    echo "<div class='content'>";
                    echo "<ul class='listshots'>"; 
                    ksort($data, SORT_STRING | SORT_FLAG_CASE);
                    foreach ($data as $shot) {
                        echo "<li class='", $shot['status'], "'>", $shot['shot'];
                        echo "<div class='info'>", explode("/", $shot['vendor'])[0], " ", $shot['date'], "</div>";
                        echo "</li>";
                    }                    
                    echo "</ul>";    

                    echo "</div>";
                    echo "</li>";              
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
            print_r($shotList);
        }
    ?>
    <script>
        var coll = document.getElementsByClassName("toggleitem");
        var i;

        for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.display === "block") {
                    content.style.display = "none";
                } else {
                    content.style.display = "block";
                }
            });
            } 
    </script>

</body>
</html>