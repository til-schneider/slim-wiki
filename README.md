slim-wiki
=========

A slim wiki based on PHP and markdown



Build instructions
------------------

Install grunt globally:

    sudo npm install -g grunt-cli

Install grunt dependency in project:

    cd src
    npm install

Build client:

    cd src
    grunt

Build automatically on source changes (watch mode):

    cd src
    grunt watch



Used libraries
--------------

- [Parsedown](https://github.com/erusev/parsedown/) - PHP markdown parser
- [highlight.js](https://highlightjs.org/) - JavaScript syntax highlighter
- [CodeMirror](https://codemirror.net/) - JavaScript in-browser code editor

Slim wiki is inspired by [Wikitten](https://github.com/victorstanciu/Wikitten).
