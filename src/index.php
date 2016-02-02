<?php

function init() {
    ini_set('display_errors', 1);

    // Split URI              example: 'http://localhost/slim-wiki/myfolder/mypage?action=bla'
    // into $appPath          example: '/slim-wiki'
    // and $requestPathArray  example: array('myfolder', 'mypage')

    $uriParts        = parse_url($_SERVER['REQUEST_URI']);
    $uriPathArray    = explode("/", $uriParts['path']);
    $scriptPathArray = explode("/", dirname($_SERVER['SCRIPT_NAME']));

    $basePathArray = array();
    $requestPathArray = array();
    $isBasePath = true;
    foreach ($uriPathArray as $level => $uriDir) {
        $scriptDir = isset($scriptPathArray[$level]) ? $scriptPathArray[$level] : null;
        if ($isBasePath && $scriptDir != $uriDir) {
            // The URI path differs from the script path here -> We arrived at the level where the app is installed
            $isBasePath = false;
        }

        if ($isBasePath) {
            $basePathArray[] = $uriDir;
        } else {
            $requestPathArray[] = $uriDir;
        }
    }
    $basePath = rtrim(implode('/', $basePathArray), '/') . '/';

    $https = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $https = true;
    }
    $baseUrl = 'http' . ($https ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $basePath;

    require_once __DIR__ . '/server/logic/Main.php';

    (new Main())->dispatch($baseUrl, $basePath, $requestPathArray, isset($uriParts['query']) ? $uriParts['query'] : '');
}

init();
