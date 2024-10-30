<div class="wrap">
  <h2>Add / edit survey</h2>
  <?php if ($message): ?>
  <div id="message" class="updated fade">
    <p>
      <?php echo htmlspecialchars($message); ?>
    </p>
  </div>
  <?php endif; ?> 
  <?php if ($error): ?>
  <div id="message" class="error fade">
    <p>
      <strong>
        <?php echo htmlspecialchars($error); ?>
      </strong>
    </p>
  </div>
  <?php endif; ?> 

  <form action="admin.php" method="POST" enctype="multipart/form-data"> 
    <input type="hidden" name="action" value="survey_save_survey" />
    <input type="hidden" name="ID" value="<?php echo $survey->ID; ?>" />
    <table class="form-table">
      <tr>
        <th scope="row">Title:</th>
        <td>
          <input type="text" name="title" class="regular-text"   value="<?php echo esc_attr($survey->title); ?>" />
        </td>
      </tr>
      <tr>
        <th scope="row">Intro message:</th>
        <td>
          <textarea name="intro_message" rows="10" cols="40"><?php echo esc_attr($survey->intro_message); ?></textarea>
        </td>
      </tr>
      <tr>
        <th scope="row">"Thank you" page message:</th>
        <td>
          <textarea name="thankyou_message" rows="10" cols="40"><?php echo esc_attr($survey->thankyou_message); ?></textarea>
        </td>
      </tr>
      <tr>
        <th scope="row">Allow anonymous participation?</th>
        <td>
          <input type="checkbox" name="allow_anonymous" <?php checked($survey->allow_anonymous, 1);  ?> />
          <label>Yes</label>
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" class="button-primary" value="Save changes" />
    </p>
  </form>
</div>

