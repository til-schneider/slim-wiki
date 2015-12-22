<?php

class Context {

    private $config;

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
            'timezone' => 'Europe/Berlin'
        );

        if (file_exists(__DIR__ . '/../../config.php')) {
            include(__DIR__ . '/../../config.php');
        }

        $this->config = $config;
        return $config;
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
            $this->renderService = new RenderService();
        }
        return $this->renderService;
    }

}
