(function(window, document, console, CodeMirror) {

  var slimwiki = window.slimwiki,
      editor,
      updatePreviewDelay = 1000,
      updatePreviewTimeout = null,
      updatePreviewRunning = false,
      previewIsDirty = false;


  function init() {
    editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
      // Config see: https://codemirror.net/doc/manual.html
      mode: 'gfm',
      lineNumbers: false,
      lineWrapping: true,
      theme: 'railscasts'
    });

    editor.on('changes', onEditorChange);
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
        callRpc('editor', 'saveArticle', [ articleFilename, editor.getValue() ], function(result, error) {
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

  function callRpc(objectName, methodName, paramArray, done) {
    var request = new XMLHttpRequest(),
        requestJson;

    request.open('POST', 'rpc/' + objectName, true);
    request.onreadystatechange = function () {
      if (request.readyState == 4) {
        if (request.status != 200) {
          done(null, 'Request failed with status ' + request.status);
        } else {
          try {
            var responseJson = JSON.parse(request.responseText);
            if (responseJson.error) {
              done(null, 'Request failed on server-side: ' + responseJson.error.message);
            } else {
              done(responseJson.result);
            }
          } catch (err) {
            done(null, 'Request failed: ' + err);
          }
        }
      }
    };

    requestJson = { jsonrpc: '2.0', method: methodName, params: paramArray || [], id: 1 };
    request.send(JSON.stringify(requestJson));
  }

  init();

})(window, document, console, CodeMirror);
