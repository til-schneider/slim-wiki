<?php

class RenderService {

    public function renderMarkdown($markdownText) {
        require_once __DIR__ . '/../lib/parsedown/Parsedown.php';
        return Parsedown::instance()->text($markdownText);
    }

}
