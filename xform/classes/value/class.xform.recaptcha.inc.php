<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.2
* @version Klasse: 0.2
*
* $Id: class.xform.recaptcha.inc.php 74 2011-01-22 01:31:00Z jeffe $:
*/

/**
* ---------------------------------------------------------------------------
* RECAPTCHA LIB: http://recaptcha.net/plugins/php/
* RECAPTCHA KEYS: https://admin.recaptcha.net/accounts/signup/
* ---------------------------------------------------------------------------
* AUFRUF:
* recaptcha| (weitere settings aus Addon bezogen)
* recaptcha|label|warning_text|public-key|private-key|lang|theme|tabindex|custom_theme_widget
* ---------------------------------------------------------------------------
* TODOS:
* * lang wahlweise aus rex_lang oder moduleingabe (auto/wert)
* ---------------------------------------------------------------------------
*/

class rex_xform_recaptcha extends rex_xform_abstract
{
  function enterObject(&$email_elements,&$sql_elements,&$erroring,&$form_output,$send = 0)
  {
    global $REX;
    require_once($REX['INCLUDE_PATH'].'/addons/xform/plugins/xform_plugins/libs/recaptcha/recaptcha.net/recaptchalib.php');

    // RESET XFORM ERROR
    ////////////////////////////////////////////////////////////////////////////
    $this->params['error_class'] = '';

    // DEFAULTS
    ////////////////////////////////////////////////////////////////////////////
    $RC = false;
    if(isset($REX['ADDON']['plugins']['xform']['install']['xform_plugins']) &&
       $REX['ADDON']['plugins']['xform']['install']['xform_plugins']==1 &&
       isset($REX['ADDON']['xform_plugins']['recaptcha']))
    {
      $RC = $REX['ADDON']['xform_plugins']['recaptcha'];
    }

    if(!$RC)
    {
      $RC = array(
        'rc_label'               => 'reCaptcha',
        'rc_warning'             => 'Die reCaptcha Eingabe war nicht korrekt',
        'rc_pubkey'              => '',
        'rc_privkey'             => '',
        'rc_lang'                => 'de',
        'rc_theme'               => 'white',
        'rc_tabindex'            => '',
        'rc_custom_theme_widget' => ''
        );
    }
    $RCindex = array_keys($RC);

    // OVERRIDE DEFAULTS BY FORM DEFINITION
    ////////////////////////////////////////////////////////////////////////////
    for($i = 1; $i < count($this->elements); $i++)
    {
      if($this->elements[$i] != '')
      {
        $RC[$RCindex[$i-1]] = $this->elements[$i];
      }
    }

    // GET RECAPTCHA
    ////////////////////////////////////////////////////////////////////////////
    if (rex_request('recaptcha','string') == "show")
    {
      ob_end_clean();
      ob_end_clean();
      $server = &new recaptcha ();
      $server->handle_request ();
      exit;
    }

    // GET / CHECK RECAPTCHA
    ////////////////////////////////////////////////////////////////////////////
    $RC_html = recaptcha_get_html($RC['rc_pubkey']);
    $recaptcha_challenge_field = rex_request('recaptcha_challenge_field','string');
    $recaptcha_response_field  = rex_request('recaptcha_response_field', 'string');

    if ( $send == 1)
    {
      $RC_response = recaptcha_check_answer($RC['rc_privkey'],$_SERVER["REMOTE_ADDR"],$recaptcha_challenge_field,$recaptcha_response_field);

      if (!$RC_response->is_valid)
      {
        switch ($RC_response->error)
        {
          case 'incorrect-captcha-sol':
            $this->params['warning'][] = $RC['rc_warning'];
            $this->params['warning_messages'][] = $RC['rc_warning'];
            $this->params['error_class'] = 'form_warning';
            break;
        }
      }
    }

    // RECAPTCHA OPTIONS
    ////////////////////////////////////////////////////////////////////////////
    $RC_control ='';
    if ($RC['rc_lang'] != '')
    {
      $RC_control = 'lang:"'.$RC['rc_lang'].'",';
    }
    if ($RC['rc_theme'] != '')
    {
      $RC_control .= 'theme:"'.$RC['rc_theme'].'",';
    }
    if ($RC['rc_tabindex'] != '')
    {
      $RC_control .= 'tabindex:"'.$RC['rc_tabindex'].'",';
    }
    if ($RC['rc_custom_theme_widget'] != '')
    {
      $RC_control .= 'custom_theme_widget:"'.$RC['rc_custom_theme_widget'].'",';
    }

    $RC_control = rtrim($RC_control,',');

    if ($RC_control != '')
    {
      $RC_control ='
      <script type="text/javascript">
          var RecaptchaOptions = {
          '.$RC_control.'
          };
        </script>
        ';
    }

    // FORM OUT
    ////////////////////////////////////////////////////////////////////////////
    $form_output[] = '
      <div class="recaptcha">
        <label class="textarea '.$this->params['error_class'].'" for="recaptcha_widget_div">'.$RC['rc_label'].'</label>
        '.$RC_control.$RC_html.'
      </div>';
  }

  function getDescription()
  {
    $plugin_link = '';
    if(rex_request('subpage','string') != 'xform_plugins')
    {
      $plugin_link = ': <a href="index.php?page=xform&subpage=plugins&chapter=&open_plugin=recaptcha" target="xform_backend">XForm > Plugins > Value > Recaptcha</a>';
    }
    
    return '<strong>recaptcha</strong> : <em>barrierefreies Captcha von reCaptcha.net -></em><br />'.PHP_EOL
          .'<code class="xform-form-code">recaptcha|label|warning_text|<a href="https://admin.recaptcha.net/accounts/signup/" target="recaptchakeys">public-key|private-key</a>|<a href="http://recaptcha.net/apidocs/captcha/client.html#customization" target="recaptchacustom">lang|theme|tabindex|custom_theme_widget</a></code><br />'.PHP_EOL
          .'<code class="xform-form-code">recaptcha|</code> (Settings aus den Default Einstellungen bezogen'.$plugin_link.')';
  }
}
