<?php
/**
 * phpfreechattools.class.php
 *
 * Copyright � 2006 Stephane Gully <stephane.gully@gmail.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details. 
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, 51 Franklin St, Fifth Floor,
 * Boston, MA  02110-1301  USA
 */

require_once dirname(__FILE__)."/phpfreechatconfig.class.php";

/**
 * phpFreeChatTools is a toolbox with misc. functions
 *
 * @author Stephane Gully <stephane.gully@gmail.com>
 */
class phpFreeChatTools
{
  function RelativePath($p1, $p2)
  {
    $p1 = realpath($p1);
    $p2 = realpath($p2);
    $res = "";
    while( $p1 != "" && $p1 != "/" && strpos($p2, $p1) === FALSE)
    {
      $res .= "../";
      $p1 = dirname($p1);
    }
    $p2 = substr($p2, strlen($p1)+1, strlen($p2)-strlen($p1));
    $res .= $p2;
    return $res;
  }

  function RecursiveMkdir($path)
  {
    // This function creates the specified directory using mkdir().  Note
    // that the recursive feature on mkdir() is broken with PHP 5.0.4 for
    // Windows, so I have to do the recursion myself.
    if (!file_exists($path))
    {
      // The directory doesn't exist.  Recurse, passing in the parent
      // directory so that it gets created.
      phpFreeChatTools::RecursiveMkdir(dirname($path));
      mkdir($path, 0777);
    }
  }

  function &GetSmarty()
  {
    $c =& phpFreeChatConfig::Instance();
    if (!class_exists("Smarty")) require_once $c->smartypath."/libs/Smarty.class.php";
    $smarty = new Smarty();
    $smarty->left_delimiter  = "~[";
    $smarty->right_delimiter = "]~";
    $smarty->template_dir    = dirname(__FILE__).'/../templates/';
    $smarty->compile_dir     = $c->data_private."/templates_c/";    
    if ($c->debug || $_SERVER["HTTP_HOST"] == "localhost")
      $smarty->compile_check = true;
    else
      $smarty->compile_check = false;
    $smarty->debugging       = false;

    // generate a unique client id (stored with JS: client side)
    // this id is used to identify client window
    // (2 clients can use the same session: then only the nickname is shared)
    $smarty->assign("clientid", md5(uniqid(rand(), true)));

    return $smarty;
  }

  /**
   * Copy a file, or recursively copy a folder and its contents
   *
   * @author      Aidan Lister <aidan@php.net>
   * @version     1.0.1
   * @link        http://aidanlister.com/repos/v/function.copyr.php
   * @param       string   $source    Source path
   * @param       string   $dest      Destination path
   * @return      bool     Returns TRUE on success, FALSE on failure
   */
  function CopyR($source, $dest)
  {
    // Simple copy for a file
    if (is_file($source)) {
      return copy($source, $dest);
    }
    
    // Make destination directory
    if (!is_dir($dest)) {
      mkdir($dest);
    }
    
    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
      // Skip pointers
      if ($entry == '.' || $entry == '..') {
	continue;
      }
      
      // Deep copy directories
      if ($dest !== "$source/$entry") {
	phpFreeChatTools::CopyR("$source/$entry", "$dest/$entry");
      }
    }
    
    // Clean up
    $dir->close();
    return true;
  }
}

?>