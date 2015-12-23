<?php

class EditorService {

    private $context;


    public function __construct($context) {
        $this->context = $context;
    }

    public function isRpcMethod($methodName) {
        return ($methodName == 'saveArticle' || $methodName == 'createUserConfig');
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

    public function saveArticle($articleFilename, $markdownText) {
        $this->assertLoggedIn();

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

        return $this->context->getRenderService()->renderMarkdown($markdownText, true);
    }

    public function createUserConfig($user, $pass) {
        $type = 'sha256';
        $salt = uniqid(mt_rand(), true);
        $hash = hash($type, $pass . $salt);

        return "\$config['user.".strtolower($user)."'] = array('type' => '$type', 'salt' => '$salt', 'hash' => '$hash');";
    }

}
