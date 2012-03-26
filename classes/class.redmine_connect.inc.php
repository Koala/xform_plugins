<?php
/**
* @name    REXDEV Redmine Project Parser Class
* @link    http://rexdev.de/
* @package redaxo4
* @version 0.1
*
* $Id: class.redmine_connect.inc.php 89 2011-03-11 17:21:39Z jeffe $:
*/

class redmine_connect
{
  public  $url;
  public  $key;
  public  $feed_items;
  public  $cache_duration;
  public  $max_feed_items;
  public  $cache_location;
  private $parser_class;
  private $parser_path;
  private $error;


  /* private */
  private function registerError($err=false,$err_type=false)
  {
      $err = 'CLASS REDMINE_CONNECT: '.$err.'.';
      $this->error .= $err.'<br />'.PHP_EOL;
      if($err_type)
        trigger_error($err, $err_type);
  }


  /* private */
  private function getFeed()
  {
    require_once($this->parser_path);
    if(!class_exists($this->parser_class))
      $this->registerError($this->parser_class.' not availlable',E_USER_ERROR);

    switch($this->parser_class)
    {
      case 'some_other_parser':
        // implement other parser
        break;

      default:
        $Parser = new SimplePie();
        $Parser->set_cache_location($this->cache_location);
        $Parser->set_cache_duration($this->cache_duration);
        $Parser->set_feed_url($this->url);
        $Parser->init();
        $Parser->handle_content_type();
        $this->feed_items = $Parser->get_items(0,$this->max_feed_items);
        unset($Parser);
    }
  }


  /* constructor */
  public function redmine_connect($project_url=false, $project_key=false)
  {
    global $REX;
    $this->error = false;

    $this->url = !$project_url ? $this->registerError('no project url provided',E_USER_ERROR) : $project_url;
    $this->key = !$project_key ? $this->registerError('no project key provided',E_USER_ERROR) : $project_key;

    $this->setCacheDuration(300);
    $this->setMaxFeedItems(20);
    $this->setCacheLocation($REX['INCLUDE_PATH'].'/generated/files');
    $this->setParserClass('simplepie');
    $this->setParserPath('class.simplepie.inc.php');
  }


  public function setParserClass($v)
  {
    $this->parser_class = $v;
  }


  public function setParserPath($v)
  {
    $this->parser_path = $v;
  }


  public function setCacheDuration($v)
  {
    $this->cache_duration = (int) $v;
  }


  public function setMaxFeedItems($v)
  {
    $this->max_feed_items = (int) $v;
  }


  public function setCacheLocation($v)
  {
    $this->cache_location = $v;
  }


  public function getLatest($type=false,$current=false,$return='link',$dl_regex='/([0-9]+.[0-9]+.[0-9]+).zip/',$rev_regex='/Revision ([0-9]+):/')
  {
    global $REX;
    $valid_types   = array('download','revision');
    $valid_returns = array('link','version');

    if(!$type || !in_array($type,$valid_types))
      $this->registerError('invalid or no type for getLatest() provided');

    if(!in_array($return,$valid_returns))
      $this->registerError('wrong return type for getLatest() provided');

    if(!$current)
      $this->registerError('no current version for getLatest() provided');

    if(!$this->error)
    {
      $this->getFeed();

      if(count($this->feed_items)>0)
      {
        foreach($this->feed_items as $item)
        {
          $title = $item->get_title();
          $id    = $item->get_id();
          $title = $item->get_title();

          switch($type)
          {
            case 'download':
              $regex = $dl_regex;
              break;
            default:
              $regex = $rev_regex;
          }

          $match = array();
          preg_match($regex,$title,$match);
          if(count($match)>0)
          {
            if(version_compare($match[1],$current)>0)
            {
              switch($return)
              {
                case 'link':
                  return '<a class="jsopenwin" href="'.$id.'">'.$title.'</a>';
                  break;
                default:
                  return $match[1];
              }
              break;
            }
          }

        }
      }
    }
    else
    {
      return $this->error;
    }

  }

  public function getList($type=false)
  {
    global $REX;

    if(!$this->error)
    {
      $valid_types = array('download','tickets','changelog');
      $feed_url  = rtrim($this->url,'/').'/activity.atom?key='.$this->key;
      $files_url = rtrim($this->url,'/').'/files';
      $html = '';
  
      if(!$type || !in_array($type,$valid_types))
      {
        $this->registerError('wrong or no list type provided',E_USER_ERROR);
        return $this->error;
      }

      $this->getFeed();

      if(count($this->feed_items)>0)
      {
        foreach($this->feed_items as $item)
        {
          $date  = '<strong>'.$item->get_date('d.m.Y').'</strong> '.$item->get_date('H:i');
          $id    = $item->get_id();
          $title = $item->get_title();
    
          if(!strpos($REX['LANG'],'utf'))
          {
            $title = utf8_decode($title);
          }
    
          switch($type)
          {
            case 'download':
              if (substr($title,-4,4)=='.zip')
              {
                $html .= '<span class="redmine-date">'.$date.'</span><a class="" href="'.$id.'">'.$title.'</a><li>';
              }
              break;
    
            case 'tickets':
              if (substr($title,0,8)!='Revision' && substr($title,-4,4)!='.zip')
              {
                $html .= '<li><span class="redmine-date">'.$date.'</span><a class="jsopenwin" target="_blank" href="'.$id.'">'.$title.'</a><li>';
              }
              break;
    
            case 'changelog':
              if (substr($title,0,8)=='Revision')
              {
                $html .= '<li><span class="redmine-date">'.$date.'</span><a class="jsopenwin" target="_blank" href="'.$id.'/diff'.'">'.$title.'</a></li>';
              }
              break;
          }
        }
      }
  
      if($html=='')
      {
        $html = '<ul class="redmine-feed">';
        $html .= '<li><strong>Redmine '.$type.' feed:</strong> Keine aktuellen Eintragungen.</li>';
        if($type == 'download')
        {
          $html .= '<li><span class="redmine-date">Downloadseite:</span><a class="jsopenwin" target="_blank" href="'.$files_url.'">'.$files_url.'</a></li>';
        }
        $html .= '</ul>';
      }
      else
      {
        $html = '<ul class="redmine-feed">'.$html;
        if($type == 'download')
        {
          $html .= '<li><span class="redmine-date">Downloadseite:</span><a class="jsopenwin" target="_blank" href="'.$files_url.'">'.$files_url.'</a></li>';
        }
        $html .= '</ul>';
      }

      return $html;
    }
  }

}

?>