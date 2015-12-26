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

        $this->articleBaseDir = $appBaseDir . '/articles/';
        $this->dataBaseDir = $appBaseDir . '/data/';
    }

    public function getConfig() {
        if (! is_null($this->config)) {
            return $this->config;
        }

        // Defaults
        $config = array(
            'wikiName' => 'Slim Wiki',
            'timezone' => 'Europe/Berlin',
            'lang'     => 'en'
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
