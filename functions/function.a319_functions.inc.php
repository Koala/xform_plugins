<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.2
*
* $Id: function.a319_functions.inc.php 72 2011-01-21 05:29:13Z jeffe $:
*/


/**
* RECUSIVE FILESYSTEM SCAN
*
* @author <a href="http://rexdev.de">rexdev.de</a>
*
* @param $source    (string)    Pfad des zu scanenden Verzeichnisses
* @param $limit     (int)       Scantiefe limitiert (1.-n.) Level bzw. nicht (0)
* @param $blacklist (array)     Auszuschließende Ordner oder Dateien per:
*                               - volle Ordner/Dateinamen
*                               - wildcard: 'prefix*' od. '*suffix';
* @param $whitelist (array)     Ergebnis (nur Dateien) eingrenzen auf:
*                               - wildcard: 'prefix*' od. '*suffix';
*
* @return      (array/null)     Array
*                               (
*                                   [basedir]         => (absolute PATH)/
*                                   [depth]           => (1-n)
*                                   [counter]         => Array
*                                       (
*                                           [folders] => (relative PATH)/
*                                           [files]   => (relative PATH)
*                                       )
*                                   [folders]         => Array
*                                       (
*                                           [1]       => (relative PATH)
*                                           [2]       => ...
*                                       )
*                                   [files]           => Array
*                                       (
*                                           [1]       => (relative PATH)
*                                           [2]       => ...
*                                       )
*                               )
*/
if (!function_exists('a319_scandir'))
{
  function a319_scandir($source, $limit=0, $blacklist=array(), $whitelist=array(), &$result=array())
  {
    // SANITIZE SOURCE PATH, CHECK IF IS DIR
    $source= '/'.trim($source,'/ ').'/';
    if(!is_dir($source))
    {
      return NULL;
    }

    // INIT RESULT ARRAY
    if(!isset($result['root']))
    {
      $root = $result['root'] = $source;
      $result['folders'] = NULL;
      $result['files'] = NULL;
      $result['depth'] = 1;
      $result['counter']['folders'] = 0;
      $result['counter']['files'] = 0;
    }

    // SCAN SOURCE DIR WHILE IGNORING FULL ITEMNAMES (WILDCARDS WON'T MATCH)
    $ignore = array('.DS_Store','.svn','.','..'); // bulitin ignores
    $ignore = array_merge((array)$ignore,(array)$blacklist); // merge bulitin irgnores with user blacklist
    $rawscan = scandir($source);
    $dirscan = array_diff($rawscan, $ignore); // subtract ignores from full listing

    // RESCAN RESULT WITH WILDCARDS
    foreach($ignore as $i) // run through ignores (blacklist)
    {
      $i = explode('*',$i); // explode values strings to array by wildcard character
      if(count($i) == 2) // is valid wildcard string
      {
        if(array_search('', $i) == 0) // wildcard string is extension
        {
          foreach($dirscan as $item) // run through prior scan result
          {
            if(substr($item, '-'.strlen($i[1])) == $i[1]) // wipe extension matches from $dirscan array
            {
              $dirscan = array_diff($dirscan, array($item));
            }
          }

        }
        else // wildcard string is prefix
        {
          foreach($dirscan as $item) // run through prior scan result
          {
            if(substr($item, 0, strlen($i[0])) == $i[0]) // wipe prefix matches from $dirscan array
            {
              $dirscan = array_diff($dirscan, array($item));
            }
          }
        }
      }
    }

    // WALK THROUGH RESULT RECURSIVELY
    foreach($dirscan as $item)
    {
      // DO DIR STUFF
      if (is_dir($source.$item))
      {
        $i = count($result['folders']) + 1;
        $result['folders'][$i] = str_replace($result['root'], '', $source.$item).'/';
        $result['counter']['folders']++;

        $depth = count(explode('/',str_replace($result['root'], '', $source.$item.'/')));
        if($depth>$result['depth'])
        {
          $result['depth'] = $depth;
        }

        // RECURSION IF NOT LIMITED
        if($limit && intval($limit))
        {
          if($limit > $depth)
          {
            a319_scandir($source.$item.'/', $limit, $blacklist, $whitelist, $result);
          }
        }
        else
        {
          a319_scandir($source.$item.'/', $limit, $blacklist, $whitelist, $result);
        }
      }

      // DO FILE STUFF
      elseif (is_file($source.$item))
      {
        $depth = count(explode('/',$source));

        if(count($whitelist)>0) // LIMIT ACCORDING WHITELIST
        {
          foreach($whitelist as $w)
          {
            $w = explode('*',$w); // string auf wildcard prüfen per zerlegen
            if(count($w) == 2) // korrekter wildcard string  -> weiter
            {
              if(array_search('', $w) == 0) // extension
              {
                if(substr($item, '-'.strlen($w[1])) == $w[1])
                {
                  $i = count($result['files']) + 1;
                  $result['files'][$i] = str_replace($result['root'], '', $source.$item);
                  $result['counter']['files']++;
                }
              }
              else /* prefix */
              {
                if(substr($item, 0, strlen($w[0])) == $w[0])
                {
                  $i = count($result['files']) + 1;
                  $result['files'][$i] = str_replace($result['root'], '', $source.$item);
                  $result['counter']['files']++;
                }
              }
            }
          }
        }
        else // NO WHITELIST -> GET ALL
        {
          $i = count($result['files']) + 1;
          $result['files'][$i] = str_replace($result['root'], '', $source.$item);
          $result['counter']['files']++;
        }
      }
    }

    // CHECK RESULT, IF NO MATCHES AT ALL -> RETURN NULL
    if ($result['files']==NULL && $result['folders']==NULL)
    {
      return NULL;
    }
    else
    {
      return $result;
    }
  }
}


/**
* RECURSIVE FILE COPY FUNCTION
*/
if (!function_exists('a319_recursive_copy'))
{
  function a319_recursive_copy($source, $target, $makedir=TRUE, &$result=array(), $counter=1,$folderPermission=0740, $filePermission=0710)
  {
    // SCAN SOURCE DIR WHILE IGNORING  CERTAIN FILES
    $ignore = array('.DS_Store','.svn','.','..');
    $dirscan = array_diff(scandir($source), $ignore);
    
    // WALK THROUGH RESULT RECURSIVELY
    foreach($dirscan as $item)
    {
      
      // DO DIR STUFF
      if (is_dir($source.$item)) /* ITEM IS A DIR */
      {
        if(!is_dir($target.$item) && $makedir=TRUE) /* DIR NONEXISTANT IN TARGET */
        { 
          if(mkdir($target.$item)) /* CREATE DIR IN TARGET */
          {
            if(!chmod($source.$item,$folderPermission))
            {
              echo rex_warning('Rechte für "'.$target.$item.'" konnten nicht gesetzt werden!');
            }
          }
          else
          {
            echo rex_warning('Das Verzeichnis '.$source.$item.' konnte nicht angelegt werden!');
          }
        }

        // RECURSION
        a319_recursive_copy($source.$item.'/', $target.$item.'/', $makedir, $result, $counter);
      }
      
      // DO FILE STUFF
      elseif (is_file($source.$item)) /* ITEM IS A FILE */
      {
        if (rex_is_writable($target)) /* CHECK WRITE PERMISSION */
        {
          
          if(is_file($target.$item)) /* FILE EXISTS IN TARGET */
          {
            $slug = date("d.m.y_H.i.s_");
            if(!rename($target.$item,$target.$slug.$item))
            {
              echo rex_warning('Datei "'.$target.$item.'" konnte nicht umbenannt werden!');
            }
            else
            {
              if(!copy($source.$item,$target.$item))
              {
                $result[$counter]['path'] = $target;
                $result[$counter]['item'] = $item;
                $result[$counter]['copystate'] = 0;
                echo rex_warning('Datei "'.$target.$item.'" konnte nicht geschrieben werden!');
              }
              else
              {
                $result[$counter]['path'] = $target;
                $result[$counter]['item'] = $item;
                $result[$counter]['copystate'] = 1;
                echo rex_info('Datei "'.$target.$item.'" wurde erfolgreich angelegt.');
              }
            }
          }
          else
          {
            if(!copy($source.$item,$target.$item))
            {
              $result[$counter]['path'] = $target;
              $result[$counter]['item'] = $item;
              $result[$counter]['copystate'] = 0;
              echo rex_warning('Datei "'.$target.$item.'" konnte nicht geschrieben werden!');
            }
            else
            {
              $result[$counter]['path'] = $target;
              $result[$counter]['item'] = $item;
              $result[$counter]['copystate'] = 1;
              echo rex_info('Datei "'.$target.$item.'" wurde erfolgreich angelegt.');
            }
          }
        }
        else
        {
          echo rex_warning('Keine Schreibrechte für das Verzeichnis "'.$target.'" !');
        }
      }
      $counter++;
    }
    return $result;
  }
}


/**
* CONTENT PARSER FUNCTION
* @author rexdev.de
*/
if (!function_exists('a319_incparse'))
{
  function a319_incparse($root,$source,$parsemode,$return=false)
  {

    switch ($parsemode)
    {
      case 'textile':
      $source = $root.$source;
      $content = file_get_contents($source);
      $html = rexdev_textileparser($content,true);
      break;

      case 'txt':
      $source = $root.$source;
      $content = file_get_contents($source);
      $html =  '<pre class="plain">'.$content.'</pre>';
      break;

      case 'raw':
      $source = $root.$source;
      $content = file_get_contents($source);
      $html = $content;
      break;

      case 'php':
      $source = $root.$source;
      $html =  get_include_contents($source);
      break;



      case 'iframe':
      $html = '<iframe src="'.$source.'" width="99%" height="600px"></iframe>';
      break;

      case 'jsopenwin':
      $html = 'Externer link: <a href="'.$source.'">'.$source.'</a>
      <script language="JavaScript">
      <!--
      window.open(\''.$source.'\',\''.$source.'\');
      //-->
      </script>';
      break;

      case 'extlink':
      $html = 'Externer link: <a href="'.$source.'">'.$source.'</a>';
      break;
    }

    if($return)
    {
      return $html;
    }
    else
    {
      echo $html;
    }

  }
}


/**
* TEXTILE PARSER FUNCTION
*/
if (!function_exists('rexdev_textileparser'))
{
  function rexdev_textileparser($textile,$return=false)
  {
    if(OOAddon::isAvailable("textile"))
    {
      global $REX;

      if($textile!='')
      {
        $textile = htmlspecialchars_decode($textile);
        $textile = str_replace("<br />","",$textile);
        $textile = str_replace("&#039;","'",$textile);
        if (strpos($REX['LANG'],'utf'))
        {
          $html = rex_a79_textile($textile);
        }
        else
        {
          $html =  utf8_decode(rex_a79_textile($textile));
        }

        if($return)
        {
          return $html;
        }
        else
        {
          echo $html;
        }
      }

    }
    else
    {
      $html = rex_warning('WARNUNG: Das <a href="index.php?page=addon">Textile Addon</a> ist nicht aktiviert! Der Text wird ungeparst angezeigt..');
      $html .= '<pre>'.$textile.'</pre>';

      if($return)
      {
        return $html;
      }
      else
      {
        echo $html;
      }
    }
  }
}

/**
* ECHO TEXTILE FORMATED STRING
*/
if (!function_exists('echotextile'))
{
  function echotextile($msg) {
    global $REX;
    if(OOAddon::isAvailable("textile")) {
      if($msg!='') {
         $msg = str_replace("	","",$msg); // tabs entfernen
         if (strpos($REX['LANG'],'utf')) {
          echo rex_a79_textile($msg);
        } else {
          echo utf8_decode(rex_a79_textile($msg));
        }
      }
    } else {
      $fallback = rex_warning('WARNUNG: Das <a href="index.php?page=addon">Textile Addon</a> ist nicht aktiviert! Der Text wird ungeparst angezeigt..');
      $fallback .= '<pre>'.$msg.'</pre>';
      echo $fallback;
    }
  }
}


/**
* GET INCLUDE CONTENTS
* @link http://php.net/manual/de/function.include.php
*/
if (!function_exists('get_include_contents'))
{
  function get_include_contents($filename) {
    if (is_file($filename)) {
      ob_start();
      include $filename;
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
    }
    return false;
  }
}


/**
* Generische Funktion zum Einbinden von CSS, JS, .. ins Redaxo Backend
*
* @param $params Extension-Point Parameter
* @author rexdev.de
* @version 0.1
*
* Beispiel:
* ---------------------------------------------------------------
* // include als array (zwingend, auch wenn nur ein wert!)
* $inc = array (
* '<link rel="stylesheet" type="text/css" href="../files/addons/'.$myself.'/backend.css" />,
* '<script type="text/javascript" src="../files/addons/'.$myself.'/foo.js"></script>'
* );
*
* // include per 3. parameter an ep übergeben
* if ($REX['REDAXO']) {
*   include_once $myroot.'/functions/function.rexdev_header_add.inc.php';
*   rex_register_extension('PAGE_HEADER', 'rexdev_header_add', $inc);
* }
* ------------------------------------------------------------------------------
*/
if(!function_exists('a319_header_add'))
{
  function a319_header_add($params) {

    if (is_array($params) && count($params)>2) {
      foreach($params as $key => $val) {
        if($key !== 'subject' && $key !== 'extension_point') {
        $params['subject'] .= "\n".$val;
        }
      }
    }

    return $params['subject'];
  }
}