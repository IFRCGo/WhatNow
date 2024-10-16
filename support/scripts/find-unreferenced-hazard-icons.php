<?php



define('ROOT_DIR', realpath(__DIR__.'/../../'));

$jsFilePath = ROOT_DIR.'/resources/assets/js/methods/index.js';
$iconsDir = ROOT_DIR.'/resources/assets/img/icons';

$iconFilePaths = glob("$iconsDir/*");
$iconFileNames = array_map(function ($path) {
    return array_pop(explode("/", $path));
}, $iconFilePaths);

$existingRequires = get_existing_requires($jsFilePath);
$missingImages = array_diff(array_filter($iconFileNames, function ($item) {
    if (substr($item, -strlen('@3x.png')) !== '@3x.png') {
        return false;
    }
    return true;
}), array_keys($existingRequires));

print "The following images are referenced under hazardIcon() in index.js:\n\n";
array_walk($existingRequires, function ($v, $k) {
    print "$k (eventTypes = $v)\n";
});
print "\n";
print "The following images are NOT yet referenced:\n\n";
array_walk($missingImages, function ($v) {
    print "$v\n";
});
print "\n";
print "Include the following extra lines in index.js:\n\n";
array_walk($missingImages, function ($v) {
    $value = substr($v, 0, -strlen("@3x.png"));
    print "      case eventType.includes('".$value."'):\n";
    print "        return require('../../img/icons/".$value."@3x.png')\n";
});

function get_existing_requires(string $path)
{
    if (!file_exists($path)) {
        return null;
    }
    $fileContents = file($path);
    $spliced = get_spliced_array_by_match($fileContents, "/hazardIcon/");
    $filtered = get_cases_and_requires($spliced);
    $ret = [];
    array_walk($filtered, function ($v, $k) use (&$ret) {
        $ret[$k] = implode(", ", $v);
    });
    ksort($ret);
    return $ret;
}

function script_die(string $message)
{
    print "*** DIE *** $message\n";
    die();
}

function get_spliced_array_by_match(array $arr, string $regex)
{
    $offset = null;
    foreach ($arr as $index => $line) {
        if ($offset) {
            continue;
        }
        if (preg_match($regex, $line)) {
            $offset = $index;
        }
    }
    return array_splice($arr, $offset);
}

function get_cases_and_requires(array $arr)
{
    $filtered = [];
    foreach ($arr as $line) {
        if (preg_match("/case eventType.includes\('/", $line)) {
            $expl = explode("case eventType.includes('", $line);
            $value = explode("'):", $expl[count($expl)-1])[0];
            array_push($filtered, ['type' => 'case', 'value' => $value]);
            continue;
        }
        if (preg_match("/return require\('/", $line)) {
            $expl = explode("return require('", $line);
            $path = explode("')", $expl[count($expl)-1])[0];
            $pathExpl = explode("/", $path);
            $value = $pathExpl[count($pathExpl)-1];
            array_push($filtered, ['type' => 'require', 'value' => $value]);
        }
    }

    $requires = [];
    $case = null;
    $require = null;
    foreach ($filtered as $item) {
        if ($item['type'] === "case") {
            $case = $item['value'];
        }
        if ($item['type'] === "require") {
            $require = $item['value'];
        }
        if ($case && $require) {
            $requires = push_require($requires, $require, $case);
            $case = null;
            $require = null;
        }
    }
    $requires = push_require($requires, $require, "default");
    return $requires;
}

function push_require(array $requires, string $require, string $case)
{
    if (!isset($requires[$require])) {
        $requires[$require] = [];
    }
    array_push($requires[$require], $case);
    return $requires;
}
