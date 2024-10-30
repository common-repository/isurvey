<?php
/*
Plugin Name: Survey
Plugin URI: http://www.marketing-strategy.com.au/research.html
Description: Survey tool for WordPress.
Author: Vlade Balac, Vladimir Kadalashvili
Version: 1.0
Author URI:  http://www.marketing-strategy.com.au/research.html
*/ 

function survey_root() {
    return dirname(__FILE__);
}

function survey_install() {
    global $wpdb;
    $charset = $wpdb->charset;
    if (!$charset) {
        $charset = 'utf8';
    }
    $wpdb->query("
        CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}surveys` (
           `ID` int(11) NOT NULL AUTO_INCREMENT,
           `title` varchar(255) NOT NULL,
           `intro_message` text NOT NULL,
           `thankyou_message` text NOT NULL,
           `allow_anonymous` tinyint(4) NOT NULL DEFAULT '0',
           PRIMARY KEY (`ID`)
        ) ENGINE=MyISAM DEFAULT CHARSET=$charset AUTO_INCREMENT=1
    ");

    $wpdb->query("
        CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}survey_questions` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `survey_id` int(11) NOT NULL,
        `question` varchar(255) NOT NULL,
        `question_type` varchar(255) NOT NULL,
        `dashboard_label` varchar(255) NOT NULL,
        `opt_out_label` varchar(255) NOT NULL,
        `left_label` varchar(255) NOT NULL,
        `right_label` varchar(255) NOT NULL,
         PRIMARY KEY (`ID`)
         ) ENGINE=MyISAM DEFAULT CHARSET=$charset AUTO_INCREMENT=1
    ");

    $wpdb->query("
        CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}survey_answers` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `survey_id` int(11) NOT NULL,
            `question_id` int(11) NOT NULL,
            `user_id` int(11) NOT NULL,
            `score` tinyint(4) NOT NULL,
            `text_answer` varchar(50) NOT NULL,
             PRIMARY KEY (`ID`)
        ) ENGINE=MyISAM DEFAULT CHARSET=$charset AUTO_INCREMENT=1
    ");
}

register_activation_hook(__FILE__, 'survey_install');

function survey_db_shortcuts() {
    global $wpdb;
    $wpdb->surveys = "{$wpdb->prefix}surveys";
    $wpdb->questions = "{$wpdb->prefix}survey_questions";
    $wpdb->answers = "{$wpdb->prefix}survey_answers";
}

add_action('init', 'survey_db_shortcuts');


function survey_get_surveys($args = array()) {
    global $wpdb;
    $defaults = array(
        'ID' => 0,
        'offset' => -1,
        'num' => -1
    );
    $args = wp_parse_args($args, $defaults);
    extract($args, EXTR_SKIP);
    
    $ID = (int) $ID;
    $offset = (int) $offset;
    $num = (int) $num;

    $sql = '';
    $cond = array();
    $limit = '';
    $calc_found_rows = '';
    $orderby_sql = $ID ? '' : 'ORDER BY ID DESC';

    if ($ID) {
        $cond[] = "ID = $ID";
        $limit = "LIMIT 1";
    }

    if (!$limit) {
        if (($offset != -1) && ($num != -1)) {
            $limit = "LIMIT $offset, $num";
            $calc_found_rows = 'SQL_CALC_FOUND_ROWS';
        }
        else if ($num != -1) {
            $limit = "LIMIT $num";
        }
    }

    if (!empty($cond)) {
        $cond =  'WHERE '.implode(' AND ', $cond);
    }
    else {
        $cond = '';
    }

    $sql = "
        SELECT $calc_found_rows  ID, title, intro_message, thankyou_message, allow_anonymous
        FROM $wpdb->surveys
        $cond
        $orderby_sql
        $limit
    ";
    if ($ID) {
        $result = $wpdb->get_row($sql);
    }
    else {
        $result = $wpdb->get_results($sql);
        if ($calc_found_rows) {
            $total = $wpdb->get_var('SELECT FOUND_ROWS()');
            $result = compact('result', 'total');
        }
    }
    return $result;
}

function survey_insert_survey($args = array()) {
    global $wpdb;
    $defaults = array(
        'ID' => 0,
        'title' => '',
        'intro_message' => '',
        'thankyou_message' => '',
        'allow_anonymous' => false
    );
    $args = wp_parse_args($args, $defaults);
    extract($args, EXTR_SKIP);
    $ID = (int) $ID;
    $title = trim($title);
    $intro_message = trim($intro_message);
    $thankyou_message = trim($thankyou_message);
    $allow_anonymous =  $allow_anonymous ? 1 : 0;

    $new_survey = compact('title', 'intro_message', 'thankyou_message', 'allow_anonymous');
    if ($ID) {
        $wpdb->update($wpdb->surveys, $new_survey, array('ID' => $ID));
    }
    else {
        $wpdb->insert($wpdb->surveys, $new_survey);
        $ID = $wpdb->insert_id;
    }
    return $ID;
}

function survey_get_questions($args = array()) {
    global $wpdb;
    $defaults = array(
        'ID' => 0,
        'survey_id' => 0,
        'survey_id_in' => array(),
        'offset' => -1,
        'num' => -1,
        'scored_only' => false
    );
    $args = wp_parse_args($args, $defaults);
    extract($args, EXTR_SKIP);

    $ID = (int) $ID;
    $survey_id = (int) $survey_id;
    $survey_id_in = array_map('intval', $survey_id_in);
    $offset = (int) $offset;
    $num = (int) $num;
    $scored_only = (bool) $scored_only;

    $sql = '';
    $cond = array();
    $limit = '';
    $orderby_sql = $ID ? '' : 'ORDER BY ID ASC';
    $calc_found_rows = '';

    if ($ID) {
        $cond[] = "ID = $ID";
        $limit = "LIMIT 1";
    }

    if ($survey_id) {
        $cond[] = "survey_id = $survey_id";        
    }

    if ($survey_id_in) {
        $survey_id_in_sql = implode(', ', $survey_id_in);
        $cond[] = "survey_id IN ($survey_id_in_sql)";
    }

    if ($scored_only) {
        $cond[] = "question_type = 'scored'";
    } 

    if (!$limit) {
        if (($offset != -1) && ($num != -1)) {
            $limit = "LIMIT $offset, $num";
            $calc_found_rows = 'SQL_CALC_FOUND_ROWS';
        }
        else if ($num != -1) {
            $limit = "LIMIT $num";
        }
    }

    if (!empty($cond)) {
        $cond = 'WHERE '.implode(' AND ', $cond);
    }
    else {
        $cond = '';
    }

    $sql = "
        SELECT $calc_found_rows ID, survey_id, question, question_type, dashboard_label, opt_out_label, left_label, right_label
        FROM $wpdb->questions
        $cond
        $orderby_sql
        $limit
    ";

    if ($ID) {
        $result = $wpdb->get_row($sql);
    }
    else {
        $result =$wpdb->get_results($sql);
        if ($calc_found_rows) {
            $total =$wpdb->get_var("SELECT FOUND_ROWS()");
            $result = compact('result', 'total');
        }
    }
    return $result;
}

function survey_insert_question($args = array()) {
    global $wpdb;
    $defaults = array(
        'ID' => 0,
        'survey_id' => 0,
        'question' => '',
        'question_type' => 'scored',
        'dashboard_label' => '',
        'opt_out_label' => '',
        'left_label' => '',
        'right_label' => ''
    );
    $args = wp_parse_args($args, $defaults);
    $args = array_map('stripslashes', $args);
    extract($args, EXTR_SKIP);

    $ID = (int) $ID;
    $survey_id = (int) $survey_id;
    $valid_question_types = array('free', 'scored');
    if (!in_array($question_type, $valid_question_types)) {
        $question_type = 'free';
    }
    $new_question = compact('survey_id', 'question', 'question_type', 'dashboard_label', 'opt_out_label', 'left_label', 'right_label');
    if ($ID) {
        $wpdb->update($wpdb->questions, $new_question, array('ID' => $ID));
    }
    else {
        $wpdb->insert($wpdb->questions, $new_question);
        $ID = $wpdb->insert_id;
    }
    return $ID;
}

function survey_get_answers($args = array()) {
    global $wpdb;
    $defaults = array(
        'question_id' => 0,
        'question_id_in' => array(),
        'survey_id' => 0,
        'offset' => -1,
        'num' => -1
    );
    $args = wp_parse_args($args, $defaults);
    extract($args, EXTR_SKIP);

    $question_id = (int) $question_id;
    $question_id_in = array_map('intval', $question_id_in);
    $survey_id = (int) $survey_id;
    $sql = '';
    $cond = array();
    $limit = '';
    $calc_found_rows = '';
    if ($question_id) {
        $cond[] = "a.question_id = $question_id";
    }

    if ($question_id_in) {
        $question_id_in_sql = implode(', ', $question_id_in);
        $cond[] = "a.question_id IN ($question_id_in_sql)";
    }
    if ($survey_id) {
        $cond[] = "a.survey_id = $survey_id";
    }
    if (!empty($cond)) {
        $cond = 'WHERE '.implode(' AND ', $cond);
    }
    else {
        $cond = '';
    }

    if (($offset >= 0) && ($num > 0)) {
        $limit = "LIMIT $offset, $num";
        $calc_found_rows = 'SQL_CALC_FOUND_ROWS';
    }
    else if ($num > 0) {
        $limit = "LIMIT $num";
    }

    $sql = "
        SELECT $calc_found_rows a.ID, a.question_id, a.user_id, a.score, a.text_answer, u.user_nicename
        FROM ($wpdb->answers a) LEFT OUTER JOIN  $wpdb->users u ON u.ID = a.user_id
        $cond
        ORDER BY a.ID
        $limit
    ";

    $result = $wpdb->get_results($sql);
    if ($calc_found_rows) {
        $total = $wpdb->get_var('SELECT FOUND_ROWS()');
        $result = compact('result', 'total');
    }
    return $result;
}

function survey_delete_answers($ids = array()) {
    global $wpdb;
    if (!is_array($ids)) $ids = array($ids);
    if (empty($ids)) return false;
    $ids = array_map('intval', $ids);
    $in = implode(', ', $ids);
    $wpdb->query("
        DELETE FROM $wpdb->answers
        WHERE ID IN ($in)
    ");
    return $wpdb->rows_affected;
}

function survey_delete_questions($ids) {
    global $wpdb;
    if (!is_array($ids)) $ids = array($ids);
    if (!$ids) return false;
    $ids = array_map('intval', $ids);
    $answers =survey_get_answers(array('question_id_in' => $ids));
    $answer_ids = array_map(create_function('$c', 'return $c->ID;'), $answers);
    survey_delete_answers($answer_ids);
    $in = implode(', ', $ids);
    $wpdb->query("
        DELETE FROM $wpdb->questions
        WHERE ID IN ($in)
    ");
    return $wpdb->rows_affected;
}

function survey_delete_surveys($ids) {
    global $wpdb;
    if (!is_array($ids)) $ids = array($ids);
    if (!$ids) return false;
    $ids = array_map('intval', $ids);
    $questions = survey_get_questions(array('survey_id_in' => $ids));
    if ($questions) {
        $question_ids = array_map(create_function('$c', 'return $c->ID;'), $questions);
        survey_delete_questions($question_ids);
    }
    $in = implode(', ', $ids);
    $wpdb->query("
        DELETE FROM $wpdb->surveys
        WHERE ID IN ($in)
    ");
    return $wpdb->rows_affected;
}


function survey_admin_menu() {
    add_menu_page('Surveys', 'Surveys', 'administrator', 'survey_surveys', 'survey_surveys');
    add_submenu_page('survey_surveys', 'Surveys', 'Surveys', 'administrator', 'survey_surveys', 'survey_surveys');
    add_submenu_page('survey_surveys', 'New Surveys', 'New Survey', 'administrator', 'survey_add_survey', 'survey_add_survey');

}
add_action('admin_menu', 'survey_admin_menu');


function survey_surveys() {
    $page = isset($_GET['paged']) ? (int) $_GET['paged'] : 1;
    $show_per_page = 10;
    $offset = ($page - 1) * $show_per_page;
    $result = survey_get_surveys("offset=$offset&num=$show_per_page");
    $surveys = $result['result'];
    $total = $result['total'];
    $page_links = paginate_links( array(
	    'base' => add_query_arg( 'paged', '%#%' ),
	    'format' => '?paged=%#%',
	    'prev_text' => '&laquo;',
	    'next_text' => '&raquo;',
	    'total' => ceil($total / $show_per_page),
	    'current' => $page
    ));
    $messages = array(
        'deleted' => 'Survey deleted'
    );
    $message = isset($_GET['message']) ? $messages[$_GET['message']] : '';
    include(survey_root().'/templates/surveys.php');
}

function survey_add_survey() {
    $ID = (int) $_GET['ID'];
    if ($ID) {
        $survey = survey_get_surveys("ID=$ID");
    }
    else {
        $survey = false;
    }
    include(survey_root().'/templates/add-survey.php');
}

function survey_save_survey() {
    $new_id = survey_insert_survey($_POST);
    wp_redirect("admin.php?action=survey_questions&survey_id=$new_id");    
}
add_action('admin_action_survey_save_survey', 'survey_save_survey');

function survey_questions() {
    $survey_id = (int) $_GET['survey_id'];
    $question_id = (int) $_GET['question_id'];
    if ($question_id) {
        $question = survey_get_questions("ID=$question_id");
    }
    else {
        $question = false;
    }
    $survey = survey_get_surveys("ID=$survey_id");
    $questions = survey_get_questions("survey_id=$survey_id");
    $messages = array(
        'saved' => 'Changes saved.',
        'deleted' => 'Question deleted.'
    );
    $message = isset($_GET['message']) ? $messages[$_GET['message']] : '';
    //error_reporting(E_ALL);
    include(survey_root().'/templates/questions.php');
}
add_action('admin_action_survey_questions', 'survey_questions');

function survey_humanize_question_type($type) {
    $types = array(
        'free' => 'Free text',
        'scored' => 'Scored response'
    );
    return $types[$type];
}

function survey_question_edit_link($question) {
    echo "admin.php?action=survey_questions&survey_id=$question->survey_id&question_id=$question->ID";
}

function survey_question_delete_link($question) {
    echo "admin.php?action=survey_delete_question&survey_id=$question->survey_id&question_id=$question->ID";
}

function survey_question_type_dropdown($selected = '') {
    $options = array(
        'scored' => 'Scored response',
        'free' => 'Free text'
    );
    $args = array(
        'name' => 'question_type',
        'selected' => $selected,
        'options' => $options
    );
    survey_dropdown($args);
}

function survey_dropdown($args = array()) {
    $defaults = array(
        'name' => '',
        'options' => array(),
        'selected' => 0,
        'echo' => true
    );

    $args = wp_parse_args($args, $defaults);
    extract($args, EXTR_SKIP);
    $out = "<select name='$name' size='1'>";
    foreach ($options as $value => $text) {
        $selected_attr = $value == $selected ? 'selected' : '';
        $value = esc_attr($value);
        $out .= "<option value='$value' $selected_attr>$text</option>";
    }

    $out .= '</select>';
    if ($echo) {
        echo $out;
    }
    else {
        return $out;
    }
}

function survey_save_question() {
    $survey_id = (int) $_POST['survey_id'];
    survey_insert_question($_POST);
    wp_redirect("admin.php?action=survey_questions&survey_id=$survey_id&message=saved");
    exit;
}
add_action('admin_action_survey_save_question', 'survey_save_question');

function survey_do_delete_question() {
    survey_delete_questions($_GET['question_id']);
    $survey_id = (int) $_GET['survey_id'];
    wp_redirect("admin.php?action=survey_questions&survey_id=$survey_id&message=deleted");
    exit;
}
add_action('admin_action_survey_delete_question', 'survey_do_delete_question');

function survey_summary_link($id) {
    echo "admin.php?action=survey_summary&survey_id=$id";
}

function survey_results_link($id) {
    echo "admin.php?action=survey_results&survey_id=$id";
}

function survey_edit_link($id) {
    echo "admin.php?page=survey_add_survey&ID=$id";
}

function survey_delete_link($id) {
    echo "admin.php?action=survey_delete_survey&survey_id=$id";
}

function survey_questions_link($id) {
    echo "admin.php?action=survey_questions&survey_id=$id";
}

function survey_do_delete_survey() {
    survey_delete_surveys($_GET['survey_id']);
    wp_redirect("admin.php?page=survey_surveys&message=deleted");
    exit;
}
add_action('admin_action_survey_delete_survey', 'survey_do_delete_survey');

function survey_display_survey($survey_id) {
    $survey = survey_get_surveys("ID=$survey_id");
    if (!is_user_logged_in() && ($survey->allow_anonymous != 1)) {
        return '<p>Please log in to participate in this survey.</p>';
    }
    else if (isset($_GET['completed'])) {
        return "<p>$survey->thankyou_message</p>";
    }
    else if (survey_already_participated($id)) {
        return '<p>You have already participated in this survey.</p>';
    }
    $questions = survey_get_questions("survey_id=$survey_id");
    ob_start();
    include(survey_root().'/templates/survey-form.php');
    return ob_get_clean();
}

function survey_shortcode($atts) {
    $defaults = array(
        'id' => 0
    );
    $atts = shortcode_atts($defaults, $atts);
    extract($atts, EXTR_SKIP);
    $result =  survey_display_survey($id);
    return $result;
}
add_shortcode('survey', 'survey_shortcode');


function survey_user_exists($user_id) {
    global $wpdb;
    $user_id = (int) $user_id;
    $user_id = $wpdb->get_var("
        SELECT user_id
        FROM $wpdb->answers
        WHERE user_id = '$user_id'
    ");
    return (bool) $user_id;
}

function survey_generate_user_id() {
    do {
        $user_id =-rand(1, 1000000000);
    } while(survey_user_exists($user_id));
    return $user_id;
}

function survey_save_answers() {
    global $wpdb;
    $survey_id = (int) $_POST['survey_id'];
    $re = $_POST['re'];
    if (survey_already_participated($survey_id)) {
        wp_redirect($re);
        exit;
    }
    $survey = survey_get_surveys("ID=$survey_id");
    $questions = survey_get_questions("survey_id=$survey_id");
    $user_id = get_user_option('ID');
    if (!$user_id && (1 == $survey->allow_anonymous)) {
        $user_id = survey_generate_user_id();
    }
    else if (!$user_id) {
        exit;
    }
    if (!$questions) {
        wp_redirect($re);
        exit;
    }

    $answers = array();
     
    foreach ($questions as $q) {
        $answer = $_POST['questions'][$q->ID];
        $db_answer = array(
            'question_id' => $q->ID,
            'user_id' => $user_id,
            'survey_id' => $survey_id
        );
        if ('scored' == $q->question_type) {
            $answer = (int) $answer;
            if (!$answer && !$q->opt_out_label) {
                wp_redirect($re);
                exit;
            }
            $db_answer['score'] = $answer;
            $db_answer['text_answer'] = '';
        }
        else {
            $answer = trim($answer);
            if (!$answer) {
                wp_redirect($re);
                exit;
            }
            $db_answer['score'] = 0;
            $db_answer['text_answer'] = $answer;
        }
        $answers[] = $db_answer;
    }

    foreach ($answers as $a) {
        $wpdb->insert($wpdb->answers, $a);
    }
    $re = add_query_arg('completed', 'true', $re);
    wp_redirect($re);
    exit;

}
add_action('wp_ajax_survey_save_answers', 'survey_save_answers');
add_action('wp_ajax_nopriv_survey_save_answers', 'survey_save_answers');

function survey_already_participated($survey_id) {
    global $wpdb;
    $survey_id = (int) $survey_id;
    $user_id = get_user_option('ID');
    $answer = $wpdb->get_var("
        SELECT a.ID
        FROM $wpdb->answers a, $wpdb->questions q
        WHERE q.survey_id = $survey_id
        AND a.question_id = q.ID
        AND a.user_id = $user_id
        LIMIT 1
    ");
    return $answer;
}

function survey_summary() {
    $survey_id = (int) $_GET['survey_id'];
    $survey = survey_get_surveys("ID=$survey_id");
    $questions = survey_get_questions("survey_id=$survey_id&scored_only=true");
    for ($i = 0, $len = count($questions); $i < $len; ++$i) {
        $questions[$i]->participation = survey_get_participation($questions[$i]->ID);

        $questions[$i]->rating = survey_get_rating($questions[$i]->ID);

    }
    include(survey_root().'/templates/summary.php');
}
add_action('admin_action_survey_summary', 'survey_summary');

function survey_get_participation($question_id) {
    global $wpdb;
    $num_participants = $wpdb->get_var("
        SELECT COUNT(*)
        FROM $wpdb->answers
        WHERE question_id = $question_id
    ");
    if (0 == $num_participants) return 0;
    $num_scored = $wpdb->get_var("
        SELECT COUNT(*)
        FROM $wpdb->answers
        WHERE question_id = $question_id
        AND score > 0
    ");
    $result =  $num_scored / $num_participants * 100;
    $result = number_format($result, 1);
    return $result;
}

function survey_get_rating($question_id) {
    global $wpdb;
    $avg_rating = $wpdb->get_var("
        SELECT SUM(score) / COUNT(*)
        FROM $wpdb->answers
        WHERE question_id = $question_id
        AND score > 0
    ");
    $avg_rating = (float) $avg_rating;
    $avg_rating *= 10;
    $avg_rating = number_format($avg_rating, 1);
    return $avg_rating;
}

function survey_results() {
    $survey_id = (int) $_GET['survey_id'];
    $survey = survey_get_surveys("ID=$survey_id");
    $questions = survey_get_questions("survey_id=$survey_id");
    $page = (int) $_GET['paged'];
    if (!$page) $page = 1;
    $show_per_page = 30 * count($questions);
    $offset = ($page - 1) * $show_per_page;
    $result = survey_get_answers("survey_id=$survey_id&offset=$offset&num=$show_per_page");
    $answers = $result['result'];
    $total = $result['total'];
    $page_links = paginate_links( array(
	    'base' => add_query_arg( 'paged', '%#%' ),
	    'format' => '?paged=%#%',
	    'prev_text' => '&laquo;',
	    'next_text' => '&raquo;',
	    'total' => ceil($total / $show_per_page),
	    'current' => $page
    ));

    $participants = array_map(create_function('$c', 'return $c->user_id;'), $answers);
    $participants = array_unique($participants);
    include(survey_root().'/templates/results.php');
}
add_action('admin_action_survey_results', 'survey_results');

function survey_get_participant_name($user_id, $answers) {
    if ($user_id < 0) return 'Anonymous';
    foreach ($answers as $a) {
        if ($a->user_id == $user_id) {
            return $a->user_nicename;
        }
    }
    return '';
}

function survey_get_participant_answer($user_id, $question_id, $answers) {
    foreach ($answers as $a) {
        if (($a->user_id == $user_id) && ($a->question_id == $question_id)) {
            if (strlen($a->text_answer)) {
                return $a->text_answer;
            } 
            else {
                return $a->score;
            }
        }
    }
    return false;
}
