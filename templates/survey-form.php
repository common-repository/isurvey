<p class="survey-intro-message"><?php echo $survey->intro_message;  ?></p>
<form class="survey-form" action="/wp-admin/admin-ajax.php" method="POST">
  <input type="hidden" name="action" value="survey_save_answers" />
  <input type="hidden" name="survey_id" value="<?php echo $survey->ID;  ?>" />
  <input type="hidden" name="re" value="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" />
  <table>
    <?php foreach ($questions as $q): ?>
      <tr>
        <td class="survey-question"><?php echo $q->question; ?></td>
        <td class="opt-out">
          <?php if ($q->opt_out_label && ('scored' == $q->question_type)): ?>
            <input type="radio" name="questions[<?php echo $q->ID; ?>]" value="0" />
            <label class="opt-out-label"><?php echo htmlspecialchars($q->opt_out_label); ?></label>
          <?php endif; ?>
        </td>
        <td>
          <?php if ('free' == $q->question_type): ?>
            <textarea name="questions[<?php echo $q->ID; ?>]" rows="10" cols="40"></textarea>
            <label class="sybmol-counter">55</label>
          <?php else: ?>
            <div class="radios-wrapper">
            <label class="left-label">1</label>
            <label class="right-label">10</label>
            <div class="radios">
            <?php for ($i = 1; $i <= 10; ++$i): ?>
              <input type="radio" name="questions[<?php echo $q->ID; ?>]" value="<?php echo $i; ?>" />
            <?php endfor; ?>
            </div>
            <label class="left-label"><?php echo htmlspecialchars($q->left_label); ?></label>
            <label class="right-label"><?php echo htmlspecialchars($q->right_label); ?></label>
            </div>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p class="submit">
    <input type="submit" value="Save" disabled="true" />
  </p>
</form>

<style type="text/css">
.survey-question {
    width: 400px;
}
.survey-form label {
    font-size: 10px;
    font-weight: normal;
}
.survey-form .left-label {
    float: left;
    display: inline-block;
    clear: left;
}
.survey-form .right-label {
    float: right;
    display: inline-block;
    clear: right;
}
.survey-form .radios {
    float: left;
    clear: both;
    display: inline-block;
}

.survey-form .opt-out {
    width: 125px;
    text-align: center;
}

.survey-form textarea {
    padding: 2px 3px;
}

.survey-form .radios-wrapper {
    width: 220px;
}

.radios-wrapper input {
    margin-right: 5px;
}


</style>

<script type="text/javascript">
<!--
(function($) {
function enableOrDisable() {
    var $radios = $(".survey-form input[type='radio']");
    var allAnswered = true;
    $radios.each(function() {
        if (!allAnswered) return;
        var $this = $(this);
        var thisName = $this.attr("name");
        var $checkedRadio  = $("input[name='" + thisName + "']:checked");
        if (!$checkedRadio.length) {
            allAnswered = false;
        }
    });

    if (allAnswered) {
        var $textareas = $(".survey-form textarea");
        $textareas.each(function() {
          if (!allAnswered) return;
          var $this = $(this);
          if ("" == $.trim($this.val())) {
              allAnswered = false;
          }
          });
    }

    if (allAnswered) {
        $(".survey-form .submit input").attr("disabled", false);
    }
    else {
        $(".survey-form .submit input").attr("disabled", true);
    }
};
$(function() {
$(".survey-form textarea").keyup(function() {
  var $this = $(this);
  var symLeft = 55 - $this.val().length;
  $this.next().text(symLeft);
  });
  $(".survey-form textarea").keyup(enableOrDisable);
  $(".survey-form input[type='radio']").click(enableOrDisable);
  });
 })(jQuery);
-->
</script>
