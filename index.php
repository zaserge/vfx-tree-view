<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin-left: 100px;
            width: 700px;
        }
        ul {
            font-family: sans-serif;         
        }
        .listscenes {
            font-weight: bold;
            font-size: 1.2em;
            list-style-image: url(images/clapperboard.png);
        }         
        .listdates {
            font-weight: bold;
            font-size: 1.2em;
            list-style-image: url(images/calendar.png);
        } 
        .toggleitem {
            cursor: pointer;
            padding: 5px;
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
        .shotname {
            padding: 3px;
            cursor: pointer;
        }
        .shotname:hover {
            background-color: #DDD;
        }
        .content {
            display: none;
            background-color: #EEE;
        }
        .count {
            font-size: 0.6em;
            font-weight: normal;
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
        .shotname .infotext {
            visibility: hidden;
            width: 300px;
            height: 100px;
            border: 1px solid black;
            position: absolute;
            z-index: 1;
            left: 500px;
        }
        .shotname:hover .infotext {
            #visibility: visible;
        }
        .info {
            padding: 20px;  
            font-size: 1em; 
            color: silver;
        }
    </style>
</head>
<body>

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
                    echo "<div class='toggleitem'>", $scene, "<span class='count'>", count($data)  ," shot(s)</span></div>";

                    echo "<div class='content'>";
                    echo "<ul class='listshots'>"; 
                    ksort($data, SORT_STRING | SORT_FLAG_CASE);
                    foreach ($data as $shot) {
                        echo "<li class='", $shot['status'], "'><span class='shotname'>", $shot['shot'], "</span>";
                        echo "<span class='info'>", explode("/", $shot['vendor'])[0], " ", $shot['date'], "</span>";
                        echo "</li>";
                    }                    
                    echo "</ul>";    

                    echo "</div>";
                    echo "</li>";              
                }
                echo "</ul>";

                break;
            case 'date':
                $list = walkByDates($configData);

                echo "<ul class='listdates'>";
                krsort($list, SORT_STRING | SORT_FLAG_CASE);
                foreach ($list as $date => $data) {
                    echo "\n<li >";
                    echo "<div class='toggleitem'>", $date, "<span class='count'>", count($data)  ," shot(s)</span></div>";

                    echo "<div class='content'>";
                    echo "<ul class='listshots'>"; 
                    ksort($data, SORT_STRING | SORT_FLAG_CASE);
                    foreach ($data as $shot) {
                        echo "<li class='", $shot['status'], "'><span class='shotname'>", $shot['shot'];
                        echo "<div class='infotext'>sdfsdgdgdfdfs</div>";
                        echo "</span>";
                        echo "<span class='info'>", explode("/", $shot['vendor'])[0], "</span>";
                        echo "</li>";
                    }                    
                    echo "</ul>";    

                    echo "</div>";
                    echo "</li>";              
                }
                echo "</ul>";
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