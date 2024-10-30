<?php require_once('admin-header.php'); ?>
<div class="wrap">
  <h2>Summary for "<?php echo $survey->title;   ?>"</h2>
  <?php if ($questions): ?>
  <table class="widefat fixed" cellspacing="0">
    <thead>
      <tr>
        <th class="manage-column">Dimension</th>
        <th class="manage-column">Participation</th>
        <th class="manage-column">Rating</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th class="manage-column">Dimension</th>
        <th class="manage-column">Participation</th>
        <th class="manage-column">Rating</th>
      </tr>
    </tfoot>
    <tbody>
      <?php foreach ($questions as $q): ?>
      <tr>
        <td><?php echo $q->dashboard_label; ?></td>
        <td><?php echo $q->participation; ?>%</td>
        <td>
          <?php echo $q->rating; ?>%
        </td>
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
    No surveys yet.
  </p>
  <?php endif; ?> 
</div>

<?php include('admin-footer.php'); ?>
