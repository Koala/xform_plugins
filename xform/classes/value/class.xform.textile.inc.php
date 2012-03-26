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
* $Id: class.xform.textile.inc.php 60 2011-01-19 00:05:14Z jeffe $:
*/

class rex_xform_textile extends rex_xform_abstract
{

  function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
  {
    $textile ='';
    foreach($this->elements as $key => $val)
    {
      if($key > 0)
      {
        $textile = $textile.$val.'|';
      }
    }
    $textile = rtrim($textile,'|');
    $textile = htmlspecialchars_decode($textile);
    $textile = str_replace('<n>',"\n",$textile);
    $textile = rex_a79_textile($textile);
    $form_output[] = '<div class="formtext" style="clear:left;">'.$textile.'</div>';
  }

  function getDescription()
  {
    return '<strong>textile</strong> : <em>Erm&ouml;glicht Textile markup innerhalb eines Formulars. linebreaks werden per &lt;n&gt; notiert -></em><br />'.PHP_EOL
    .'<code class="xform-form-code">textile|h2. Textile markup&lt;n&gt;&lt;n&gt;innerhalb eines&lt;n&gt;*XFORM* Formulars..</code>'
    ;
  }
}
