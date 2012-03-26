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
* $Id: plugins.inc.php 69 2011-01-19 01:43:57Z jeffe $:
*/

// GET PARAMS, IDENTIFIER, ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself      = 'xform_plugins';
$subpage     = rex_request('subpage', 'string');
$chapter     = rex_request('chapter', 'string');
$func        = rex_request('func', 'string');
$open_plugin = rex_request('open_plugin', 'string');

$myroot = XFORM_PLUGINS_ROOT;

// SWITCH PLUGIN TYPE
////////////////////////////////////////////////////////////////////////////////
switch($chapter)
{
  case('action'):
    $include_dir = 'action';
    break;

  case('validate'):
    $include_dir = 'validate';
    break;

  default:
    $include_dir = 'value';
}

$include_root = $myroot.'/xform/classes/'.$include_dir.'/';
$slide_js = '';


$include_files = a319_scandir($include_root,0,array('config.*','settings.*','*.txt'),array('*inc.php'));

if($include_files)
{
  foreach($include_files['files'] as $key => $file)
  {
    $rep = array('class.xform.','.inc.php');
    $xform_name = str_replace($rep,'',$file);

    if($open_plugin == $xform_name)
    {
      $display_state = '';
    }
    else
    {
      $display_state = 'display:none;';
    }

    echo '
    <div class="rex-addon-output">
      <h2 class="rex-hl2" style="font-size:1em"><a id="toggle-switch-'.$xform_name.'">'.strtoupper($xform_name).'</a> <em style="color:gray;font-style:normal;font-weight:normal"> ('.$file.')</em></h2>
      <div class="rex-addon-content xform-plugin" id="toggle-block-'.$xform_name.'" style="'.$display_state.'">
        <div class="xform_plugins">';

    $slide_js .= '
  $("#toggle-switch-'.$xform_name.'").click(function() {
    $("#toggle-block-'.$xform_name.'").slideToggle("fast");
  });

    ';

    if($display_state == '')
    {
      $slide_js .= '$("#toggle-block-'.$xform_name.'").show();
      ';
    }

    $classname = 'rex_xform_'.$xform_name;
    include_once(XFORM_PLUGINS_ROOT.'/xform/classes/'.$include_dir.'/class.xform.'.$xform_name.'.inc.php');
    $class = new $classname;
    echo '<div class="xform-description">'.$class->getDescription().'</div>';

    if(file_exists($include_root.'settings.'.$xform_name.'.inc.php'))
    {
       a319_incparse($include_root,'settings.'.$xform_name.'.inc.php','php',false);
    }

    if(file_exists($include_root.'help.'.$xform_name.'.textile.txt') || file_exists($include_root.'help.'.$xform_name.'.txt'))
    {
      echo '<div class="rex-form">
      <fieldset class="rex-form-col-1">
      <legend>Hilfe</legend>
      <div class="rex-form-wrapper">';

      if(file_exists($include_root.'help.'.$xform_name.'.textile.txt'))
      {
        a319_incparse($include_root,'help.'.$xform_name.'.textile.txt','textile',false);
      }

      if(file_exists($include_root.'help.'.$xform_name.'.txt'))
      {
        a319_incparse($include_root,'help.'.$xform_name.'.txt','txt',false);
      }
      echo '</div></fieldset></div>';
    }

    echo '
        </div><!-- /.xform_plugins -->
      </div><!-- /.rex-addon-content xform-plugin #toggle-'.$xform_name.' -->
    </div><!-- /.rex-addon-output -->
    ';
  }

  echo '
<script type="text/javascript">
<!--
jQuery(function($) {

'.$slide_js.'

});
//-->
</script>
';
}
else
{
  echotextile('h2. Keine Plugins vorhanden.');
}
