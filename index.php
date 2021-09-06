<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    $configData = yaml_parse_file(__DIR__ . '/config.yaml');
    if ($configData == false) {
        echo "No config files";
        exit(-1);
    }
    ?>

    <div class='buttons'>
        <div class='button'><a href='.?order=scene'>by scenes</a></div>
        <div class='button'><a href='.?order=date'>by dates</a></div>
        <div class='button'><a href='.?order=vendordate'>by vendor then date</a></div>
        <div class='button'><a href='.?order=vendorscene'>by vendor then scene</a></div>
    </div>

    <?php
    echo "<h2 class='toptitle'>", $configData['title'], "</h2>";

    date_default_timezone_set($configData['timezone']);
    echo "<p class='timestamp'>", date("F j, Y, H:i:s T"), "</p>";
    ?>

    <div id='getlist'></div>
    <script src="getlist.js"></script>

</body>

</html>