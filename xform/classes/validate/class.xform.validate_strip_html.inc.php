<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.2
* @version Klasse: 0.1
*
* Löscht/ersetzt HTML aus user Eingabe..
*
* $Id: class.xform.validate_strip_html.inc.php 79 2011-01-22 11:36:11Z jeffe $:
*/

class rex_xform_validate_strip_html extends rex_xform_validate_abstract
{

  function enterObject(&$warning, $send, &$warning_messages)
  {
    if($send=="1")
    {
      $replacement = $this->elements[3];

      foreach($this->obj_array as $Object)
      {
        $input = $Object->getValue();
        $sanitized = preg_replace('/<.*>.*<\/.*>/i', $replacement, $input);
        $Object->setValue($sanitized);
      }
    }
  }

  function getDescription()
  {
    return '<strong>strip_html</strong> : <em>entfernt HTML aus Formulareingabe (ersetzt optional gegen vordefinierten Text) -></em><br />'.PHP_EOL
    .'<code class="xform-form-code">validate|strip_html|label|replace_string</code> (optional)';
  }

}
