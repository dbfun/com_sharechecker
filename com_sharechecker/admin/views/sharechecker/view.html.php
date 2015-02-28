<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SharecheckerViewSharechecker extends JView {

  function display($tpl = null) {
    $model = &$this->getModel();
    JToolBarHelper::custom('resetall', 'delete.png', 'delete_f2.png', 'Reset All', false);
    JToolBarHelper::custom('resetbroken', 'delete.png', 'delete_f2.png', 'Reset Broken', false);
    JToolBarHelper::preferences('com_sharechecker', 550);
    $this->assignRef('finder', $model->runFinder());
    $this->assignRef('checker', $model->runChecker());
    $this->assignRef('statistics', $model->getStatistics());
    $this->assignRef('params', $model->getParams());
    parent::display($tpl);
  }

}