(function(window, document, hljs) {

  window.slimwiki = window.slimwiki || {};

  window.slimwiki.View = {
    updateSyntaxHighlighting: updateSyntaxHighlighting
  };

  updateSyntaxHighlighting();


  function updateSyntaxHighlighting() {
    var blocks = document.getElementById('content').querySelectorAll('pre code');
    Array.prototype.forEach.call(blocks, hljs.highlightBlock);
  }

})(window, document, hljs);
