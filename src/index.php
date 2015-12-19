<?php

ini_set('display_errors', 1);

// Split URI              example: 'http://localhost/slim-wiki/myfolder/mypage?action=bla'
// into $appPath          example: '/slim-wiki'
// and $requestPathArray  example: array('myfolder', 'mypage')

$uriPathArray    = explode("/", parse_url($_SERVER['REQUEST_URI'])['path']);
$scriptPathArray = explode("/", dirname($_SERVER['SCRIPT_NAME']));

$appPathArray = array();
$requestPathArray = array();
$isAppPath = true;
foreach ($uriPathArray as $level => $uriDir) {
    $scriptDir = isset($scriptPathArray[$level]) ? $scriptPathArray[$level] : null;
    if ($isAppPath && $scriptDir != $uriDir) {
        // The URI path differs from the script path here -> We arrived at the level where the app is installed
        $isAppPath = false;
    }

    if ($isAppPath) {
        $appPathArray[] = $uriDir;
    } else {
        $requestPathArray[] = $uriDir;
    }
}
$appPath = rtrim(implode('/', $appPathArray), '/');

$https = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $https = true;
}
$baseUrl = 'http' . ($https ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $appPath;

unset($uriPathArray, $scriptPathArray, $appPathArray, $isAppPath, $https);

require_once __DIR__ . '/server/logic/main.php';

(new Main())->dispatch($baseUrl, $appPath, $requestPathArray);
