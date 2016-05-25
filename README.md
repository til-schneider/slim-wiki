slim-wiki
=========

slim wiki is a fast and slim wiki based on PHP and markdown.


Want to use slim wiki?
----------------------

Check out the **[demo website](http://slim-wiki.murfman.de/)** for more details and features.

[![slim wiki](http://slim-wiki.murfman.de/slim-wiki-screen.png)](http://slim-wiki.murfman.de/)



Want to develop slim wiki?
--------------------------

### Set up build environment

1. Install [node.js](https://nodejs.org/en/) (this includes `npm`)

2. Install grunt globally:

        sudo npm install -g grunt-cli

3. Install grunt dependencies in project:

        cd src
        npm install

**Note:** node.js is only used by grunt for the build. Slim wiki uses PHP to run on server-side.



### Build instructions

Build client:

    cd src
    grunt

Now link the project directory to a locally installed Apache Webserver with PHP and `mod_rewrite` enabled.

Go to the browser and open one of:

- `http://localhost/path/to/src/` - for the development version using the source JavaScript files.
- `http://localhost/path/to/dist/` - for the production version using compressed CSS and JavaScript.



### Other build options

Build automatically on source changes (watch mode):

    cd src
    grunt watch

Build a release zip:

    cd src
    grunt release



### Installation instructions

1. Build the project (see above).
2. Copy the contents of the `dist` directory to your webspace.
3. Create a `config.php` (copy and adjust the example).
4. Give write permissions to the server for the directory `data` (including subdirectories and files).



### Used libraries

- [Bootstrap](http://getbootstrap.com/) - Basic CSS styling.
- [Parsedown](https://github.com/erusev/parsedown/) - PHP markdown parser.
- [prism](http://prismjs.com/) - JavaScript syntax highlighter.
- [CodeMirror](https://codemirror.net/) - JavaScript in-browser code editor.
- [Tocbot](http://tscanlin.github.io/tocbot/) - JavaScript table of contents generator.
- [Vanilla JS](http://vanilla-js.com/) - No jQuery. Instead standard DOM API in order to make things fast and slim.



### Special thanks

- Slim wiki is inspired by [Wikitten](https://github.com/victorstanciu/Wikitten). I have used some of their ideas, but technically slim wiki is a complete rewrite.
- Thanks to Daring Fireball for inventing [Markdown](https://daringfireball.net/projects/markdown/syntax).
- Thanks to GitHub for hosting this project and for some great [enhancements to Markdown](https://help.github.com/articles/github-flavored-markdown).
