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



Installation instructions
-------------------------

1. Build the project (see above)
2. Copy the contents of the `dist` directory to your webspace.
3. Create a `config.xml` (copy and adjust the example).
4. Give write permissions to the server for directories `articles` and `data`.


Used libraries
--------------

- [Bootstrap](http://getbootstrap.com/) - Basic CSS styling
- [Parsedown](https://github.com/erusev/parsedown/) - PHP markdown parser
- [highlight.js](https://highlightjs.org/) - JavaScript syntax highlighter
- [CodeMirror](https://codemirror.net/) - JavaScript in-browser code editor

Slim wiki is inspired by [Wikitten](https://github.com/victorstanciu/Wikitten).
