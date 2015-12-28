<?php

class RenderService {

    private $context;


    public function __construct($context) {
        $this->context = $context;
    }

    public function articleExists($articleFilename) {
        return file_exists($this->context->getArticleBaseDir() . $articleFilename);
    }

    public function renderMarkdown($markdownText, $isEditMode) {
        require_once __DIR__ . '/../lib/parsedown/Parsedown.php';
        $html = Parsedown::instance()->text($markdownText);

        // Support `FIXME`
        $html = preg_replace('/(^|\\W)FIXME(\\W|$)/', '$1<span class="fixme">FIXME</span>$2', $html);

        if ($isEditMode) {
            // Append `?edit` to local links (in order to stay in edit mode)
            $html = preg_replace_callback('|(<a href="([^"]+))"|',
                function($match) {
                    $url = $match[2];
                    $isLocalLink = ! strpos($url, '//');
                    if ($isLocalLink) {
                        return $match[1] . '?edit"';
                    } else {
                        return $match[0];
                    }
                }, $html);
        }
        return $html;
    }

}
