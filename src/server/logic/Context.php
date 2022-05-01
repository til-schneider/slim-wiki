<?php

class Context {

    private $config;
    private $i18n;

    private $articleBaseDir;
    private $dataBaseDir;

    private $editorService;
    private $renderService;


    public function __construct() {
        $appBaseDir = realpath(__DIR__ . '/../../');

        $this->dataBaseDir = $appBaseDir . '/data/';
        $this->articleBaseDir = $this->dataBaseDir . 'articles/';
    }

    public function getConfig() {
        if (! is_null($this->config)) {
            return $this->config;
        }

        // Defaults
        $config = array(
            'wikiName' => 'Slim Wiki',
            'timezone' => 'Europe/Berlin',
            'lang'     => 'en',
            'theme'    => 'slim',
            'demoMode' => false,
            'openExternalLinksInNewTab' => true,
            'showCompleteBreadcrumbs' => true,
            'showToc'  => true,
            'private'  => false,
        );

        if (file_exists(__DIR__ . '/../../config.php')) {
            include(__DIR__ . '/../../config.php');
        }

        $this->config = $config;
        return $config;
    }

    public function getI18n() {
        if (! is_null($this->i18n)) {
            return $this->i18n;
        }

        $lang = $this->getConfig()['lang'];
        include(__DIR__ . "/../i18n/$lang.php");

        $this->i18n = $i18n;
        return $i18n;
    }

    public function getArticleBaseDir() {
        return $this->articleBaseDir;
    }

    public function getDataBaseDir() {
        return $this->dataBaseDir;
    }

    public function isValidArticleFilename($articleFilename) {
        // Don't allow to escape from the article base directory
        return ! is_string($articleFilename) || ! strpos($articleFilename, '..');
    }

    // Returns tuple of username/password or [null,null].
    private function getUserCredentials() {
        if (isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]) && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            list ($auth_type, $cred) = explode (" ", $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
            if ($auth_type == 'Basic') {
                return explode (":", base64_decode($cred));
            }
        } else if (isset($_SERVER['PHP_AUTH_USER'])) {
            return array( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );
        }
        return array(null, null);
    }

    // Returns one of: 'logged-in', 'no-credentials', 'wrong-credentials'
    public function getLoginState() {
        list ($auth_user,  $auth_pw) = $this->getUserCredentials();

        if (!($auth_user && $auth_pw)) {
            return 'no-credentials';
        }

        $userInfo = $this->context->getConfig()['user.' . $auth_user];
        if (isset($userInfo)) {
            $loginHash = hash($userInfo['type'], $auth_pw . $userInfo['salt']);
            if ($loginHash == $userInfo['hash']) {
                return 'logged-in';
            }
        }

        return 'wrong-credentials';
    }

    public function assertLoggedIn() {
        if ($this->getLoginState() != 'logged-in') {
            throw new Exception('Not logged in');
        }
    }

    public function getEditorService() {
        if (is_null($this->editorService)) {
            require_once __DIR__ . '/EditorService.php';
            $this->editorService = new EditorService($this);
        }
        return $this->editorService;
    }

    public function getRenderService() {
        if (is_null($this->renderService)) {
            require_once __DIR__ . '/RenderService.php';
            $this->renderService = new RenderService($this);
        }
        return $this->renderService;
    }

}
