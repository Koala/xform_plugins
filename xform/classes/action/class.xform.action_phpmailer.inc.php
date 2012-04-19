<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package XForm 2.8
* @version Addon: 0.2
* @version Klasse: 0.1
*
* $Id: class.xform.action_phpmailer.inc.php 139 2011-11-20 10:15:22Z jeffe $:
*/

class rex_xform_action_phpmailer extends rex_xform_action_abstract
{

  function execute()
  {
    global $REX;

    $mail_from    = $this->getElement(2);
    $mail_to      = $this->getElement(3);
    $mail_subject = $this->getElement(4);
    $mail_body    = $this->getElement(5);


    // RESOLVE ADRESSING
    ////////////////////////////////////////////////////////////////////////////
    $mail_from    = $this->resolveMailFrom($mail_from);
    $mail_to      = $this->resolveMailTo($mail_to);


    // MAIL BODY TEMPLATE FROM ARTICLE OR SLICE
    ////////////////////////////////////////////////////////////////////////////
    if(preg_match('#article\(([0-9]+),([0-9]+)\)#',$mail_body))
    {
      $mail_body = $this->getArticle($mail_body);
    }
    elseif(preg_match('#slice\(([0-9]+)\)#',$mail_body))
    {
      $mail_body = $this->getSlice($mail_body);
    }

    // REPLACE VARS IN TEMPLATE
    ////////////////////////////////////////////////////////////////////////////
    foreach ($this->params['value_pool']['email'] as $search => $replace)
    {
      $mail_body = str_replace('###'. $search .'###', $replace, $mail_body);
      $mail_body = str_replace('+++'. $search .'+++', urlencode($replace), $mail_body);
    }

    // MAIN
    ////////////////////////////////////////////////////////////////////////////
    $mail = new PHPMailer(true);

    // GET SOME SETTINGS FROM ADDON
    $settings       = new rex_mailer();
    $mail->WordWrap = $settings->WordWrap;
    $mail->CharSet  = $settings->CharSet;
    /*$mail->WordWrap = 80;
    $mail->CharSet  = 'utf-8';*/

    // RECIPIENTS
    foreach($mail_to as $k => $v)
    {
      switch($v['mode'])
      {
        case 'cc':
          $mail->AddCC($v['email'], $v['email']);
          break;
        case 'bcc':
          $mail->AddBCC($v['email'], $v['email']);
          break;
        default:
          $mail->AddAddress($v['email'], $v['email']);
      }
    }

    $mail->FromName = $mail_from;
    $mail->From     = $mail_from;
    $mail->Subject  = $mail_subject;
    $mail->Body     = nl2br($mail_body);
    $mail->AltBody  = strip_tags($mail_body);

    //ATTACHMENTS
    if(isset($this->params['attachments']) && is_array($this->params['attachments']) && count($this->params['attachments'])>0)
    {
      foreach ($this->params['attachments'] as $el => $v)
      {
        if($v['size']>0 || $v['size']!='')
        {
          if(is_uploaded_file($v['tmp_name']))
          {
            $mail->AddAttachment($v['tmp_name'],$v['name'],'base64',$v['type']);
          }
        }
      }
    }

    $mail->Send();

    $mail->ClearAddresses();
    $mail->ClearAttachments();
  }


  function getDescription()
  {
    return '<strong>phpmailer</strong> : <em>Erweiterung der normalen "email" Klasse mit zus&auml;tzlichen features: mehrere Empf&auml;nger und Adressierungs-Typen (to,cc,bcc), Mailbody Bezug aus Article/Slice, Verarbeitung von Attachments</em><br />'.PHP_EOL
    .'Gewohnter Aufruf:<br />'.PHP_EOL
    .'<code class="xform-form-code">action|phpmailer|from@email.de|to@email.de|Mailsubject|Mailbody###name###"</code><br />'
    .'Mehrere Empf&auml;nger:<br />'.PHP_EOL
    .'<code class="xform-form-code">action|phpmailer|from@email.de|to:a@bc.de,cc:a@bc.de,bcc:a@bc.de|Mailsubject|Mailbody###name###)"</code><br />'
    .'<em>Mailbody aus Slice beziehen -></em><br />'.PHP_EOL
    .'<code class="xform-form-code">action|phpmailer|from@email.de|to@email.de|Mailsubject|slice(123)"</code><br />'
    .'Mailbody aus Article beziehen:<br />'.PHP_EOL
    .'<code class="xform-form-code">action|phpmailer|from@email.de|to@email.de|Mailsubject|article(8,0)"</code><br />'
    .'Attachments:'.PHP_EOL
    .'Daten eines uploads m&uuml;ssen von der jeiligen upload-Klasse aus $_FILES in xform_object->params["attachments"] als array hinterlegt worden sein:</em><br/>'.PHP_EOL
    .'<pre>"size"     => $_FILES["size"],
"name"     => $_FILES["name"],
"type"     => $_FILES["type"],
"tmp_name" => $_FILES["tmp_name"],
"error"    => $_FILES["error"],'.PHP_EOL
    ;
  }


  function retrieve_slice($m)
  {
    $slice = OOArticleSlice::getArticleSliceById($m[1]);
    if(is_object($slice))
    {
      return $slice->getSlice();
    }
    else
    {
      return 'Slice Id['.$m[1].'] not found.';
    }
  }


  function retrieve_article($m)
  {
    $article = OOArticle::getArticleById($m[1],$m[2]);
    if(is_object($article))
    {
      return $article->getArticle();
    }
    else
    {
      return'Article Id['.$m[1].'] not found.';
    }
  }


  function getSlice($str)
  {
    return preg_replace_callback('#slice\(([0-9]+)\).*#',array('rex_xform_action_phpmailer', 'retrieve_slice'),$str);
  }


  function getArticle($str)
  {
    return preg_replace_callback('#article\(([0-9]+),([0-9]+)\).*#',array('rex_xform_action_phpmailer', 'retrieve_article'),$str);
  }


  function resolveMailTo($mail_to)
  {
    $recipients = array();
    if(strpos($mail_to,','))
    {
      // ARRAY OF RECIPIENTS
      $tmp = explode(',',$mail_to);
      foreach($tmp as $recipient)
      {
        $r = explode(':',trim($recipient));

        if(preg_match('/###.*###/',$r[1]))
        {
          $r[1] = self::resolvePlaceholder($r[1]);
        }

        $recipients[] = array('mode' => $r[0], 'email' => $r[1]);
      }
    }
    else
    {
      // SINGLE RECIPIENT
      if(strpos($mail_to,':'))
      {
        $mail_to = explode(':',$mail_to);

        if(preg_match('/###.*###/',$mail_to[1]))
        {
          $mail_to[1] = self::resolvePlaceholder($mail_to[1]);
        }

        $recipients[] = array('mode' => $mail_to[0], 'email' => $mail_to[1]);
      }
      else
      {
        if(preg_match('/###.*###/',$mail_to))
        {
          $mail_to = self::resolvePlaceholder($mail_to);
        }
        $recipients[] = array('mode' => 'to', 'email' => $mail_to);
      }
    }
    return $recipients;
  }


  function resolveMailFrom($mail_from)
  {
    return preg_match('/###.*###/',$mail_from)
           ? self::resolvePlaceholder($mail_from)
           : $mail_from;
  }


  function resolvePlaceholder($placeholder)
  {
    global $REX;

    $placeholder = trim($placeholder,'###');
    if(isset($this->params['value_pool']['email'][$placeholder]) && $this->params['value_pool']['email'][$placeholder] != '' )
    {
      return $this->params['value_pool']['email'][$placeholder];
    }
    else
    {
      return $REX['ERROR_EMAIL'];
    }
  }

}

?>