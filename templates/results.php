<?php require_once('admin-header.php'); ?>
<div class="wrap">
  <h2>Results for "<?php echo $survey->title;   ?>"</h2>
  <?php if ($answers): ?>
  <table class="widefat fixed" cellspacing="0">
    <thead>
      <tr>
        <th class="manage-column"></th>
        <?php foreach ($questions as $q): ?>
        <th class="manage-column"><?php echo $q->dashboard_label; ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th class="manage-column"></th>
        <?php foreach ($questions as $q): ?>
        <th class="manage-column"><?php echo $q->dashboard_label; ?></th>
        <?php endforeach; ?>
      </tr>
    </tfoot>
    <tbody>
    <?php foreach ($participants as $p): ?>
    <tr>
      <td><?php echo survey_get_participant_name($p, $answers);  ?></td>
      <?php foreach ($questions as $q): ?>
      <td><?php echo survey_get_participant_answer($p, $q->ID, $answers);  ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <div class="tablenav">
    <div class="tablenav-pages">
    <?php echo $page_links; ?>
    </div>
  </div> 
  <?php else: ?>
  <p>
    No results yet.
  </p>
  <?php endif; ?> 
</div>

<?php include('admin-footer.php'); ?>
