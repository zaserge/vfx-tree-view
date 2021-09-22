<?php
error_reporting(E_ALL ^ E_WARNING);

$handle = fopen($argv[1], "r");
$count = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        if ($line[0] == "/" || $line[1] == ":") {
            continue;
        }
        $line = trim($line);

        $dir =  "vault" . DIRECTORY_SEPARATOR . truepath($line);
        //echo $dir, "\n";
        mkdir($dir, 0555, true);
        $count++;
        if ($count % 100 == 0) {
            echo "Proceed ", $count, " folders\r";
        }
    }

    fclose($handle);
} else {
    echo "List not found (", $argv[1], ").\n";
    exit(-1);
}

echo "Created ", $count, " folders\n.";




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
        if ('.'  == $part) {
            continue;
        }

        if ('..' == $part) {
            array_pop($absolutes);
            continue;
        }

        $absolutes[] = $part;
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
