<?php

require_once __DIR__ . '/Context.php';

class Main {

    private static $singleton;

    private $context;


    private function __construct() {
        $this->context = new Context();
    }

    public static function get() {
        if (is_null(self::$singleton)) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    // Parameters:
    // - $baseUrl:          E.g. 'http://localhost/slim-wiki/?edit'
    // - $basePath:         E.g. '/slim-wiki/'
    // - $requestPathArray: E.g. array('myfolder', 'mypage')
    // - $requestQuery:     E.g. 'edit'
    public function dispatch($baseUrl, $basePath, $requestPathArray, $requestQuery) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handlePost($requestPathArray);
        } else {
            $this->handleGet($baseUrl, $basePath, $requestPathArray, $requestQuery);
        }
    }

    private function handlePost($requestPathArray) {
        if (count($requestPathArray) == 2 && $requestPathArray[0] == 'rpc') {
            $requestData = json_decode(file_get_contents('php://input'), true);

            $objectName = $requestPathArray[1];
            $object = null;
            if ($objectName == 'editor') {
                $object = $this->context->getEditorService();
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

    private function handleGet($baseUrl, $basePath, $requestPathArray, $requestQuery) {
        if ($requestQuery == 'edit' || $requestQuery == 'createUser') {
            $mode = $requestQuery;
        } else {
            $mode = 'view';
        }

        $articleFilename = $this->getArticleFilename($requestPathArray);
        if ($articleFilename == null) {
            header('HTTP/1.0 404 Not Found');
            header('Content-Type:text/html; charset=utf-8');
            echo '<h1>File not found</h1>'; // TODO: Show error page
        } else {
            $config = $this->context->getConfig();

            $data = array();
            $data['baseUrl']    = $baseUrl;
            $data['basePath']   = $basePath;
            $data['mode'] = $mode;

            foreach (array('wikiName', 'footerHtml') as $key) {
                $data[$key] = $config[$key];
            }

            $data['breadcrumbs'] = $this->createBreadcrumbs($requestPathArray, $config['wikiName']);

            $data['articleFilename'] = $articleFilename;
            $articleMarkdown = file_get_contents($this->context->getArticleBaseDir() . $articleFilename);
            $data['articleMarkdown'] = $articleMarkdown;
            $data['articleHtml'] = $this->context->getRenderService()->renderMarkdown($articleMarkdown, $mode == 'edit');

            $this->renderPage($data);
        }
    }

    private function getArticleFilename($requestPathArray) {
        $articleBaseDir = $this->context->getArticleBaseDir();
        $articleFilename = implode('/', $requestPathArray);

        // Support `index.md` for directories
        if (is_dir($articleBaseDir . $articleFilename)) {
            $articleFilename = rtrim($articleFilename, '/') . '/index.md';
        }

        // Make the extension `.md` optional
        if (! file_exists($articleBaseDir . $articleFilename) && file_exists($articleBaseDir . $articleFilename . '.md')) {
            $articleFilename .= '.md';
        }

        $articleFullFilename = $articleBaseDir . $articleFilename;
        if (! $this->context->isValidArticleFilename($articleFilename)) {
            // Attempt to break out of article base directory (e.g. `../../outside.ext`)
            return null;
        } else if (file_exists($articleFullFilename) && is_readable($articleFullFilename)) {
            return $articleFilename;
        } else {
            return null;
        }
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

        $i18n = $this->context->getI18n();
        include(__DIR__ . '/../layout/page.php');
    }

}
