<?php include(ABSPATH.'wp-admin/admin-header.php');  ?>
<div class="wrap">
  <h2>Questions for "<?php echo $survey->title; ?>" survey</h2>
  <?php if ($message): ?>
  <div id="message" class="updated fade">
    <p>
      <?php echo $message; ?>
    </p>
  </div>
  <?php endif; ?>
  <?php if ($questions): ?>
  <table class="widefat fixed" cellspacing="0">
    <thead>
      <tr>
        <th class="manage-column">Question</th>
        <th class="manage-column">Question type</th>
        <th class="manage-column">Dashboard label</th>
        <th class="manage-column">Opt-out label</th>
        <th class="manage-column">Left label</th>
        <th class="manage-column">Right label</th>
        <th class="manage-column"></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th class="manage-column">Question</th>
        <th class="manage-column">Question type</th>
        <th class="manage-column">Dashboard label</th>
        <th class="manage-column">Opt-out label</th>
        <th class="manage-column">Left label</th>
        <th class="manage-column">Right label</th>
        <th class="manage-column"></th>
      </tr>
    </tfoot>
    <tbody>
      <?php foreach ($questions as $q): ?>
      <tr>
        <td><?php echo $q->question; ?></td>
        <td><?php echo survey_humanize_question_type($q->question_type);  ?></td>
        <td><?php echo $q->dashboard_label; ?></td>
        <td><?php echo $q->opt_out_label;  ?></td>
        <td><?php echo $q->left_label; ?></td>
        <td><?php echo $q->right_label; ?></td>
        <td>
          <a href="<?php survey_question_edit_link($q); ?>">Edit</a> |
          <a href="<?php survey_question_delete_link($q); ?>" class="delete-brand" style="color: #BC0B0B;">Delete</a>
        </td>
      </tr> 
      <?php endforeach; ?>                    
    </tbody>
  </table>
  <?php endif; ?>
  <form action="admin.php" method="POST" enctype="multipart/form-data"> 
    <input type="hidden" name="action" value="survey_save_question" />
    <input type="hidden" name="ID" value="<?php echo $question->ID; ?>" />
    <input type="hidden" name="survey_id" value="<?php echo $survey->ID;  ?>" />
    <table class="form-table">
      <tr>
        <th scope="row">Question:</th>
        <td>
          <input type="text" name="question" class="regular-text"   value="<?php echo esc_attr($question->question); ?>" />
        </td>
      </tr>
      <tr>
        <th scope="row">Question type:</th>
        <td> 
          <?php survey_question_type_dropdown($question->question_type); ?>
        </td>
      </tr>
      <tr class="scored">
        <th scope="row">Dashboard label:</th>
        <td>
          <input type="text" class="regular-text" name="dashboard_label" value="<?php echo esc_attr($question->dashboard_label);  ?>" />
        </td>
      </tr>
      <tr class="scored">
        <th scope="row">Opt-out label:</th>
        <td>
          <input type="text" class="regular-text" name="opt_out_label" value="<?php echo esc_attr($question->opt_out_label);  ?>" />
        </td>
      </tr>
      <tr class="scored">
        <th scope="row">Left label:</th>
        <td>
          <input type="text" class="regular-text" name="left_label" value="<?php echo esc_attr($question->left_label);  ?>" />
        </td>
      </tr>
      <tr class="scored">
        <th scope="row">Right label:</th>
        <td>
          <input type="text" class="regular-text" name="right_label" value="<?php echo esc_attr($question->right_label);  ?>" />
        </td>
      </tr>

    </table>
    <p class="submit">
      <input type="submit" class="button-primary" value="Save changes" />
    </p>
  </form>

</div>

<script type="text/javascript">
<!--
(function($) {
$(function() {
    $(".delete-brand").click(function(e) {
        if (!window.confirm("Are you sure you want to delete this question?")) {
            e.preventDefault();
        }
    });  

    function questionTypeChanged() {
        var type = $("select[name='question_type']").val();
        if ("free" == type) {
            $(".scored").hide();
        }
        else {
            $(".scored").show();
        }  
    };
    $("select[name='question_type']").change(questionTypeChanged);
    questionTypeChanged();
});
})(jQuery);
-->
</script>
<?php include(ABSPATH.'/wp-admin/admin-footer.php');  ?>
