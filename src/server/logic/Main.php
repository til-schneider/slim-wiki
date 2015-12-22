<?php

class Main {

    private static $singleton;

    private $articleBaseDir;


    private function __construct() {
        $this->articleBaseDir = realpath(__DIR__ . '/../../articles') . '/';
    }

    public static function get() {
        if (is_null(self::$singleton)) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    // Parameters:
    // - $baseUrl:          E.g. 'http://localhost/slim-wiki/'
    // - $basePath:         E.g. '/slim-wiki/'
    // - $requestPathArray: E.g. array('myfolder', 'mypage')
    public function dispatch($baseUrl, $basePath, $requestPathArray) {
        $config = $this->loadConfig();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handlePost($requestPathArray);
        } else {
            $this->handleGet($baseUrl, $basePath, $requestPathArray, $config);
        }
    }

    private function handlePost($requestPathArray) {
        if (count($requestPathArray) == 2 && $requestPathArray[0] == 'rpc') {
            $requestData = json_decode(file_get_contents('php://input'), true);

            $objectName = $requestPathArray[1];
            $object = null;
            if ($objectName == 'render') {
                require_once __DIR__ . '/RenderService.php';
                $object = RenderService::get();
            }

            $responseData = array(
                'jsonrpc' => '2.0',
                'id' => $requestData['id']
            );
            if ($object == null) {
                $responseData['error'] = array( 'code' => -32601, 'message' => "Object not found: $objectName" );
            } else {
                $methodName = $requestData['method'];

                if (! $object->isRpcMethod($methodName)) {
                    $responseData['error'] = array( 'code' => -32601, 'message' => "Method not found or not public: $objectName.$methodName" );
                } else {
                    try {
                        $responseData['result'] = call_user_func_array(array($object, $methodName), $requestData['params']);
                    } catch (Exception $exc) {
                        $msg = "Calling RPC $objectName.$methodName failed";
                        error_log($msg . ': ' . $exc->getMessage());
                        $responseData['error'] = array( 'code' => -32000, 'message' => $msg );
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode($responseData);
        } else {
            header('HTTP/1.0 404 Not Found');
        }
    }

    private function handleGet($baseUrl, $basePath, $requestPathArray, $config) {
        $isEditMode = isset($requestPathArray[0]) && $requestPathArray[0] == 'edit';
        if ($isEditMode) {
            array_shift($requestPathArray);
        }

        $articleFilename = $this->getArticleFilename($requestPathArray);
        if ($articleFilename == null) {
            header('HTTP/1.0 404 Not Found');
            header('Content-Type:text/html; charset=utf-8');
            echo '<h1>File not found</h1>'; // TODO: Show error page
        } else {
            $data = array();
            $data['baseUrl']    = $baseUrl;
            $data['basePath']   = $basePath;
            $data['isEditMode'] = $isEditMode;

            foreach (array('wikiName', 'footerHtml') as $key) {
                $data[$key] = $config[$key];
            }

            $data['breadcrumbs'] = $this->createBreadcrumbs($requestPathArray, $config['wikiName']);

            $articleMarkdown = file_get_contents($articleFilename);
            $data['articleMarkdown'] = $articleMarkdown;

            require_once __DIR__ . '/RenderService.php';
            $data['articleHtml'] = RenderService::get()->renderMarkdown($articleMarkdown);

            $this->renderPage($data);
        }
    }

    private function getArticleFilename($requestPathArray) {
        $articleFilename = $this->articleBaseDir . implode('/', $requestPathArray);

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

    private function createBreadcrumbs($requestPathArray, $wikiName) {
        $pathCount = count($requestPathArray);
        $breadcrumbArray = array(array('name' => $wikiName, 'path' => '', 'active' => ($pathCount == 0)));

        $currentPath = '';
        for ($i = 0; $i < $pathCount; $i++) {
            $pathPart = $requestPathArray[$i];
            $currentPath .= ($i == 0 ? '' : '/') . $pathPart;
            $isLast = ($i == $pathCount - 1);

            if ($isLast || file_exists($this->articleBaseDir . $currentPath . '/index.md')) {
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
