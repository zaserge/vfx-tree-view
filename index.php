<!DOCTYPE html>
<html>
<head>
</head>
<body>

<pre>
    <?php
        $configData = yaml_parse_file(__DIR__ . '/config.yaml');

        $order = $_GET['name'] ?? 'vendor';
        switch ($order) {
            case 'scene':
                walkByScenes($configData);
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
            print_r($shotList);
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
                                    if (!isset($shotList[$matches['scene']]))
                                        $shotList[$matches['scene']] = [];
                                    array_push($shotList[$matches['scene']], 
                                                [ "shot" => $item, 
                                                "scene" => $matches['scene'],
                                                "vendor" => $vendor, 
                                                "date" => $date, 
                                                "status" => "WARN"
                                                ]);
                                }
                                elseif (preg_match($reWarn, $item, $matches)) {
                                    if (!isset($shotList[$matches['scene']]))
                                        $shotList[$matches['scene']] = [];
                                    array_push($shotList[$matches['scene']], 
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