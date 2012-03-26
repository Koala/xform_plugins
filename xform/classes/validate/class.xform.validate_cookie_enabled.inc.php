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
* Vergleicht die User-Eingaben zweier Datums-Felder
* Minimaler und maximaler Abstand definierbar
* Fehlermeldung je Regel
*
* $Id: class.xform.validate_cookie_enabled.inc.php 132 2011-07-01 14:11:36Z jeffe $:
*/

class rex_xform_validate_cookie_enabled extends rex_xform_validate_abstract
{
  function enterObject(&$warning, $send, &$warning_messages)
  {
    if($send=="1")
    {
      foreach($this->obj_array as $o)
      {
        if ($o->elements[1]==$this->elements[2])
        {
        $id_1 = $o->getId();
        $start_date = $o->getValue();
        $start_date = strtotime($start_date['day'].'.'.$start_date['month'].'.'.$start_date['year']);
        }

        if ($o->elements[1]==$this->elements[3])
        {
        $id_2 = $o->getId();
        $end_date = $o->getValue();
        $end_date = strtotime($end_date['day'].'.'.$end_date['month'].'.'.$end_date['year']);
        }
      }

      $date_diff = $end_date - $start_date;

      if ($date_diff < $this->elements[4])
      {
        $warning['el_' . $id_1] = $this->params['error_class'];
        $warning['el_' . $id_2] = $this->params['error_class'];
        $warning_messages[] = $this->elements[6];
      }

      if ($this->elements[5] !='')
      {
        if ($date_diff > $this->elements[5])
        {
          $warning['el_' . $id_1] = $this->params['error_class'];
          $warning['el_' . $id_2] = $this->params['error_class'];
          $warning_messages[] = $this->elements[7];
        }
      }

    }
  }

  function getDescription()
  {
    return
    '<strong>date_diff</strong> : <em>pr&uuml;ft Abstand zwischen zwei Datumseingaben (min_diff/max_diff: Angaben in Sekunden) -></em><br />'.PHP_EOL
   .'<code class="xform-form-code">validate|date_diff|start_date|end_date|min_diff|max_diff|min_warn_message|max_warn_message</code>';
    //                                                                      0   |     1   |     2    |   3    |   4    |   5    |       6        |       7
  }
}
