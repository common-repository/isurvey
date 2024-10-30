<div class="wrap">
  <h2>Surveys</h2>
  <?php if ($message): ?>
  <div id="message" class="updated fade">
    <p>
      <?php echo $message; ?>
    </p>
  </div>
  <?php endif; ?>
  <?php if ($surveys): ?>
  <table class="widefat fixed" cellspacing="0">
    <thead>
      <tr>
        <th class="manage-column" style="width: 50px;">ID</th>
        <th class="manage-column">Title</th>
        <th class="manage-column"></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th class="manage-column">ID</th>
        <th class="manage-column">Title</th>
        <th class="manage-column"></th>
      </tr>
    </tfoot>
    <tbody>
      <?php foreach ($surveys as $survey): ?>
      <tr>
        <td><?php echo $survey->ID; ?></td>
        <td><?php echo htmlspecialchars($survey->title); ?></td>
        <td>
          <a href="<?php survey_summary_link($survey->ID);  ?>">Summary</a> |
          <a href="<?php survey_results_link($survey->ID); ?>">Results</a> |
          <a href="<?php survey_questions_link($survey->ID);  ?>">Edit Questions</a> |
          <a href="<?php survey_edit_link($survey->ID); ?>">Edit</a> |
          <a href="<?php survey_delete_link($survey->ID); ?>" class="delete-brand" style="color: #BC0B0B;">Delete</a>
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

<script type="text/javascript">
<!--
(function($) {
$(function() {
    $(".delete-brand").click(function(e) {
        if (!window.confirm("Are you sure you want to delete this survey?")) {
            e.preventDefault();
        }
    });  
});
})(jQuery);
-->
</script>

