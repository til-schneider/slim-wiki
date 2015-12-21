(function(CodeMirror) {
  var editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
    // Config see: https://codemirror.net/doc/manual.html
    mode: 'gfm',
    lineNumbers: false,
    lineWrapping: true,
    theme: 'railscasts'
  });

})(CodeMirror);
