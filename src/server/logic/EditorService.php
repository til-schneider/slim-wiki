<?php

class EditorService {

    private $context;


    public function __construct($context) {
        $this->context = $context;
    }

    public function isRpcMethod($methodName) {
        return ($methodName == 'saveArticle');
    }

    public function saveArticle($articleFilename, $markdownText) {
        if (! $this->context->isValidArticleFilename($articleFilename)) {
            throw new Exception("Invalid article filename: '$articleFilename'");
        }

        // Set timezone
        $this->context->getConfig();

        // Write article file
        $articleFullFilename = $this->context->getArticleBaseDir() . $articleFilename;
        $articleDir = dirname($articleFullFilename);
        if (! file_exists($articleDir)) {
            mkdir($articleDir, 0777, true);
        }
        file_put_contents($articleFullFilename, $markdownText);

        // Write backup file (one per day)
        $backupFullFilename = $this->context->getDataBaseDir() . 'backup/' . $articleFilename . date('_Y-m-d') . '.gz';
        $backupDir = dirname($backupFullFilename);
        if (! file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }
        $fp = gzopen ($backupFullFilename, 'w9');
        gzwrite ($fp, $markdownText);
        gzclose($fp);

        return $this->context->getRenderService()->renderMarkdown($markdownText);
    }

}
