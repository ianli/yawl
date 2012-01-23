YAWL Ain't Web Logs
===================

YAWL is a very simple blogging platform in PHP.

YAWL is similar to [Jekyll](https://github.com/mojombo/jekyll) by Tom Preston-Werner in the following ways:

* People write posts and in plain text files.
* Text files have front matter that are parsed for key-value pairs that can be used in templates.
* Posts and pages are converted into static files.


Quick Start
-----------

To start using YAWL immediately, get a copy of YAWL at http://github.com/ianli/yawl and put the directory anywhere under the document root of your web site. If your document root is at `document_root/` and you put `yawl/` right underneath, your YAWL blog is available at `http://example.com/yawl/`. You can rename the directory if you want.

In the YAWL directory, write your posts in the `_posts/` directory and your pages in the `_pages/` directory.


Pages and Posts
---------------

Content for a YAWL blog are called _pages_ and _posts_. Pages are just regular web pages, while posts are pages with a date. Pages and posts can be written in HTML, Textile, and PHP.

Pages are placed in the `_pages/` directory, while posts are placed in the `_posts/` directory.

### Front Matter

Pages and posts contain a _front matter_. The front matter for HTML and Textile is at the top of the file and has the following format:

```text
---
title:  Title of Post
layout: default
---
```

The front matter for PHP is different. Instead, you must assign an associative array to the variable `$front_matter`. For example:

```php
<?php
  $front_matter = array(
    'title'   => 'Title of Post',
    'layout'  => 'default'
  );
?>
```

Layouts
-------

Layouts in YAWL are written in PHP and use RAFT to access variables to display. The following are the variables available to the layout:

__Page Variables__

```text
Variable        Description
--------        -----------
page.layout     The layout used by the page
page.title      The title of the page
page.content    The content of the page
page.date       The date associated with the page
page.extension  The extension of the file from which this page was processed
page.year       The year of the page
page.month      The month of the page
page.day        The day of the page
page.content    The content of the page
```

__Site Variables__

```text
Variable        Description
--------        -----------
site.pages      All the *published* pages processed by YAWL
site.posts      All the *published* posts processed by YAWL. 
                In reverse chronological order
```

File Structure
--------------

```text
yawl/

    # Content creation
    # Add posts and pages in these folders.
    
    _posts/
      # Posts are pages with an associated date. Processed by YAWL.
      
    _pages/
      # Pages are not ordered chronologically. Processed by YAWL.
    
    # Content styling
    # Add layouts and styles in these folders.
    
    _layouts/
      # Directory of RAFT layouts in PHP. A default is provided.
      
    css/
      # Directory of stylesheets. A default is provided.
      
    # Content publishing
    # Posts and pages processed by YAWL are placed here.
    
    _site/
      # Generated static files are stored here.
      
    .htaccess
      # Apache settings to direct how URLs are handled.
    
    # YAWL platform
    # Do not modify these unless you know what you are doing.
    
    yawl.php
      # Main PHP file that provides YAWL's functionality.
    
    php/
      # Contains PHP libraries that YAWL uses.
```


PHP Dependencies
----------------

YAWL is dependent on the following PHP libraries:

* [RAFT](http://github.com/ianli/raft/) - Used for creating templates.
* [classTextile.php](http://code.google.com/p/textpattern/source/browse/development/4.x/textpattern/lib/classTextile.php?r=3359) by Dean Allen - Used to process posts and pages ending in `.textile`.
* [PHP Markdown](http://michelf.com/projects/php-markdown/) by Michel Fortin - Used to process posts and pages ending in `.markdown` or `.md`.


Versioning
----------

For transparency and insight into our release cycle, and for striving to maintain backwards compatibility, this code will be maintained under the Semantic Versioning guidelines as much as possible.

Releases will be numbered with the follow format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backwards compatibility bumps the major
* New additions without breaking backwards compatibility bumps the minor
* Bug fixes and misc changes bump the patch

For more information on SemVer, please visit http://semver.org/.


Versions
--------

*0.3.0pre* - Pending (started January 23, 2012)

- Added support for categories. A post can belong to a category.
- Changed .htaccess to pass all requests ending in .html to YAWL.

*0.2.0* - January 22, 2012

- Complete rewrite.
- Removed dependency from JIFF.
- More similar to Jekyll than previous versions
    - Uses front matter.
    - Properties are extracted from filenames.
- Added support for PHP pages/posts.
- Differentiated concepts between pages and posts.
- Added `published` to the front matter to allow drafts.
- Added reverse-chronological ordering of posts.
- Added support for PHP pages.
- Added support for Textile pages.
- Added support for Markdown pages.

*0.1.0*	- November 16, 2010

- Class-based implementation.
- Removed dependency on configuration file.

*0.0.1* - November 8, 2010

- Initial version of YAWL. Not working, but tagged for historical purposes.


License
-------

Copyright 2012 Ian Li, http://ianli.com

Licensed under [the MIT license](http://www.opensource.org/licenses/mit-license.php).
