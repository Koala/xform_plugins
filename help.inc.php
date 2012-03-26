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
* $Id: help.inc.php 150 2012-01-31 00:23:51Z jeffe $:
*/

// ADDON IDENTIFIER & ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself = 'xform_plugins';
$myroot = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself.'/';


// FUNCTIONS & CLASSES
////////////////////////////////////////////////////////////////////////////////
require_once $myroot.'/functions/function.a319_functions.inc.php';
require_once $myroot.'/classes/class.simplepie.inc.php';

// BACKEND CSS
////////////////////////////////////////////////////////////////////////////////
if ($REX['REDAXO']) {
  $header = array(
  '  <link rel="stylesheet" type="text/css" href="../files/addons/xform/plugins/'.$myself.'/backend.css" media="screen, projection, print" />'
  );
  rex_register_extension('PAGE_HEADER', 'rexdev_header_add',$header);
}

// HELP CONTENT
////////////////////////////////////////////////////////////////////////////////
$help_includes = array (
'readme'      => array('XForm Plugins Readme','_readme.txt','textile'),
'changelog'   => array('XForm Plugins Changelog','_changelog.txt','textile')
);

// OUTPUT
////////////////////////////////////////////////////////////////////////////////
foreach($help_includes as $key => $def)
{
  echo '
  <div class="rex-addon-output" id="xform-plugins">
    <h2 class="rex-hl2" style="font-size:1em">'.$def[0].' <span style="color: gray; font-style: normal; font-weight: normal;">( '.$def[1].' )</span></h2>
    <div class="rex-addon-content">
      <div class="xform_plugins">
        '.a319_incparse($myroot,$def[1],$def[2],true).'
      </div>
    </div>
  </div>';
}
