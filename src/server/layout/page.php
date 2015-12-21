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
  <link href="client/libs/highlightjs/styles/idea.css" rel="stylesheet">

  <link href=".tmp/app-view.css" rel="stylesheet" />
  <!-- endbuild -->

  <?php if ($data['isEditMode']) { ?>
  <!-- build:css client/edit.css -->
  <link href="client/libs/CodeMirror/lib/codemirror.css" rel="stylesheet">
  <link href="client/libs/CodeMirror/theme/railscasts.css" rel="stylesheet">
  <!-- endbuild -->
  <?php } // if $data['isEditMode'] ?>

</head>
<body<?php echo $data['isEditMode'] ? ' class="edit-mode"' : '' ?>>
  <?php
  if ($data['isEditMode']) {
    ?><div id="editor-wrapper">
      <textarea id="editor"><?php echo str_replace('<', '&lt;', $data['articleMarkdown']); ?></textarea>
    </div><?php
  }
  ?>
  <div id="main-wrapper">
    <nav class="breadcrumbs"><div class="main-column"><?php
      $isFirst = true;
      foreach ($data['breadcrumbs'] as $item) {
        if (! $isFirst) {
          echo ' / ';
        }
        if ($item['active']) {
          echo $item['name'];
        } else {
          ?><a href="<?php echo $data['basePath'] . ($data['isEditMode'] ? 'edit/' : '') . $item['path']; ?>"><?php echo $item['name']; ?></a><?php
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

<script src="client/js/app-view.js"></script>
<!-- endbuild -->

<?php if ($data['isEditMode']) { ?>
<!-- build:js client/edit.js -->
<script src="client/libs/CodeMirror/lib/codemirror.js"></script>
<script src="client/libs/CodeMirror/addon/mode/overlay.js"></script> <!-- Allow language-in-language -->
<script src="client/libs/CodeMirror/mode/markdown/markdown.js"></script>
<script src="client/libs/CodeMirror/mode/gfm/gfm.js"></script>

<!-- Nested languages -->
<script src="client/libs/CodeMirror/mode/clike/clike.js"></script>
<script src="client/libs/CodeMirror/mode/css/css.js"></script>
<script src="client/libs/CodeMirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="client/libs/CodeMirror/mode/javascript/javascript.js"></script>
<script src="client/libs/CodeMirror/mode/xml/xml.js"></script>

<script src="client/libs/CodeMirror/mode/meta.js"></script>

<script src="client/js/app-edit.js"></script>
<!-- endbuild -->
<?php } // if $data['isEditMode'] ?>

</html>
