<?php

require_once __DIR__ . '/../lib/parsedown/Parsedown.php';

class Main {

    // Parameters:
    // - $baseUrl:          E.g. 'http://localhost/slim-wiki/'
    // - $basePath:         E.g. '/slim-wiki/'
    // - $requestPathArray: E.g. array('myfolder', 'mypage')
    public function dispatch($baseUrl, $basePath, $requestPathArray) {
        $articleBaseDir = realpath(__DIR__ . '/../../articles') . '/';
        $articleFilename = $articleBaseDir . implode('/', $requestPathArray);

        if (is_dir($articleFilename)) {
            $articleFilename = rtrim($articleFilename, '/') . '/index.md';
        }

        if (($articleFilename == realpath($articleFilename)) && file_exists($articleFilename) && is_readable($articleFilename)) {
            $wikiName = 'Slim Wiki'; // TODO: Make this configurable

            $data = array();
            $data['baseUrl']  = $baseUrl;
            $data['basePath'] = $basePath;
            $data['wikiName'] = $wikiName;

            $data['breadcrumbs'] = $this->createBreadcrumbs($articleBaseDir, $requestPathArray, $wikiName);

            $articleContent = file_get_contents($articleFilename);
            $data['articleHtml'] = Parsedown::instance()->text($articleContent);

            $this->renderPage($data);
        } else {
            // TODO: Show error page
            echo '<p style="color:#990000">File does not exist or is not readable: '.$articleFilename.'</p>';
        }

    }


    private function createBreadcrumbs($articleBaseDir, $requestPathArray, $wikiName) {
        $pathCount = count($requestPathArray);
        $breadcrumbArray = array(array('name' => $wikiName, 'path' => '', 'active' => ($pathCount == 0)));

        $currentPath = '';
        for ($i = 0; $i < $pathCount; $i++) {
            $pathPart = $requestPathArray[$i];
            $currentPath .= ($i == 0 ? '' : '/') . $pathPart;
            $isLast = ($i == $pathCount - 1);

            if ($isLast || file_exists($articleBaseDir . $currentPath . '/index.md')) {
                // This is the requested file or an directory having an index -> Add it
                $breadcrumbArray[] = array('name' => $pathPart, 'path' => urlencode($currentPath), 'active' => $isLast);
            }
        }

        return $breadcrumbArray;
    }


    private function renderPage($data) {
        include(__DIR__ . '/../layout/page.php');
    }

}
