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
* Löscht beim Anklicken eines inputs dessen Inhalt
*
* $Id: class.xform.onclick_clear_value.inc.php 69 2011-01-19 01:43:57Z jeffe $:
*/

class rex_xform_onclick_clear_value extends rex_xform_abstract
{

  function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
  {
    $inputs = explode(',',$this->elements[1]);

    if(isset($this->elements[2]))
    {
      switch($this->elements[2])
      {
        case 'auto':
          $script = PHP_EOL.'<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.min.js"></script>'.PHP_EOL;
          break;
        default:
          $script = PHP_EOL.$this->elements[2];
      }
    }
    else
    {
      $script = PHP_EOL;
    }

    $script .= '<script type="text/javascript">'.PHP_EOL;
    foreach($this->obj as $Object)
    {
      
      if(in_array($Object->getName(),$inputs))
     {
      $script .= '
var default'.$Object->getId().' = $("#el_'.$Object->getId().'").val();
$("#el_'.$Object->getId().'").focus(function() {
    $(this).attr("value","");
  });
$("#el_'.$Object->getId().'").blur(function() {
    if($(this).attr("value")=="") {
      $(this).attr("value",default'.$Object->getId().');
    }
  });'.PHP_EOL;
      }
    }
    $script .= '</script>'.PHP_EOL;

    $form_output[] = $script;
  }

  function getDescription()
  {
    return '<strong>onclick_clear_value</strong> : <em>L&ouml;scht beim Anklicken eines inputs dessen (default)Inhalt, und restort ihn wieder, falls keine user Eingabe erfolgt ist (d.h. das Feld leer geblieben ist)-></em><br />'.PHP_EOL
    .'<code class="xform-form-code">onclick_clear_value|label_1,label_2,label_3,..|JQuery-core-include</code> (auto/URL/<i>leer</i>)';
  }

}
