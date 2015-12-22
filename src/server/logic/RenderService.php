<?php

class RenderService {

    private static $singleton;


    public static function get() {
        if (is_null(self::$singleton)) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    public function isRpcMethod($methodName) {
        return ($methodName == 'renderMarkdown');
    }

    public function renderMarkdown($markdownText) {
        require_once __DIR__ . '/../lib/parsedown/Parsedown.php';
        return Parsedown::instance()->text($markdownText);
    }

}
