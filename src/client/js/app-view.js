(function(document, slimwiki, Prism, tocbot) {

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
      initToc();
      updateSyntaxHighlighting();
    }
  }

  function initToc() {
    if (! slimwiki.settings.showToc) {
      return; // Nothing to do
    }

    var headingSelector = 'h1, h2, h3',
        nextId = 1,
        headingsOffset = 80,
        headings;

    // tocbot needs ID attributes at the headings in order to function
    headings = document.getElementById('content').querySelectorAll(headingSelector);
    Array.prototype.forEach.call(headings, function (headingElem) {
      headingElem.id = 'heading-' + (nextId++);
    });

    tocbot.init({
      // Where to render the table of contents.
      tocSelector: '.toc-wrapper',
      // Where to grab the headings to build the table of contents.
      contentSelector: '#content',
      // Which headings to grab inside of the contentSelector element.
      headingSelector: headingSelector,
      // Headings offset between the headings and the top of the document.
      headingsOffset: headingsOffset,
      // smooth-scroll options object, see docs at:
      // https://github.com/cferdinandi/smooth-scroll
      smoothScrollOptions: {
        offset: headingsOffset
      }
    });
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

})(document, slimwiki, Prism, tocbot);
