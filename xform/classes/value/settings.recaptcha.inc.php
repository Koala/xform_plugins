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
* $Id: settings.recaptcha.inc.php 60 2011-01-19 00:05:14Z jeffe $:
*/

global $REX;

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$page            = rex_request('page', 'string');
$subpage         = rex_request('subpage', 'string');
$func            = rex_request('func', 'string');

$myfunc          = 'recaptchaupdate';

$rc_label                = rex_request('rc_label', 'string');
$rc_warning              = rex_request('rc_warning', 'string');
$rc_pubkey               = rex_request('rc_pubkey', 'string');
$rc_privkey              = rex_request('rc_privkey', 'string');
$rc_lang                 = rex_request('rc_lang', 'string');
$rc_theme                = rex_request('rc_theme', 'string');
$rc_tabindex             = rex_request('rc_tabindex', 'string');
$rc_custom_theme_widget  = rex_request('rc_custom_theme_widget', 'string');

// UPDATE/SAVE SETTINGS
////////////////////////////////////////////////////////////////////////////////
if ($func == $myfunc)
{
  $REX['ADDON']['xform_plugins']['recaptcha']['rc_label']               = $rc_label;
  $REX['ADDON']['xform_plugins']['recaptcha']['rc_warning']             = $rc_warning;
  $REX['ADDON']['xform_plugins']['recaptcha']['rc_pubkey']              = $rc_pubkey;
  $REX['ADDON']['xform_plugins']['recaptcha']['rc_privkey']             = $rc_privkey;
  $REX['ADDON']['xform_plugins']['recaptcha']['rc_lang']                = $rc_lang;
  $REX['ADDON']['xform_plugins']['recaptcha']['rc_theme']               = $rc_theme;
  $REX['ADDON']['xform_plugins']['recaptcha']['rc_tabindex']            = $rc_tabindex;
  $REX['ADDON']['xform_plugins']['recaptcha']['rc_custom_theme_widget'] = $rc_custom_theme_widget;

  $content = '
$REX[\'ADDON\'][\'xform_plugins\'][\'recaptcha\'][\'rc_label\']               = \''.$rc_label.'\';
$REX[\'ADDON\'][\'xform_plugins\'][\'recaptcha\'][\'rc_warning\']             = \''.$rc_warning.'\';
$REX[\'ADDON\'][\'xform_plugins\'][\'recaptcha\'][\'rc_pubkey\']              = \''.$rc_pubkey.'\';
$REX[\'ADDON\'][\'xform_plugins\'][\'recaptcha\'][\'rc_privkey\']             = \''.$rc_privkey.'\';
$REX[\'ADDON\'][\'xform_plugins\'][\'recaptcha\'][\'rc_lang\']                = \''.$rc_lang.'\';
$REX[\'ADDON\'][\'xform_plugins\'][\'recaptcha\'][\'rc_theme\']               = \''.$rc_theme.'\';
$REX[\'ADDON\'][\'xform_plugins\'][\'recaptcha\'][\'rc_tabindex\']            = \''.$rc_tabindex.'\';
$REX[\'ADDON\'][\'xform_plugins\'][\'recaptcha\'][\'rc_custom_theme_widget\'] = \''.$rc_custom_theme_widget.'\';
';

  $file = dirname(__FILE__).'/config.recaptcha.inc.php';
  rex_replace_dynamic_contents($file, $content);

}

    $RC = $REX['ADDON']['xform_plugins'];
echo '
<div class="rex-form">

  <form action="index.php" method="get">
    <input type="hidden" name="page" value="'.$page.'" />
    <input type="hidden" name="subpage" value="'.$subpage.'" />
    <input type="hidden" name="func" value="'.$myfunc.'" />
    <input type="hidden" name="rex_info" value="Recaptcha Konfiguration wurde aktualisiert" />
    <input type="hidden" name="open_plugin" value="recaptcha" />
    <input type="hidden" name="rc_tabindex" value="" />
    <input type="hidden" name="rc_custom_theme_widget" value="" />

    <fieldset class="rex-form-col-1">
      <legend>Einstellungen</legend>
      <div class="rex-form-wrapper">

      <div class="rex-form-row">
        <p class="rex-form-text">
          <label for="rc_label">label:</label>
          <input type="text" id="rc_label" name="rc_label" value="'.stripslashes($REX['ADDON']['xform_plugins']['recaptcha']['rc_label']).'" />
        </p>
      </div>

      <div class="rex-form-row">
        <p class="rex-form-text">
          <label for="rc_warning">warning_text:</label>
          <input type="text" id="rc_warning" name="rc_warning" value="'.stripslashes($REX['ADDON']['xform_plugins']['recaptcha']['rc_warning']).'" />
      </div>

      <div class="rex-form-row">
        <p class="rex-form-text">
          <label for="rc_pubkey">public key:</label>
          <input type="text" id="rc_pubkey" name="rc_pubkey" value="'.stripslashes($REX['ADDON']['xform_plugins']['recaptcha']['rc_pubkey']).'" />
        </p>
      </div>

      <div class="rex-form-row">
        <p class="rex-form-text">
          <label for="rc_privkey">private key:</label>
          <input type="text" id="rc_privkey" name="rc_privkey" value="'.stripslashes($REX['ADDON']['xform_plugins']['recaptcha']['rc_privkey']).'" />
        </p>
      </div>

      <div class="rex-form-row">
        <p class="rex-form-text">
          <label for="rc_lang">lang:</label>
          <input type="text" id="rc_lang" name="rc_lang" value="'.stripslashes($REX['ADDON']['xform_plugins']['recaptcha']['rc_lang']).'" />
        </p>
      </div>

      <div class="rex-form-row">
        <p class="rex-form-text">
          <label for="rc_theme">theme:</label>
          <input type="text" id="rc_theme" name="rc_theme" value="'.stripslashes($REX['ADDON']['xform_plugins']['recaptcha']['rc_theme']).'" />
        </p>
      </div>

      <div class="rex-form-row rex-form-element-v2">
        <p class="rex-form-submit">
          <input class="rex-form-submit" type="submit" id="sendit" name="sendit" value="Recaptcha Einstellungen speichern" />
        </p>
      </div>


      </div>
    </fieldset>
  </form>
  </div>';
