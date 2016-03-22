<?php

if (function_exists('date_default_timezone_set')) {
  date_default_timezone_set('Europe/Berlin');
}

$config['wikiName'] = 'Slim Wiki';
$config['lang'] = 'en'; // 'de' or 'en'

//$config['theme'] = 'slim';

// Hide directories having no 'index.md' in breadcrumbs
//$config['showCompleteBreadcrumbs'] = false;

//$config['footerHtml'] = '&copy; Copyright 2000-'.date('Y').' My name';
