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


Posts and Page Format
---------------------


Templates
---------


File Structure
--------------

```text
yawl/

  (ADD YOUR POSTS/PAGES HERE)
  _posts/
    # Posts are pages with an associated date. Processed by YAML.
  _pages/
    # Pages are not ordered chronologically. Processed by YAWL.
    
  (OPTIONAL)
  _layouts/
    # Directory of RAFT layouts in PHP. A default is provided.
  css/
    # Directory of stylesheets. A default is provided.

  (DO NOT MODIFY)
  yawl.php
    # Main PHP file that provides YAWL's functionality.
  _site/
    # Generated static files are stored here.
  php/
    # Contains PHP libraries that YAWL uses.
```


PHP Dependencies
----------------

YAWL is dependent on the following PHP libraries:

* [classTextile.php](http://code.google.com/p/textpattern/source/browse/development/4.x/textpattern/lib/classTextile.php?r=3359) by Dean Allen - Used to process posts and pages ending in `.textile`.
* RAFT - Used for creating templates.


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

*0.1.0*	- November 16, 2010

- Class-based implementation.
- Removed dependency on configuration file.

*0.0.1* - November 8, 2010

- Initial version of YAWL. Not working, but tagged for historical purposes.


License
-------

Copyright 2010 Ian Li, http://ianli.com

Licensed under [the MIT license](http://www.opensource.org/licenses/mit-license.php).
