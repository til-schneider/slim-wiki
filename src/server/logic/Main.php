<?php

require_once __DIR__ . '/../lib/parsedown/Parsedown.php';

class Main {

    // Parameters:
    // - $baseUrl:          E.g. 'http://localhost/slim-wiki/'
    // - $basePath:         E.g. '/slim-wiki/'
    // - $requestPathArray: E.g. array('myfolder', 'mypage')
    public function dispatch($baseUrl, $basePath, $requestPathArray) {
        $articleBaseDir = realpath(__DIR__ . '/../../articles') . '/';
        $articleFilename = $this->getArticleFilename($articleBaseDir, $requestPathArray);
        if ($articleFilename == null) {
            header('HTTP/1.0 404 Not Found');
            header('Content-Type:text/html; charset=utf-8');
            echo '<h1>File not found</h1>'; // TODO: Show error page
        } else {
            $config = $this->loadConfig();

            $data = array();
            $data['baseUrl']  = $baseUrl;
            $data['basePath'] = $basePath;

            foreach (array('wikiName', 'footerHtml') as $key) {
                $data[$key] = $config[$key];
            }

            $data['breadcrumbs'] = $this->createBreadcrumbs($articleBaseDir, $requestPathArray, $config['wikiName']);

            $articleContent = file_get_contents($articleFilename);
            $data['articleHtml'] = Parsedown::instance()->text($articleContent);

            $this->renderPage($data);
        }
    }

    private function getArticleFilename($articleBaseDir, $requestPathArray) {
        $articleFilename = $articleBaseDir . implode('/', $requestPathArray);

        // Support `index.md` for directories
        if (is_dir($articleFilename)) {
            $articleFilename = rtrim($articleFilename, '/') . '/index.md';
        }

        // Make the extension `.md` optional
        if (! file_exists($articleFilename) && file_exists($articleFilename . '.md')) {
            $articleFilename .= '.md';
        }

        if ($articleFilename != realpath($articleFilename)) {
            // Attempt to break out of article base directory (e.g. `../../outside.ext`)
            return null;
        } else if (file_exists($articleFilename) && is_readable($articleFilename)) {
            return $articleFilename;
        } else {
            return null;
        }
    }

    private function loadConfig() {
        // Defaults
        $config = array(
            'wikiName' => 'Slim Wiki'
        );

        if (file_exists(__DIR__ . '/../../config.php')) {
            include(__DIR__ . '/../../config.php');
        }

        return $config;
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
                $breadcrumbArray[] = array(
                    'name' => str_replace('_', ' ', $pathPart),
                    'path' => urlencode($currentPath),
                    'active' => $isLast);
            }
        }

        return $breadcrumbArray;
    }

    private function renderPage($data) {
        header('Content-Type:text/html; charset=utf-8');

        include(__DIR__ . '/../layout/page.php');
    }

}
