slim-wiki
=========

slim wiki is a fast and slim wiki based on PHP and markdown.


Want to use slim wiki?
----------------------

Check out the **[demo website](http://slim-wiki.murfman.de/)** for more details and features.

[![slim wiki](http://slim-wiki.murfman.de/slim-wiki-screen.png)](http://slim-wiki.murfman.de/)



Want to develop slim wiki?
--------------------------


### Build instructions

Install grunt globally:

    sudo npm install -g grunt-cli

Install grunt dependencies in project:

    cd src
    npm install

Build client:

    cd src
    grunt

Build automatically on source changes (watch mode):

    cd src
    grunt watch



### Installation instructions

1. Build the project (see above).
2. Copy the contents of the `dist` directory to your webspace.
3. Create a `config.xml` (copy and adjust the example).
4. Give write permissions to the server for directories `articles` and `data`.



### Used libraries

- [Bootstrap](http://getbootstrap.com/) - Basic CSS styling.
- [Parsedown](https://github.com/erusev/parsedown/) - PHP markdown parser.
- [prism](http://prismjs.com/) - JavaScript syntax highlighter.
- [CodeMirror](https://codemirror.net/) - JavaScript in-browser code editor.
- [Vanilla JS](http://vanilla-js.com/) - No jQuery. Instead standard DOM API in order to make things fast and slim.



### Special thanks

- Slim wiki is inspired by [Wikitten](https://github.com/victorstanciu/Wikitten). I have used some of their ideas, but technically slim wiki is a complete rewrite.
- Thanks to Daring Fireball for inventing [Markdown](https://daringfireball.net/projects/markdown/syntax).
- Thanks to GitHub for hosting this project and for some great [enhancements to Markdown](https://help.github.com/articles/github-flavored-markdown).
