<?php

/*
 * CUSTOM FORMS
 */

function oc_comment_comment_submit_form($form, &$form_state) {
    $nid = $form_state['build_info']['args'][0];
    $node = node_load($nid);
    $form['Headline'] = array(
        '#markup' => '<h2>' . t('Write comment') . '</h2>',
    );
    $form['my_markup'] = array(
        '#markup' => '<div class="submit-form-error-message"></div>'
    );
    if (variable_get('comment_subject_field_' . $node->type, 0)) {
        $form['comment_subject'] = array(
            '#type' => 'textfield', //you can find a list of available types in the form api
            '#size' => 50,
            '#required' => TRUE, //make this field required
            '#name' => 'comment_subject',
            '#id' => 'reply_comment_submit_subject',
            '#attributes' => array('placeholder' => t('Write your comment subject here')),
        );
    }
    $form['comment_message'] = array(
        '#type' => 'textarea', //you can find a list of available types in the form api
        '#size' => 50,
        '#required' => TRUE, //make this field required
        '#name' => 'comment_message',
        '#id' => 'reply_comment_submit_message',
        '#attributes' => array('placeholder' => t('Write your comment reply here..')),
    );
    $form['#action'] = "/oc/comments/ajax_form/reply/submit"; //the submit will be hijacked by javascript.

    $rulesLink = variable_get('oc_comment_rules_link', null);
    if ($rulesLink != null) {
        $form['terms_of_usage'] = array(
            '#markup' => "<a href='{$rulesLink}' target='_blank' class='oc-comments-terms-links'>" . t('se vores regler') . "</a>"
        );
    }

    $form['submit_button'] = array(
        '#type' => 'button',
        '#attributes' => array(
            'class' => array("oc_comment_btn")
        ),
        '#value' => t('Send'),
        '#id' => 'oc_comment_submit_form_btn',
        '#href' => "#",
    );
    if (variable_get('oc_comment_reply_limit_active_' . $node->type, 0)) {
        $form['char_limit'] = array(
            '#markup' => '<p class="oc_comment_word_limit">' . variable_get('oc_comment_max_reply_length_' . $node->type, 0) . '<p>',
        );
        //Force max length
        $form['comment_message']['#attributes'] = array('maxlength' => variable_get('oc_comment_max_reply_length_' . $node->type, 250));
    }
    return $form;
}

/*
 * 
 */

function oc_comment_comment_ajax_reply_form($form, &$form_state) {
    $nid = $form_state['build_info']['args'][0];
    $node = node_load($nid);

    if (variable_get('comment_subject_field_' . $node->type, 0)) {
        $form['comment_subject'] = array(
            '#type' => 'textfield', //you can find a list of available types in the form api
            '#size' => 50,
            '#required' => TRUE, //make this field required
            '#name' => 'comment_subject',
            '#id' => 'reply_comment_subject',
            '#attributes' => array('placeholder' => t('Write your comment subject here')),
        );
    }
    $form['comment_message'] = array(
        '#type' => 'textarea', //you can find a list of available types in the form api
        '#size' => 50,
        '#required' => TRUE, //make this field required
        '#name' => 'comment_message',
        '#id' => 'reply_comment_message',
        '#attributes' => array('placeholder' => t('Write your comment reply here..')),
    );
    $form['#action'] = "/oc/comments/ajax_form/reply/submit"; //the submit will be hijacked by javascript.
    $rulesLink = variable_get('oc_comment_rules_link', null);
    if ($rulesLink != null) {
        $form['terms_of_usage'] = array(
            '#markup' => "<a href='{$rulesLink}' target='_blank' class='oc-comments-terms-links'>" . t('se vores regler') . "</a>"
        );
    }
    $form['submit_button'] = array(
        '#type' => 'button',
        '#attributes' => array(
            'class' => array("oc_comment_btn")
        ),
        '#value' => t('Send'),
        '#id' => 'oc_comment_submit_reply_btn',
        '#href' => "#",
    );
    $form['cancel_button'] = array(
        '#type' => 'button',
        '#value' => t('Cancel'),
        '#id' => 'oc_comment_submit_delete_cancel_btn',
        '#attributes' => array(
            'onclick' => "jQuery(this).parent().parent().parent().fadeOut('slow').hide();return false;",
            'class' => array("oc_comment_btn"),
        )
    );
    if (variable_get('oc_comment_reply_limit_active_' . $node->type, 0)) {
        $form['char_limit'] = array(
            '#markup' => '<p class="oc_comment_word_limit">' . variable_get('oc_comment_max_reply_length_' . $node->type, 0) . '<p>',
        );
        $form['comment_message']['#attributes'] = array('maxlength' => variable_get('oc_comment_max_reply_length_' . $node->type, 250));
    }
    return $form;
}

/*
 * 
 */

function oc_comment_comment_ajax_edit_form($form, &$form_state) {
    $nid = $form_state['build_info']['args'][0];
    $node = node_load($nid);

    if (variable_get('comment_subject_field_' . $form['#node_type']->type, 0)) {
        $form['comment_subject'] = array(
            '#type' => 'textfield', //you can find a list of available types in the form api
            '#size' => 50,
            '#required' => TRUE, //make this field required
            '#name' => 'comment_subject',
            '#id' => 'reply_comment_subject',
            '#placeholder' => t('Write your comment title here')
        );
    }
    $form['comment_message'] = array(
        '#type' => 'textarea', //you can find a list of available types in the form api
        '#size' => 50,
        '#required' => TRUE, //make this field required
        '#name' => 'edit_comment_message',
        '#id' => 'edit_comment_message',
    );
    $form['#action'] = "/oc/comments/ajax_form/reply/edit"; //the submit will be hijacked by javascript.

    $rulesLink = variable_get('oc_comment_rules_link', null);
    if ($rulesLink != null) {
        $form['terms_of_usage'] = array(
            '#markup' => "<a href='{$rulesLink}' target='_blank' class='oc-comments-terms-links'>" . t('se vores regler') . "</a>"
        );
    }

    $form['submit_button'] = array(
        '#type' => 'button',
        '#value' => t('Send'),
        '#id' => 'oc_comment_submit_edit_btn',
        '#attributes' => array(
            'class' => array("oc_comment_btn"),
        ),
        '#href' => "#",
    );
    $form['cancel_button'] = array(
        '#type' => 'button',
        '#value' => t('Cancel'),
        '#id' => 'oc_comment_submit_delete_cancel_btn',
        '#attributes' => array(
            'onclick' => "jQuery(this).parent().parent().parent().fadeOut('slow').toggle();return false;",
            'class' => array("oc_comment_btn"),
        )
    );
    if (variable_get('oc_comment_reply_limit_active_' . $node->type, 0)) {
        $form['char_limit'] = array(
            '#markup' => '<p class="oc_comment_word_limit">' . variable_get('oc_comment_max_reply_length_' . $node->type, 0) . '<p>',
        );
        //Force max length
        $form['comment_message']['#attributes'] = array('maxlength' => variable_get('oc_comment_max_reply_length_' . $node->type, 250));
    }
    return $form;
}

/*
 * 
 */

function oc_comment_comment_ajax_delete_form($form, &$form_state) {
    $form['ok_button'] = array(
        '#type' => 'button',
        '#value' => t('Delete Comment'),
        '#id' => 'oc_comment_submit_delete_confirm_btn',
    );

    $form['cancel_button'] = array(
        '#type' => 'button',
        '#value' => t('Cancel'),
        '#id' => 'oc_comment_submit_delete_cancel_btn',
        '#attributes' => array(
            'onclick' => "jQuery(this).parent().parent().parent().fadeOut('slow').toggle();return false;",
            'class' => array("oc_comment_btn"),
        )
    );
    return $form;
}

/*
 * ajax based form loading.
 */

function oc_comment_ajax_login_form() {
    $form = drupal_get_form('user_login');
    $tmp = parse_url($_SERVER['HTTP_REFERER']);
    $form['#action'] = "/user/login?destination=" . ltrim($tmp['path'], '/');
    echo render($form);
    // Halt page processing
    drupal_exit();
}

/*
 * 
 */

function oc_comment_ajax_reply_form($nid) {
    /*
     * Pass the current nid , so we can figure out if we need to include subject.
     */
    $form = drupal_get_form('oc_comment_comment_ajax_reply_form', $nid);
    echo render($form);
    drupal_exit();
}

/*
 * 
 */

function oc_comment_ajax_edit_form($nid) {
    $form = drupal_get_form('oc_comment_comment_ajax_edit_form', $nid);
    echo render($form);
    drupal_exit();
}

/*
 * 
 */

function oc_comment_ajax_delete_form($nid) {
    $form = drupal_get_form('oc_comment_comment_ajax_delete_form');
    echo render($form);
    drupal_exit();
}
