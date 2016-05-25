<?php

class EditorService {

    private $context;


    public function __construct($context) {
        $this->context = $context;
    }

    public function isRpcMethod($methodName) {
        return ($methodName == 'createArticle' || $methodName == 'previewArticle' || $methodName == 'saveArticle'
            || $methodName == 'createUserConfig');
    }

    // Returns one of: 'logged-in', 'no-credentials', 'wrong-credentials'
    public function getLoginState() {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return 'no-credentials';
        } else {
            $userInfo = $this->context->getConfig()['user.' . $_SERVER['PHP_AUTH_USER']];
            if (isset($userInfo)) {
                $loginHash = hash($userInfo['type'], $_SERVER['PHP_AUTH_PW'] . $userInfo['salt']);
                if ($loginHash == $userInfo['hash']) {
                    return 'logged-in';
                }
            }

            return 'wrong-credentials';
        }
    }

    public function assertLoggedIn() {
        if ($this->getLoginState() != 'logged-in') {
            throw new Exception('Not logged in');
        }
    }

    public function checkForError($articleFilename) {
        function canWriteFileOrParent($filename) {
            if ($filename == '') {
                return false;
            } else if (file_exists($filename)) {
                return is_writable($filename);
            } else {
                return canWriteFileOrParent(dirname($filename));
            }
        }

        $articleFullFilename = $this->context->getArticleBaseDir() . $articleFilename;
        if (! canWriteFileOrParent($articleFullFilename)) {
            return $this->context->getI18n()['error.missingWritePermissions.article']
                . ' <code>' . $articleFullFilename . '</code>';
        }

        $backupFullFilename = $this->context->getDataBaseDir() . $this->getBackupFilename($articleFilename);
        if (! canWriteFileOrParent($backupFullFilename)) {
            return $this->context->getI18n()['error.missingWritePermissions.backup']
                . ' <code>' . $backupFullFilename . '</code>';
        }

        return null;
    }

    public function createArticle($articleFilename, $pageTitle) {
        if (! $this->str_endswith($articleFilename, '.md')) {
            $articleFilename .= '.md';
        }

        $markdownText = $this->getNewArticleMarkdown($pageTitle);

        $config = $this->context->getConfig();
        if ($config['demoMode']) {
            return $this->previewArticle($articleFilename, $markdownText);
        } else {
            return $this->saveArticle($articleFilename, $markdownText);
        }
    }

    public function getNewArticleMarkdown($pageTitle) {
        return $pageTitle . "\n" . str_repeat('=', mb_strlen($pageTitle, $encoding = 'utf-8')) . "\n\n"
            . $this->context->getI18n()['createArticle.content'];
    }

    // Used in demo-mode instead of `saveArticle`
    public function previewArticle($articleFilename, $markdownText) {
        return $this->context->getRenderService()->renderMarkdown($markdownText, true);
    }

    public function saveArticle($articleFilename, $markdownText) {
        $this->assertLoggedIn();

        if (! $this->context->isValidArticleFilename($articleFilename)) {
            throw new Exception("Invalid article filename: '$articleFilename'");
        }

        // Write article file
        $articleFullFilename = $this->context->getArticleBaseDir() . $articleFilename;
        $articleDir = dirname($articleFullFilename);
        if (! file_exists($articleDir)) {
            mkdir($articleDir, 0777, true);
        }
        if (! is_writable(file_exists($articleFullFilename) ? $articleFullFilename : $articleDir)) {
            throw new Exception("No write permissions for article file");
        }
        file_put_contents($articleFullFilename, $markdownText);

        // Write backup file (one per day)
        $backupFullFilename = $this->context->getDataBaseDir() . $this->getBackupFilename($articleFilename);
        $backupDir = dirname($backupFullFilename);
        if (! file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }
        if (! is_writable(file_exists($backupFullFilename) ? $backupFullFilename : $backupDir)) {
            throw new Exception("No write permissions for backup file");
        }
        $fp = gzopen ($backupFullFilename, 'w9');
        gzwrite ($fp, $markdownText);
        gzclose($fp);

        return $this->context->getRenderService()->renderMarkdown($markdownText, true);
    }

    private function getBackupFilename($articleFilename) {
        // Set timezone
        $this->context->getConfig();

        return 'backup/' . $articleFilename . date('_Y-m-d') . '.gz';
    }

    public function createUserConfig($user, $pass) {
        $type = 'sha256';
        $salt = uniqid(mt_rand());
        $hash = hash($type, $pass . $salt);

        return "\$config['user.".strtolower($user)."'] = array('type' => '$type', 'salt' => '$salt', 'hash' => '$hash');";
    }

    function str_endswith($string, $test) {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
        return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
    }

}
