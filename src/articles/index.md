Welcome to your wiki!
=====================

Slim wiki is a fast and slim wiki based on PHP and Markdown.

- Write your pages in [GitHub-flavored Markdown](cheat_sheets/Markdown_cheat_sheet) - no fancy wiki syntax no one can remember, instead pretty text written in a widely used standard.
- Syntax highlighting support for [a ton of languages](http://prismjs.com/#languages-list).
- No database required - everything is file-based.
- Beautiful styling.
- Edit your pages with a single click in a great editor - no tiny little text area.
- Instant preview of your changes.



Installation
------------

**Requirements:**

- PHP 5.3+
- Apache Webserver with `mod_rewrite` enabled.

**Installation:**

1. [Download](http://slim-wiki.murfman.de/slim-wiki.zip) the latest version.
2. Extract the archive and put the contents on your webspace.
3. Create a `config.xml` (copy and adjust the example).
4. Give write permissions to the server for directories `articles` and `data`.



How to use the wiki
-------------------

- Just click on the `Edit` button at the top right corner and start writing.
- If you are not familiar with Markdown, check out the [Markdown cheat sheet](cheat_sheets/Markdown_cheat_sheet).
- Your changes are automatically saved as soon as you stop writing for a second. If you see your changes on the right side of the edit view, they will be already saved (with a daily backup) and public. There is no save button you have to press.
- You can add new pages by adding a link to [a non-existing page](this_is_a_new_page). Then click the link and slim wiki will allow you to add the new page.

**Tipps:**

- Use the main page as overview and add links to subpages.
- You can add a [link to a directory](cheat_sheets) having a `index.md`.

**Notes:**

- The extension `.md` is optional.
- Underscores in directory or file names are shown as spaces in the breadcrumbs. So if you name your file `My_new_page.md`, it will be shown as `My new page`.



Special thanks
--------------

- Slim wiki is inspired by [Wikitten](https://github.com/victorstanciu/Wikitten). I have used some of their ideas, but technically slim wiki is a complete rewrite.
- Thanks to Daring Fireball for inventing [Markdown](https://daringfireball.net/projects/markdown/syntax).
- Thanks to GitHub for hosting this project and for some great [enhancements to Markdown](https://help.github.com/articles/github-flavored-markdown).
