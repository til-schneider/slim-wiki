<?php

require_once __DIR__ . '/../lib/parsedown/Parsedown.php';

class Main {

    // Parameters:
    // - $baseUrl: E.g. 'http://localhost/slim-wiki/'
    // - $appPath: E.g. '/slim-wiki'
    // - $requestPathArray: E.g. array('myfolder', 'mypage')
    public function dispatch($baseUrl, $appPath, $requestPathArray) {

        $articleBaseDir = realpath(__DIR__ . '/../../articles');
        $articleFilename = $articleBaseDir . '/' . implode('/', $requestPathArray);

        if (($articleFilename == realpath($articleFilename)) && file_exists($articleFilename) && is_readable($articleFilename)) {
            $articleContent = file_get_contents($articleFilename);
            $articleMarkup = Parsedown::instance()->text($articleContent);
            include(__DIR__ . '/../layout/page.php');
        } else {
            // TODO: Show error page
            echo '<p style="color:#990000">File does not exist or is not readable: '.$articleFilename.'</p>';
        }

    }

}
