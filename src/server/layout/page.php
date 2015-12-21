<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0">

  <title><?php echo $data['wikiName']; ?></title>

  <base href="<?php echo $data['baseUrl']; ?>">

  <!-- build:css client/view.css -->
  <!--
   | Styles see: https://highlightjs.org/static/demo/
   | Good bright styles: default, color-brewer, github, idea
   | Good dark styles:   agate, androidstudio, hybrid, railscasts, sunburst, tomorrow-night
   +-->
  <link rel="stylesheet" href="client/libs/highlightjs/styles/idea.css">

  <link href=".tmp/app-view.css" rel="stylesheet" />
  <!-- endbuild -->

</head>
<body>
  <div id="wrapper">
    <nav class="breadcrumbs"><div class="main-column"><?php
      $isFirst = true;
      foreach ($data['breadcrumbs'] as $item) {
        if (! $isFirst) {
          echo ' / ';
        }
        if ($item['active']) {
          echo $item['name'];
        } else {
          ?><a href="<?php echo $data['basePath'] . $item['path']; ?>"><?php echo $item['name']; ?></a><?php
        }
        $isFirst = false;
      }
    ?></div></nav>
    <article class="content main-column"><?php echo $data['articleHtml']; ?></article>
    <?php
    if (isset($data['footerHtml'])) {
      ?><footer><div class="main-column"><?php echo $data['footerHtml']; ?></div></footer><?php
    }
   ?>
  </div>
</body>

<!-- build:js client/view.js -->
<script src="client/libs/highlightjs/highlight.pack.js"></script>

<script src="client/js/Main.js"></script>
<!-- endbuild -->

</html>
