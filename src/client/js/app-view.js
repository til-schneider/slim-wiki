(function(document, slimwiki, Prism) {

  slimwiki.View = {
    updateSyntaxHighlighting: updateSyntaxHighlighting
  };

  if (slimwiki.supportedBrowser) {
    init();
  }


  function init() {
    var mode = slimwiki.settings.mode;

    Prism.plugins.autoloader.languages_path = 'client/libs/prism/components/';

    if (mode == 'view' || mode == 'edit') {
      updateSyntaxHighlighting();
    }
  }

  function updateSyntaxHighlighting(parentElem) {
    if (! parentElem) {
      parentElem = document.getElementById('content');
    }

    var blocks = parentElem.querySelectorAll('pre code');
    Array.prototype.forEach.call(blocks, function (blockElem) {
      Prism.highlightElement(blockElem, false, function() {});
    });
  }

})(document, slimwiki, Prism);
