<?php
/*!
 * YAWL Ain't Web Logs
 *
 * A very simple blogging platform in PHP.
 *
 * Copyright (c) 2012 Ian Li, http://ianli.com
 * Licensed under the MIT License (http://www.opensource.org/licenses/mit-license.php).
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Suppresses warning from strtotime. 
date_default_timezone_set('America/New_York');

include_once('php/raft.php');
include_once('php/classTextile.php');


//////////////////////////////////////////////////////////////////////////////
////////// CONFIGURATIONS ////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

define('SITE_DIRECTORY', '_site');

define('POSTS_DIRECTORY', '_posts');

define('PAGES_DIRECTORY', '_pages');


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
////////// PAGE FUNCTIONS ////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function get_page_properties($filename) {
  // Properties from the filename.
  $filename_properties = get_page_filename_properties($filename);
  
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
    return array_merge_recursive(
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

function generate_page($properties) {
  global $raft;
  
  $raft = $properties;
  
  // Set the content based on the extension.
  switch ($properties['extension']) {
    case 'html':
    case 'htm':
    default:
      $raft['content'] = $properties['content'];
      break;
  }
  
  // Generate the page.
  ob_start();
  include('_layouts/' . $raft['layout'] . '.php');
  $html = ob_get_contents();
  ob_end_clean();
  
  // Store the page.
  $permalink = $properties['permalink'];
  file_put_contents("_site/$permalink", $html);
}


//////////////////////////////////////////////////////////////////////////////
////////// POST FUNCTIONS ////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function generate_post($properties) {
  global $raft;
  
  $raft = $properties;
  
  // Set the content based on the extension.
  switch ($properties['extension']) {
    case 'html':
    case 'htm':
    default:
      $raft['content'] = $properties['content'];
      break;
  }
  
  // Generate the page.
  ob_start();
  include('_layouts/' . $raft['layout'] . '.php');
  $html = ob_get_contents();
  ob_end_clean();
  
  // Store the page.
  $permalink = $properties['permalink'];
  file_put_contents("_site/$permalink", $html);
}

// Process the post.
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
    return array_merge_recursive(
              $default_properties,
              $filename_properties,
              $front_matter_properties,
              array('content' => $content)); 
  }
}

// Get properties from the filename.
function get_post_filename_properties($filename) {
  $regex = "/^" 
            . POSTS_DIRECTORY . "\/"
            . "(((\d{4})\-(\d{2})\-(\d{2}))\-([a-z0-9-_]+))"
            . "\."
            . "([a-z0-9]+)"
            . "$/i";
  
  if (preg_match($regex, $filename, $matches)) {
	  list(, $permalink, $date, $year, $month, $day, $title, $extension) = $matches;
	  
	  $extension = strtolower($extension);
	  
	  $permalink = "$permalink.html";
	  
	  $title = implode(' ', array_map('ucfirst', explode('-', $title)));
	  
    $date = strtotime($date);
	  
    return array(
      // Values extracted from the filename:
      
      'permalink' => $permalink,
      
      'extension' => $extension,
      
      'date'      => $date,
      'year'      => $year,
      'month'     => $month,
      'day'       => $day,
      
      'title'     => $title
    );   
  } else {
    return array();
  }
}


//////////////////////////////////////////////////////////////////////////////
////////// FRONT-MATTER FUNCTIONS ////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Get the front matter and content from the file.
function get_front_matter_and_content($file) {
  // Match the file. The modes on the regex are:
  // * i - case-insensitive
  // * m - multi-line 
  // * s - . includes newlines
  if (preg_match('/^---$(.+)^---$(.+)/ims', $file, $matches)) {
    list(, $front_matter, $content) = $matches;
    
    return array($front_matter, $content);
  } else {
    return array('', $file);
  }
}

// Get properties from the front matter.
function get_front_matter_properties($front_matter) {
  // Empty set of properties (default).
  $properties = array();
  
  // Match keys and values.
  if (preg_match_all('/^([a-z0-9_]+):(.+)$/im', $front_matter, $matches)) {
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
      if (!empty($post))
        $posts[] = $post;
    }
    
    foreach ($posts as $post) {
      generate_post($post);
    }
  }
  
  // Process the pages.
  if (is_dir(PAGES_DIRECTORY)) {
    $pages = array();
    
    $filenames = rscandir(PAGES_DIRECTORY);
    foreach ($filenames as $filename) {
      $page = get_page_properties($filename);
      if (!empty($page))
        $pages[] = $page;
    }
    
    foreach ($pages as $page) {
      generate_page($page);
    }
  }
  
}

// Get the user's request.
$request = (array_key_exists('n', $_GET)) 
            ? $_GET['n'] 
            : SITE_DIRECTORY . '/index.html';

if (preg_match("/^" . SITE_DIRECTORY . "\/.+$/i", $request, $matches)) {
  if (file_exists($request)) {
    $file = file_get_contents($request);
    
    echo $file;
  } else {
    echo "File, $request, not found";
  }
} else {
  echo "Invalid $request";
}

?>