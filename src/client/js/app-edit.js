(function(window, document, slimwiki, console, CodeMirror) {

  var editor,
      updatePreviewDelay = 1000,
      updatePreviewTimeout = null,
      updatePreviewRunning = false,
      previewIsDirty = false;

  if (slimwiki.supportedBrowser) {
    init();
  }


  function init() {
    document.getElementById('close-edit-mode').style.display = 'block';

    editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
      // Config see: https://codemirror.net/doc/manual.html
      mode: 'gfm',
      lineNumbers: false,
      lineWrapping: true,
      theme: 'railscasts'
    });

    editor.on('changes', onEditorChange);
    editor.on('scroll', onEditorScroll)
  }

  function onEditorChange() {
    previewIsDirty = true;
    if (! updatePreviewRunning) {
      window.clearTimeout(updatePreviewTimeout);
      updatePreviewTimeout = window.setTimeout(function() {
        previewIsDirty = false;

        updatePreviewRunning = true;
        var start = new Date().getTime(),
            articleFilename = slimwiki.settings.articleFilename;
        slimwiki.Util.callRpc('editor', 'saveArticle', [ articleFilename, editor.getValue() ], function(result, error) {
          updatePreviewRunning = false;

          if (error) {
            console.error('Saving article failed:', error);
          } else {
            document.getElementById('content').innerHTML = result;
            slimwiki.View.updateSyntaxHighlighting();
            console.log('Saved article in ' + (new Date().getTime() - start) + ' ms');
          }

          if (previewIsDirty) {
            onEditorChange();
          }
        })
      }, updatePreviewDelay);
    }
  }

  function onEditorScroll() {
    // Synchronize scroll position of preview when editor is scrolled
    var scrollInfo = editor.getScrollInfo(),
        scrollFactor = scrollInfo.top / (scrollInfo.height - scrollInfo.clientHeight),
        bodyElem = document.body;

    window.scrollTo(0, scrollFactor * (bodyElem.scrollHeight - bodyElem.clientHeight));
  }

})(window, document, slimwiki, console, CodeMirror);
