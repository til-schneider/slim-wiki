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
        $config = $this->context->getConfig();

        require_once __DIR__ . '/../lib/parsedown/Parsedown.php';
        $html = Parsedown::instance()->text($markdownText);

        // Support `TODO` and `FIXME`
        $html = preg_replace('/(^|\\W)(TODO|FIXME):?(\\W|$)/', '$1<span class="todo">$2</span>$3', $html);

        // Enhance links
        $openExternalLinksInNewTab = $config['openExternalLinksInNewTab'];
        $html = preg_replace_callback('|(<a href="([^"]+))"|',
            function($match) use($isEditMode, $openExternalLinksInNewTab) {
                $url = $match[2];
                $isLocalLink = ! strpos($url, '//');
                if ($isLocalLink && $isEditMode) {
                    // Append `?edit` to local links (in order to stay in edit mode)
                    return $match[1] . '?edit"';
                } else if (!$isLocalLink && $openExternalLinksInNewTab) {
                    // Add `target="_blank"` to external links
                    return $match[0] . ' target="_blank"';
                } else {
                    return $match[0];
                }
            }, $html);
        return $html;
    }

}
