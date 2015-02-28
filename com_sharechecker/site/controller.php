<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class SharecheckerController extends JController {

  public function check() {
    global $mainframe;
    jimport('joomla.utilities.date');
    $code = JRequest::getVar('code');
    $this->params = JComponentHelper::getParams('com_sharechecker');

    if ($code != $this->params->get('secret_code')) $mainframe->close('Invalid code');

    $finder = new SharecheckerFinder();
    $finder->searchForLinks($this->params->get('max_search_for_links'));

    $checker = new SharecheckerChecker();
    $checker->checkLinks($this->params->get('max_check_links'));

    if($this->canNotice()) $this->sendNotice($this->params->get('email_for_notices'));

    $mainframe->close('Done');
  }

  private $statistics;
  private function canNotice() {
    if($this->params->get('send_email') == 0) return false;
    $this->statistics = new SharecheckerStatistics();
    if($this->statistics->numBrokenLinks < $this->params->get('min_broken_links_for_notice')) return false;
    $noticeDate = $this->params->get('last_notice_date');
    if(empty($noticeDate)) return true;

    $date = new JDate();
    $noticeDate = new JDate($this->params->get('last_notice_date'));

    $delta = $date->toUnix() - $noticeDate->toUnix();
    return $delta >= $this->params->get('send_notice_interval') * 3600 * 24;
  }

  private function sendNotice($email) {
    jimport('joomla.utilities.mail');
    $from = 'info@'.$_SERVER['HTTP_HOST'];
    $mailer = JFactory::getMailer();
    $mailer->Sender = $from;
    $mailer->setSender($from, 'System notice');
    $mailer->addRecipient($email);
    $mailer->setSubject('На сайте '.$_SERVER['HTTP_HOST'].' имеются битые ссылки');
    $msg = 'Добрый день. Вы получили это письмо потому что подписаны на уведомления о наличии битых ссылок на сайте '.$_SERVER['HTTP_HOST'].".\r\n";
    $msg .= 'В данный момент на сайте обнаружено '.$this->statistics->numBrokenLinks.' материала(ов) с битыми ссылками.'."\r\n";
    $msg .= '---'."\r\n";
    $msg .= 'Это письмо было отослано автоматически. Не отвечайте на него.';
    $mailer->setBody($msg);
    if ($mailer->Send()) {
      $date = new JDate();
      $this->params->set('last_notice_date', $date->toMySQL());
      $table = JTable::getInstance('component');
      $table->loadByOption('com_sharechecker');
      $table->params = $this->params->toString();
      if (!$table->check()) throw new Exception("Error 1", 1);
      if (!$table->store()) throw new Exception("Error 2", 2);
    }
  }

}