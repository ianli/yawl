<?php
/*!
 * YAWL Ain't Web Logs
 * Version 0.3.0pre
 * http://github.com/ianli/yawl/
 *
 * A very simple blogging platform in PHP.
 *
 * Copyright 2012 Ian Li, http://ianli.com
 * Licensed under the MIT License (http://www.opensource.org/licenses/mit-license.php).
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Suppresses warning from strtotime. 
date_default_timezone_set('America/New_York');

include_once('php/raft.php');
include_once('php/classTextile.php');
include_once('php/markdown.php');


/* TODO:
 * Support the following template variables:
 * Site
 * X site.posts
 * - site.categories.CATEGORY
 * - site.tags.TAG
 *
 * Page
 * - page.url
 * - page.content
 *
 * Post
 * X post.title
 * - post.url
 * X post.date
 * - post.id
 * - post.categories
 * - post.tags
 * X post.content
 */

//////////////////////////////////////////////////////////////////////////////
////////// CONFIGURATIONS ////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

define('SITE_DIRECTORY', '_site');

define('POSTS_DIRECTORY', '_posts');

define('PAGES_DIRECTORY', '_pages');


//////////////////////////////////////////////////////////////////////////////
////////// UTILITY FUNCTIONS /////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Merges an unspecified number of arrays together.
// Similar to array_merge in that it values of a key from an array 
// are overridden by values of a similar key from subsequent arrays.
// However, it differs in that if the value of the key is an array,
// values of a similar key from subsequent arrays are also arrays,
// the values are merged.
function array_merge_special() {
  $merged = array();
  
  $arrays = func_get_args();
  foreach ($arrays as $array) {
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        if (array_key_exists($key, $merged)) {
          if (is_array($merged[$key])) {
            $merged[$key] = array_merge($merged[$key], $value);
          } else {
            $merged[$key] = $value;
          }
        } else {
          $merged[$key] = $value;
        }
      } else {
        $merged[$key] = $value;
      }
    }
  }
  
  return $merged;
}

// Compares the `date` elements of the $a and $b.
// Used by YAWL to sort posts by reverse chronological order.
function reverse_chronological_cmp($a, $b) {
  if ($a['date'] == $b['date']) {
    return 0;
  }
  
  return ($a['date'] > $b['date']) ? -1 : 1;
}


//////////////////////////////////////////////////////////////////////////////
////////// DIRECTORY FUNCTIONS ///////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Removes files and non-empty directories.
// Based on http://www.php.net/manual/en/function.copy.php#104020
function remove_directory($dir) {
  if (is_dir($dir)) {
    empty_directory($dir);
    rmdir($dir);
  } else if (file_exists($dir)) {
    unlink($dir);
  }
}

// Empties the directory.
// Extracted from http://www.php.net/manual/en/function.copy.php#104020
function empty_directory($dir, $ignore = array('.', '..', '.gitignore')) {  
  if (is_dir($dir)) {
    $filenames = scandir($dir);
    foreach ($filenames as $filename) {
      if (!in_array($filename, $ignore))
        remove_directory("$dir/$filename");
    }
  }
}

// Recursively gets the names of the files in a directory.
function rscandir($dir, $ignore = array('.', '..', '.gitignore')) {
  if (is_dir($dir)) {
    $all = array();
    $filenames = scandir($dir);
    foreach ($filenames as $filename) {
      if (!in_array($filename, $ignore)) {
        $all = array_merge($all, rscandir("$dir/$filename"));
      }
    }
    return $all;
  } else if (file_exists($dir)) {
    return array($dir);
  } else {
    return array();
  }
}

// Returns the timestamp when the contents of this directory was last modified.
function contents_last_modified($dir) {
  $last_mtime = filemtime($dir);
  $filenames = rscandir($dir);
  foreach ($filenames as $filename) {
    $mtime = filemtime($filename);
    if ($last_mtime < $mtime)
      $last_mtime = $mtime;
  }
  
  return $last_mtime;
}


//////////////////////////////////////////////////////////////////////////////
////////// PAGE PROPERTIES FUNCTIONS /////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function get_page_properties($filename) {
  // Properties from the filename.
  $filename_properties = get_page_filename_properties($filename);
  
  if (empty($filename_properties)) {
    return array();
  }
  
  // If we get to this point, then there are values to `filename_properties`
  
  // Default properties
  $default_properties = array(
    'layout'    => 'default',
    'published' => true
  );
  
  if ($filename_properties['extension'] == 'php') {
    
    // PHP file must assign values to $front_matter and $content.
    include($filename);
    
    return array_merge_special(
            $default_properties,
            $filename_properties,
            $front_matter,
            array('content' => $content));
    
  } else {    
    
    $post = file_get_contents($filename);
    
    // Get the front matter and content.
    list($front_matter, $content) = get_front_matter_and_content($post);
    
    // Properties from the front matter.
    $front_matter_properties = get_front_matter_properties($front_matter);
    
    // Merge the properties.
    return array_merge_special(
            $default_properties,
            $filename_properties,
            $front_matter_properties,
            array('content' => $content));
  }
}

function get_page_filename_properties($filename) {
  $regex = "/^"
            . PAGES_DIRECTORY . "\/"
            . "(([a-z0-9-_]+))"
            . "\."
            . "([a-z0-9]+)"
            . "$/i";
  
  if (preg_match($regex, $filename, $matches)) {
    list(, $permalink, $title, $extension) = $matches;
    
    $extension = strtolower($extension);
    
    $permalink = "$permalink.html";
	  
	  $title = implode(' ', array_map('ucfirst', explode('-', $title)));
	  
	  return array(
	    // values extracted from the filename:
	    
	    'permalink' => $permalink,
	    
      'extension' => $extension,

      'title'     => $title
	  );
    
  } else {
    return array();
  }
}


//////////////////////////////////////////////////////////////////////////////
////////// POST PROPERTIES FUNCTIONS /////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Process the post.
// $filename  string  The filename
function get_post_properties($filename) {
  // Properties from the filename.
  $filename_properties = get_post_filename_properties($filename);
  
  if (empty($filename_properties)) {
    return array();
  } else {
    // Default properties
	  $default_properties = array(
      'layout'    => 'default',
      'published' => true
    );
    
    $post = file_get_contents($filename);
    
    // Get the front matter and content.
    list($front_matter, $content) = get_front_matter_and_content($post);
    
    // Properties from the front matter.
    $front_matter_properties = get_front_matter_properties($front_matter);
    
    // Merge the properties.
    return array_merge(
              $default_properties,
              $filename_properties,
              $front_matter_properties,
              array('content' => $content)); 
  }
}

// Get properties from the filename.
// $filename  string  The filename
function get_post_filename_properties($filename) {
  // TODO: Add one-level subfolder used as category
  $regex = "/^" 
            . POSTS_DIRECTORY . "\/"    # Posts are in POSTS_DIRECTORY
            . "(([a-z0-9-_]+)\/)?"        # Posts can be in a subfolder
            . "(((\d{4})\-(\d{2})\-(\d{2}))\-([a-z0-9-_]+))"
            . "\."
            . "([a-z0-9]+)"
            . "$/i";
  
  if (preg_match($regex, $filename, $matches)) {
    list(, $category_dir, $category, $permalink, $date, $year, $month, $day, $title, $extension) = $matches;
    
    $permalink = "$category_dir$permalink.html";
    
    $date = strtotime($date);
	  
	  $title = implode(' ', array_map('ucfirst', explode('-', $title)));
	  
	  $extension = strtolower($extension);
	  
    return array(
      // Values extracted from the filename:
      
      'permalink' => $permalink,
      
      'filename'  => $filename,
      
      'extension' => $extension,
      
      'date'      => $date,
      'year'      => $year,
      'month'     => $month,
      'day'       => $day,
      
      'title'     => $title,
      
      'category' => $category
    );   
  } else {
    return array();
  }
}


//////////////////////////////////////////////////////////////////////////////
////////// FRONT-MATTER FUNCTIONS ////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Get the front matter and content from the file.
// $file  A string containing the contents of a file.
function get_front_matter_and_content($file) {
  // Match the file. The modes on the regex are:
  // * i - case-insensitive
  // * m - multi-line 
  // * s - . includes newlines
  if (preg_match('/^---\n(.+)^---\n(.+)/ims', $file, $matches)) {
    list(, $front_matter, $content) = $matches;
    
    return array($front_matter, $content);
  } else {
    return array('', $file);
  }
}

// Get properties from the front matter.
// $front_matter  A string representing the front matter.
function get_front_matter_properties($front_matter) {
  // Empty set of properties (default).
  $properties = array();
  
  // Match keys and values.
  if (preg_match_all('/^([a-z0-9_]+)\s*:\s*(.+)$/im', $front_matter, $matches)) {
    list(, $keys, $values) = $matches;
    
    $properties = array_combine($keys, $values);
    
    foreach ($properties as $key => $value) {      
      switch ($key) {
        case 'published':
          $published = $properties['published'];
          if ($published == 'false') {
            $properties['published'] = false;
          }
          break;
          
        case 'date':
          $date = strtotime($properties['date']);
          $properties['date'] = $date;
          
          $date_array = getdate($date);
          $properties['year']   = $date_array['year'];
          $properties['month']  = $date_array['mon'];
          $properties['day']    = $date_array['mday']; 
          break;
          
        case 'category':
        case 'categories':
          $categories = $properties['categories'];
          $properties['categories'] = array_map('trim', explode(',', $categories));
          break;
          
        case 'tag':
        case 'tags':
          $tags = $properties['tags'];
          $properties['tags'] = array_map('trim', explode(',', $tags));
          break;
      }
      
    }
  }
  
  return $properties;
}


//////////////////////////////////////////////////////////////////////////////
////////// GENERATION FUNCTIONS //////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function generate_page_or_post($page_properties, $site_properties) {
  global $raft;
  
  foreach ($page_properties as $key => $value) {
    $raft["page.$key"] = $value;
  }
  
  foreach ($site_properties as $key => $value) {
    $raft["site.$key"] = $value;
  }
  
  // Set the content based on the extension.
  $content = '';
  
  switch ($page_properties['extension']) {
    case 'php':
      ob_start();
      $page_properties['content']();
      $content = ob_get_contents();
      ob_end_clean();
      break;
    case 'textile':
      $textile = new Textile();
      $content = $textile->TextileThis($page_properties['content']);
      break;
    case 'markdown':
    case 'md':
      $content = Markdown($page_properties['content']);
      break;
    case 'html':
    case 'htm':
    default:
      $content = $page_properties['content'];
      break;
  }
  
  $raft['page.content'] = $content;
  
  // Generate the page.
  ob_start();
  include('_layouts/' . $raft['page.layout'] . '.php');
  $html = ob_get_contents();
  ob_end_clean();
  
  // Store the page.
  $permalink = $page_properties['permalink'];
  $category = $page_properties['category'];
  if ($category) {
    $dir = SITE_DIRECTORY . "/$category";
    
    if (!file_exists($dir)) {
      mkdir($dir);
    }
    
    if (is_dir($dir)) {
      file_put_contents(SITE_DIRECTORY . "/$permalink", $html);
    }
    
  } else {
    file_put_contents(SITE_DIRECTORY . "/$permalink", $html);
  }
}

function generate_site($site_properties) {
  if ($posts = $site_properties['posts']) {
    foreach ($posts as $post) {
      generate_page_or_post($post, $site_properties);
    }
  }
  
  if ($pages = $site_properties['pages']) {
    foreach ($pages as $page) {
      generate_page_or_post($page, $site_properties);
    }
  }
}


//////////////////////////////////////////////////////////////////////////////
////////// MAIN //////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function should_update_site() {
  return filemtime(SITE_DIRECTORY) < contents_last_modified(getcwd())
          || array_key_exists('update', $_GET);
}

if (should_update_site()) {
  
  // IMPORTANT: We're updating the _site/ directory, so clear it.
  empty_directory(SITE_DIRECTORY);

  // Process the posts.
  if (is_dir(POSTS_DIRECTORY)) {
    $posts = array();
    
    $filenames = rscandir(POSTS_DIRECTORY);
    foreach ($filenames as $filename) {
      $post = get_post_properties($filename);
      if (!empty($post) && $post['published'])
          $posts[] = $post;
    }
  }
  
  // Process the pages.
  if (is_dir(PAGES_DIRECTORY)) {
    $pages = array();
    
    $filenames = rscandir(PAGES_DIRECTORY);
    foreach ($filenames as $filename) {
      $page = get_page_properties($filename);
      if (!empty($page) && $page['published'])
          $pages[] = $page;
    }
  }
  
  usort($posts, "reverse_chronological_cmp");
  
  generate_site(array(
    'posts' => $posts,
    'pages' => $pages
  ));
  
}

// Get the user's request.
$request = (array_key_exists('n', $_GET)) 
            ? $_GET['n'] 
            : 'index.html';

# Process requests of the form "(category/)filename.html"
if (preg_match("/^([a-z0-9-_]+\/)?([a-z0-9-_]+)\.html/i", $request, $matches)) {
  $filename = SITE_DIRECTORY . "/$request";
  if (file_exists($filename)) {
    $file = file_get_contents($filename);     
    echo $file;
  } else {
    echo "File, $filename, not found";
  }
} else {
  echo "Invalid $request";
}

?>