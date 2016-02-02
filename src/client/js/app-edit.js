(function(window, document, slimwiki, console, CodeMirror) {

  var editor,
      updatePreviewDelay = 1000,
      updatePreviewTimeout = null,
      updatePreviewRunning = false,
      demoAlertState = null,
      previewIsDirty = false;

  if (slimwiki.supportedBrowser) {
    init();
  }


  function init() {
    var mode = slimwiki.settings.mode;

    if (mode == 'edit') {
      initEditMode();
    } else if (mode == 'createArticle') {
      initCreateArticle();
    } else if (mode == 'createUser') {
      initCreateUserForm();
    }
  }

  function initEditMode() {
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

  function initCreateArticle() {
    document.getElementById('createArticleBtn').addEventListener('click', function() {
      var articleFilename = slimwiki.settings.articleFilename,
          pageTitle = slimwiki.settings.pageTitle;
      callRpc('editor', 'createArticle', [ articleFilename, pageTitle ], function(result, error) {
        if (error) {
          console.error('Creating article failed:', error);
          showErrorLogged();
        } else {
          location.href = slimwiki.settings.requestPath + '?edit';
        }
      })
    });
  }

  function initCreateUserForm() {
    document.getElementById('create-user-box').style.display = 'block';

    document.getElementById('showConfigBtn').addEventListener('click', function() {
      var user = document.getElementById('user').value,
          pass = document.getElementById('password').value;

      callRpc('editor', 'createUserConfig', [ user, pass ], function(result, error) {
        if (error) {
          console.error('Creating user config failed:', error);
          showErrorLogged();
        } else {
          var resultBoxElem = document.getElementById('result-box');
          resultBoxElem.style.display = 'block';

          document.getElementById('result').innerHTML = result.replace(/</g, '&lt;');
          slimwiki.View.updateSyntaxHighlighting(resultBoxElem);
        }
      });
    }, false);
  }

  function onEditorChange() {
    var isDemoMode = slimwiki.settings.demoMode;

    if (isDemoMode && ! demoAlertState) {
      var demoAlertElem = document.getElementById('demo-alert');
      demoAlertElem.style.display = 'block';
      demoAlertState = 'showing';

      document.getElementById('demoAlertOkBtn').addEventListener('click', function() {
        demoAlertElem.style.display = 'none';
        demoAlertState = 'dismissed';
      });
    }

    previewIsDirty = true;
    if (! updatePreviewRunning) {
      window.clearTimeout(updatePreviewTimeout);
      updatePreviewTimeout = window.setTimeout(function() {
        previewIsDirty = false;
        updatePreviewRunning = true;
        var start = new Date().getTime(),
            articleFilename = slimwiki.settings.articleFilename,
            methodName = isDemoMode ? 'previewArticle' : 'saveArticle';

        callRpc('editor', methodName, [ articleFilename, editor.getValue() ], function(result, error) {
          updatePreviewRunning = false;

          if (error) {
            console.error('Saving article failed:', error);
            showErrorLogged();
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

  function callRpc(objectName, methodName, paramArray, done) {
    var request = new XMLHttpRequest(),
        requestJson;

    request.open('POST', 'rpc/' + objectName, true);
    request.onreadystatechange = function () {
      if (request.readyState == 4) {
        if (request.status != 200) {
          done(null, 'Request failed with status ' + request.status);
        } else {
          var responseJson;
          try {
            responseJson = JSON.parse(request.responseText);
          } catch (err) {
            done(null, 'Parsing response failed: ' + err);
          }

          if (responseJson.error) {
            done(null, 'Request failed on server-side: ' + responseJson.error.message);
          } else {
            done(responseJson.result);
          }
        }
      }
    };

    requestJson = { jsonrpc: '2.0', method: methodName, params: paramArray || [], id: 1 };
    request.send(JSON.stringify(requestJson));
  }

  function showErrorLogged() {
    var errorElem = document.getElementById('error-alert');
    errorElem.style.display = 'block';

    window.setTimeout(function() {
      errorElem.style.display = 'none';
    }, 5000);
  }

})(window, document, slimwiki, console, CodeMirror);
