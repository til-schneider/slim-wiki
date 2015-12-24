(function(document, slimwiki, hljs) {

  slimwiki.View = {
    updateSyntaxHighlighting: updateSyntaxHighlighting
  };

  if (slimwiki.supportedBrowser) {
    init();
  }


  function init() {
    var mode = slimwiki.settings.mode;

    if (mode == 'view' || mode == 'edit') {
      updateSyntaxHighlighting();
    }
  }

  function updateSyntaxHighlighting(parentElem) {
    if (! parentElem) {
      parentElem = document.getElementById('content');
    }

    var blocks = parentElem.querySelectorAll('pre code');
    Array.prototype.forEach.call(blocks, hljs.highlightBlock);
  }

})(document, slimwiki, hljs);
