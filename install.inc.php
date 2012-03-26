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
* $Id: install.inc.php 151 2012-01-31 00:23:51Z jeffe $:
*/


// ADDON IDENTIFIER, VARS & DEFS
////////////////////////////////////////////////////////////////////////////////
$myself = 'xform_plugins';
$xform_required = 1.7;
$xform_installed = isset($ADDONSsic) ? $ADDONSsic['version']['xform'] : false;
$REX['ADDON']['installmsg'][$myself] = '';

// INSTALL PREFLIGHT CHECKS
////////////////////////////////////////////////////////////////////////////////
if (intval(PHP_VERSION) < 5)
{
  $REX['ADDON']['installmsg'][$myself] .= '<li style="margin:2px;">Dieses Plugin ben&ouml;tigt PHP 5.</li>';
  $REX['ADDON']['install'][$myself] = 0;
}

if(!isset($ADDONSsic) || !isset($ADDONSsic['plugins']['xform']['install']['xform_plugins']))
{
  $REX['ADDON']['installmsg'][$myself] .= '<li style="margin:2px;">'.$myself.' ist ein Plugin und kein Addon - der richtige Installationsort ist der Ordner <code style="font-size:12px;color:gray;">./addons/xform/plugins/</code> (muß manuell angelegt werden wenn nicht vorhanden).</li>';
  $REX['ADDON']['install'][$myself] = 0;
}

if($xform_installed < $xform_required)
{
  $REX['ADDON']['installmsg'][$myself] .= '<li style="margin:2px;">Dieses Plugin setzt mindestens Version '.$xform_required.' des XForm Addons voraus.</li>';
}


if($REX['ADDON']['installmsg'][$myself]!='')
{
  $REX['ADDON']['installmsg'][$myself] = '<ol style="margin:6px 0 0 20px;padding:0;">'.$REX['ADDON']['installmsg'][$myself].'</ol>';
}
else
{
  $REX['ADDON']['install'][$myself] = 1;
}