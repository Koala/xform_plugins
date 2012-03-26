<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.2
*
* $Id: config.inc.php 147 2012-01-31 00:23:51Z jeffe $:
*/ 

// ADDON IDENTIFIER & ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself = 'xform_plugins';
$myroot = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself;
define('XFORM_PLUGINS_ROOT',$myroot);

// ADDON VERSION
////////////////////////////////////////////////////////////////////////////////
$Revision = '';
$REX['ADDON'][$myself]['VERSION'] = array
(
'VERSION'      => 0,
'MINORVERSION' => 2,
'SUBVERSION'   => preg_replace('/[^0-9]/','',"$Revision: 147 $")
);

// ADDON REX COMMONS
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON']['rxid'][$myself]        = '319';
$REX['ADDON']['page'][$myself]        = $myself;
$REX['ADDON']['perm'][$myself]        = $myself.'[]';
$REX['ADDON']['version'][$myself]     = implode('.', $REX['ADDON'][$myself]['VERSION']);
$REX['ADDON']['author'][$myself]      = 'rexdev.de';
$REX['ADDON']['supportpage'][$myself] = 'forum.redaxo.de';
$REX['PERM'][]                        = $myself.'[]';

// REDMINE PROJECT
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON'][$myself]['redmine_url'] = 'http://svn.rexdev.de/redmine/projects/xform-plugins';
$REX['ADDON'][$myself]['redmine_key'] = '0395cccf9fac8fe92dffaf0c697f0578d6bff269';

// FUNCTIONS
////////////////////////////////////////////////////////////////////////////////
require_once $myroot.'/functions/function.a319_functions.inc.php';

// XFORM INCLUDES
////////////////////////////////////////////////////////////////////////////////
include_once $REX['INCLUDE_PATH'].'/addons/xform/classes/basic/class.xform.value.abstract.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/xform/classes/basic/class.xform.validate.abstract.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/xform/classes/basic/class.xform.action.abstract.inc.php';

// DYNAMIC CONFIG INCLUDES
////////////////////////////////////////////////////////////////////////////////
$configs = a319_scandir($myroot.'/xform/',0,array(),array('config.*'));
foreach($configs['files'] as $config)
{
  include_once $configs['root'].$config;
}

// BACKEND CSS
////////////////////////////////////////////////////////////////////////////////
if ($REX['REDAXO'])
{
  $header = array(
  '  <link rel="stylesheet" type="text/css" href="../files/addons/xform/plugins/'.$myself.'/backend.css" media="screen, projection, print" />'
  );
  rex_register_extension('PAGE_HEADER', 'a319_header_add',$header);
}

//  EP
////////////////////////////////////////////////////////////////////////////////
rex_register_extension('ADDONS_INCLUDED', 'rexdev_xform_plugins_add');

function rexdev_xform_plugins_add($params)
{
  global $REX;
  $myself = 'xform_plugins';
  $myroot = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself;
  $REX['ADDON']['xform']['classpaths']['value'][]    = $myroot.'/xform/classes/value/';
  $REX['ADDON']['xform']['classpaths']['validate'][] = $myroot.'/xform/classes/validate/';
  $REX['ADDON']['xform']['classpaths']['action'][]   = $myroot.'/xform/classes/action/';

  /*gregors hack */
  $REX['ADDON']['xform']['SUBPAGES'][] = array('plugins', 'Rexdev-Plugins');
  if (rex_request('page', 'string') == 'xform' && rex_request('subpage', 'string') == 'plugins')
  {
    $REX['ADDON']['navigation']['xform']['path'] = $REX['INCLUDE_PATH'].'/addons/xform/plugins/xform_plugins/pages/index.inc.php';
  }
}
