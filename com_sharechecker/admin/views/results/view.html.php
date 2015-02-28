<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SharecheckerViewResults extends JView {

  function display($tpl = null) {
    $model = &$this->getModel();
    JToolBarHelper::custom('resetall', 'delete.png', 'delete_f2.png', 'Reset All', false);
    JToolBarHelper::custom('resetbroken', 'delete.png', 'delete_f2.png', 'Reset Broken', false);
    JToolBarHelper::preferences( 'com_sharechecker', 550);
    $this->assignRef('LoadResults', $model->LoadResults());
    $this->assignRef('results', $model->results);
    $this->assignRef('type', $model->type);
    $this->assignRef('pagination', $model->getPagination());

    parent::display($tpl);
  }

}