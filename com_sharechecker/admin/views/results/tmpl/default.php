<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'sharechecker.css','administrator/components/com_sharechecker/assets/');
JHTML::script('admin.js', 'administrator/components/com_sharechecker/assets/');
$results = &$this->results;
$type = &$this->type;
?>
<form action="index.php" method="get" name="adminForm">
<h2>Результаты проверки <?php echo ($type == 'good' ? 'рабочих' : 'битых')?> ссылок</h2>
<?php if (count($results) > 0) { ?>

  <table class="adminlist">
  <thead>
    <th width="10"><?php echo JText::_('id'); ?></th>
    <th width="200" class="title"><?php echo JText::_('title'); ?></th>
    <th class="title"><?php echo JText::_('links'); ?></th>
    <th class="title"><?php echo JText::_('hits'); ?></th>
    <th class="title"><?php echo JText::_('rating'); ?></th>
    <th class="title"><?php echo JText::_('days_modified'); ?></th>
  </thead>
  <tbody>
  <?php foreach($results as $result) { ?>
  <tr>
    <td><?php echo $result->id; ?></td>
    <td><?php echo '<a href="'.(ShareCheckerUri::editArticle($result->id)).'" target="_blank">'.$result->title.'</a>'; ?></td>
    <td><?php echo ($result->num_shared_links > 1 ? $result->num_shared_links.' ссылок(ки)<br/>' : null).implode(',<br />', $result->uri); ?></td>
    <td><?php echo $result->hits; ?></td>
    <td><?php echo $result->rating; ?></td>
    <td><?php echo ($result->days_modified >0 ? $result->days_modified : null); ?></td>
  </tr>
  <?php } ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
</table>

<?php } else { ?>
<p>Ссылки еще не найдены. Запустите их поиск вручную или дождитесь проверки по расписанию.</p>
<?php } ?>

<input type="hidden" name="view" value="results" />
<input type="hidden" name="option" value="com_sharechecker" />
<input type="hidden" name="type" value="<?=addslashes($_REQUEST['type']);?>" />

</form>