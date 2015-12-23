(function(window, document, slimwiki, hljs) {

  slimwiki.Util = {
    callRpc: callRpc
  };
  slimwiki.View = {
    updateSyntaxHighlighting: updateSyntaxHighlighting
  };

  init();


  function init() {
    var mode = slimwiki.settings.mode;

    if (mode == 'view' || mode == 'edit') {
      updateSyntaxHighlighting();
    } else if (mode == 'createUser') {
      initCreateUserForm();
    }
  }

  function initCreateUserForm() {
    document.getElementById('showConfigBtn').addEventListener('click', function() {
      var user = document.getElementById('user').value,
          pass = document.getElementById('password').value;

      callRpc('editor', 'createUserConfig', [ user, pass ], function(result, error) {
        if (error) {
          console.error('Creating user config failed:', error);
        } else {
          var resultBoxElem = document.getElementById('result-box');
          resultBoxElem.style.display = 'block';

          document.getElementById('result').innerHTML = result.replace(/</g, '&lt;');
          updateSyntaxHighlighting(resultBoxElem);
        }
      });
    }, false);
  }

  function updateSyntaxHighlighting(parentElem) {
    if (! parentElem) {
      parentElem = document.getElementById('content');
    }

    var blocks = parentElem.querySelectorAll('pre code');
    Array.prototype.forEach.call(blocks, hljs.highlightBlock);
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

})(window, document, slimwiki, hljs);
