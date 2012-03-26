<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.2
*
*
* $Id: index.inc.php 83 2011-02-16 04:01:07Z jeffe $:
*/

// GET PARAMS, IDENTIFIER, ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself      = 'xform_plugins';
$mypage      = rex_request('page', 'string');
$subpage     = rex_request('subpage', 'string');
$chapter     = rex_request('chapter', 'string');
$func        = rex_request('func', 'string');
$rex_info    = rex_request('rex_info', 'string');
$rex_warning = rex_request('rex_warning', 'string');
$myroot      = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself;


// FUNCTIONS & CLASSES
////////////////////////////////////////////////////////////////////////////////
require_once $myroot.'/functions/function.a319_functions.inc.php';
require_once $myroot.'/classes/class.simplepie.inc.php';
require_once $myroot.'/classes/class.redmine_connect.inc.php';


// CHAPTER DEFS ('CHAPTER GET PARAM' => array('TITLE','SOURCE','PARSEMODE'))
////////////////////////////////////////////////////////////////////////////////
$chapterpages = array (
''            => array('Value'            ,'pages/plugins.inc.php'      ,'php'),
'validate'    => array('Validate'         ,'pages/plugins.inc.php'      ,'php'),
'action'      => array('Action'           ,'pages/plugins.inc.php'      ,'php'),
'readme'      => array('Hilfe'            ,'_readme.txt'                ,'textile'),
'download'    => array('Downloads'        ,'pages/redmine_feeds.inc.php','php'),
'changelog'   => array('Changelog'        ,'pages/redmine_feeds.inc.php','php'),
'tickets'     => array('Tickets'          ,'pages/redmine_feeds.inc.php','php')
);

// BUILD CHAPTER NAVIGATION
////////////////////////////////////////////////////////////////////////////////
$chapternav = '';
foreach ($chapterpages as $chapterparam => $chapterprops)
{
  if ($chapter != $chapterparam)
  {
    $chapternav .= ' | <a href="?page=xform&subpage='.$subpage.'&chapter='.$chapterparam.'" class="chapter '.$chapterparam.' '.$chapterprops[2].'">'.$chapterprops[0].'</a>';
  }
  else
  {
    $chapternav .= ' | <span class="chapter '.$chapterparam.' '.$chapterprops[2].'">'.$chapterprops[0].'</span>';
  }
}
$chapternav = ltrim($chapternav, " | ");


// BUILD CHAPTER OUTPUT
////////////////////////////////////////////////////////////////////////////////
$addonroot = $REX['INCLUDE_PATH']. '/addons/xform/plugins/'.$myself.'/';
$source    = $chapterpages[$chapter][1];
$parse     = $chapterpages[$chapter][2];

$html = a319_incparse($addonroot,$source,$parse,true);


// OUTPUT
////////////////////////////////////////////////////////////////////////////////
require $REX['INCLUDE_PATH'] . '/layout/top.php';

rex_title('XForm <span class="addonversion">xform_plugins: '.$REX['ADDON']['plugins']['xform']['version'][$myself].'</span>', $REX['ADDON']['xform']['SUBPAGES']);


// NOTIFY UPDATES
$rc = new redmine_connect($REX['ADDON'][$myself]['redmine_url'],$REX['ADDON'][$myself]['redmine_key']);
$check = $rc->getLatest('download',$REX['ADDON']['plugins']['xform']['version'][$myself],'link');
if($check!='')
  $rex_info = 'Eine neue Version ist als Download verf&uuml;gbar: '.$check;


// ECHO REX INFO & WARNING
if($rex_info != '')
  echo rex_info($rex_info);

if($rex_warning != '')
  echo rex_warning($rex_warning);


$addclass = $chapter!='' ? 'xfp-'.$chapter.' ' : '';
echo '
<div class="'.$addclass.'rex-addon-output" id="xform-plugins">
  <h2 class="rex-hl2" style="font-size:1em">'.$chapternav.'</h2>
  <div class="rex-addon-content">
    '.$html.'
  </div>
</div>';

require $REX['INCLUDE_PATH'] . '/layout/bottom.php';
