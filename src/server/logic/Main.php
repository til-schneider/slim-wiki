<?php

require_once __DIR__ . '/Context.php';

class Main {

    private $context;


    public function __construct() {
        $this->context = new Context();
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
                        $msg = "Calling RPC $objectName.$methodName failed: " . $exc->getMessage();
                        error_log($msg);
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
        $config = $this->context->getConfig();

        $showCreateUserButton = false;
        if ($requestQuery == 'edit' || $requestQuery == 'createUser') {
            $mode = $requestQuery;
            $showCreateUserButton = ($mode == 'edit');
        } else {
            $mode = 'view';
            $showCreateUserButton = ! $this->isUserDefined();
        }

        if ($mode == 'edit' && ! $config['demoMode']) {
            $loginState = $this->context->getEditorService()->getLoginState();
            if ($loginState != 'logged-in') {
                $wikiName = $this->context->getConfig()['wikiName'];
                header('WWW-Authenticate: Basic realm="'.$wikiName.'"');
                header('HTTP/1.0 401 Unauthorized');

                $mode = 'view';
                $showCreateUserButton = true;
            }
        }

        $articleFilename = $this->getArticleFilename($requestPathArray);
        if ($articleFilename == null) {
            header('HTTP/1.0 403 Forbidden');
            header('Content-Type:text/html; charset=utf-8');
            echo '<h1>Forbidden</h1>';
        } else {
            $renderService = $this->context->getRenderService();

            $fatalErrorMessage = null;
            if ($mode == 'view') {
                if (! $renderService->articleExists($articleFilename)) {
                    $mode = 'noSuchArticle';
                }
            } else if ($mode == 'edit') {
                $editorService = $this->context->getEditorService();
                $fatalErrorMessage = $editorService->checkForError($articleFilename);
                if ($fatalErrorMessage != null) {
                    $fatalErrorMessage = $this->context->getI18n()['error.editingArticleFailed'] . '<br/>' . $fatalErrorMessage;
                    $mode = 'error';
                } else if (! $renderService->articleExists($articleFilename)) {
                    $mode = 'createArticle';
                    $showCreateUserButton = false;
                }
            }

            $data = array();
            $data['baseUrl']    = $baseUrl;
            $data['basePath']   = $basePath;
            $data['mode'] = $mode;
            $data['fatalErrorMessage'] = $fatalErrorMessage;

            foreach (array('wikiName', 'demoMode', 'footerHtml') as $key) {
                if (isset($config[$key])) {
                    $data[$key] = $config[$key];
                }
            }

            $data['breadcrumbs'] = $this->createBreadcrumbs($requestPathArray);
            $data['showCreateUserButton'] = $showCreateUserButton;

            $data['requestPath'] = implode('/', $requestPathArray);
            $data['articleFilename'] = $articleFilename;

            if ($mode == 'view' || $mode == 'edit') {
                $articleMarkdown = file_get_contents($this->context->getArticleBaseDir() . $articleFilename);
                $data['articleMarkdown'] = $articleMarkdown;
                $data['articleHtml'] = $renderService->renderMarkdown($articleMarkdown, $mode == 'edit');
            }

            $this->renderPage($data);
        }
    }

    private function isUserDefined() {
        $config = $this->context->getConfig();
        foreach ($config as $key => $value) {
            if (strpos($key, 'user.') === 0) {
                return true;
            }
        }
        return false;
    }

    private function getArticleFilename($requestPathArray) {
        $articleBaseDir = $this->context->getArticleBaseDir();
        $articleFilename = implode('/', $requestPathArray);

        // Support `index.md` for directories
        if (is_dir($articleBaseDir . $articleFilename)) {
            $articleFilename = ltrim($articleFilename . '/index.md', '/');
        }

        // Make the extension `.md` optional
        if (! file_exists($articleBaseDir . $articleFilename) && file_exists($articleBaseDir . $articleFilename . '.md')) {
            $articleFilename .= '.md';
        }

        $articleFullFilename = $articleBaseDir . $articleFilename;
        if (! $this->context->isValidArticleFilename($articleFilename)) {
            // Attempt to break out of article base directory (e.g. `../../outside.ext`)
            return null;
        } else {
            return $articleFilename;
        }
    }

    private function createBreadcrumbs($requestPathArray) {
        $config = $this->context->getConfig();
        $wikiName = $config['wikiName'];
        $showCompleteBreadcrumbs = $config['showCompleteBreadcrumbs'];
        $pathCount = count($requestPathArray);
        $breadcrumbArray = array(array('name' => $wikiName, 'path' => '', 'active' => ($pathCount == 0)));

        $articleBaseDir = $this->context->getArticleBaseDir();
        $currentPath = '';
        for ($i = 0; $i < $pathCount; $i++) {
            $pathPart = $requestPathArray[$i];
            $currentPath .= ($i == 0 ? '' : '/') . $pathPart;
            $isLast = ($i == $pathCount - 1);

            $hasContent = ($isLast || file_exists($articleBaseDir . $currentPath . '/index.md'));
            if ($hasContent || $showCompleteBreadcrumbs) {
                // This is the requested file or an directory having an index -> Add it
                $breadcrumbArray[] = array(
                    'name' => str_replace('_', ' ', $pathPart),
                    'path' => $hasContent ? urlencode($currentPath) : null,
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
