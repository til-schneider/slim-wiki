Markdown cheat sheet
====================

Headlines
---------

Create headlines using hashes. Hashes on right are optional.

~~~ markdown
# Header 1 #
## Header 2 ##
### Header 3
#### Header 4
##### Header 5
###### Header 6
~~~

You can also underline your headline with equals (`===`) for level 1 or minus (`---`) for level 2.

~~~ markdown
Header 1
========

Header 2
--------
~~~


Paragraphs
----------

A paragraph is simply one or more lines of text,
separated by blank lines.

Text with  
two trailing spaces  
(on the right)  
can be used  
for things like poems.  

Inline markup like _italics_,  **bold**, ~~strike through~~ and `code()`:

~~~ markdown
_italics_,  **bold**, ~~strike through~~ and `code()`
~~~

Want to mark something in your page to fix later? Add a yellow TODO or FIXME marker:

~~~markdown
Simply write TODO or FIXME.
~~~



Links
-----

A simple Link: http://google.de

This is [a named link](http://google.de).
Use [a relative link](cheat_sheets) for pointing to another wiki page.


~~~ markdown
A simple Link: http://google.de

This is [a named link](http://google.de).
Use [a relative link](cheat_sheets) for pointing to another wiki page.
~~~


Images
------

Embed an image:

![Alt text](http://nuclearpixel.com/content/icons/2010-02-09_stellar_icons_from_space_from_2005/earth_128.png "Title is optional")

~~~ markdown
![Alt text](http://example.com/myimage.jpg "Title is optional")
~~~


Blockquotes and lists
---------------------

> Blockquotes are like quoted text in email replies
>> And they can be nested

~~~ markdown
> Blockquotes are like quoted text in email replies
>> And they can be nested
~~~

Bullet lists:

* Bullet lists are easy too
- Another one
+ Another one

~~~ markdown
* Bullet lists are easy too
- Another one
+ Another one
~~~

Numbered lists:

1. A numbered list
2. Which is numbered
3. With periods and a space

~~~ markdown
1. A numbered list
2. Which is numbered
3. With periods and a space
~~~


Code snippets
-------------

Create a simple code block by intending the text using 4 or more spaces:

~~~
    // Code is just text indented a bit
    which(isEasy) toRemember();
~~~

You can also create a code block using `~~~` or <code>```</code>. If you set a language (e.g. `~~~ xml`), your code will be shown using syntax highlighting:

~~~ xml
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <some-tag with="argument">And text</some-tag>
</root>
~~~

    ~~~ xml
    <?xml version="1.0" encoding="UTF-8"?>
    <root>
      <some-tag with="argument">And text</some-tag>
    </root>
    ~~~

Here is a [list of supported languages](http://prismjs.com/#languages-list).



Horizontal rules
----------------

~~~ markdown
* * * *
****
--------------------------
~~~



Embedded HTML
-------------

<div style="font-size: 20px; color:green; text-shadow: 4px 4px 2px rgba(0, 0, 0, 0.3);">
You can also <b>embed HTML</b>.
</div>

~~~ markdown
Just write it:
<div style="color: green">This is embedded HTML</div>
~~~


Tables
------

| Header | Center | Right  |
| ------ | :----: | -----: |
|  Cell  |  Cell  |   $10  |
|  Cell  |  Cell  |   $20  |

~~~ markdown
| Header | Header | Right  |
| ------ | ------ | -----: |
|  Cell  |  Cell  |   $10  |
|  Cell  |  Cell  |   $20  |
~~~

* Outer pipes on tables are optional
* Colon used for alignment (right versus left)
