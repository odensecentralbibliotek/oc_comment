<?php

function oc_comment_config_form() {
    $form = array();

    $form['oc_comments_fieldset_logo'] = array(
        '#type' => 'fieldset',
        '#title' => t('Employee banner Settings'),
        '#weight' => 0,
        '#collapsible' => TRUE,
        '#collapsed' => true,
    );
    $test = variable_get('oc_comments_logo', '');
    if ($test != '') {
        $file = file_load(variable_get('oc_comments_logo'));
        $uri = $file->uri;
        $url = file_create_url($uri);
        variable_set('oc_comment_file_path', $url);
    }

    $form['oc_comments_fieldset_logo']['oc_comment_file_path'] = array(
        '#type' => 'textfield',
        '#title' => t('image file:'),
        '#default_value' => variable_get('oc_comment_file_path', 'https://odensebib.dk/sites/www.odensebib.dk/files/logo.png'),
        '#size' => 150,
        '#description' => t("image file name."),
    );

    $form['oc_comments_fieldset_logo']['oc_comments_logo'] = array(
        '#type' => 'media',
        '#title' => t('Choose a file'),
        '#description' => t('Choose a file'),
        '#tree' => TRUE,
        '#input' => TRUE,
        '#media_options' => array(
            'global' => array(
                'types' => array(
                    'image' => 'image'
                ),
                'schemes' => array(
                    'public' => 'public',
                ),
                'enabledPlugins' => array(
                    'upload' => 'upload',
                    'media_default--media_browser_my_files',
                    'media_default--media_browser_1',
                ),
            ),
        ),
    );
    $form['oc_comment_rules_link'] = array(
        '#type' => 'textfield',
        '#title' => t('Kommentar skrivnings regler:'),
        '#default_value' => variable_get('oc_comment_rules_link', null),
    );

    /*
     * Email forms
     */
    $form['oc_comments_fieldset_mailset'] = array(
        '#type' => 'fieldset',
        '#title' => t('Mail settings'),
        '#weight' => 0,
        '#collapsible' => TRUE,
    );
    $form['oc_comments_fieldset_mailset']['oc_comment_backup_emails'] = array(
        '#type' => 'textfield',
        '#title' => t('Backup Emails:'),
        '#default_value' => variable_get('oc_comment_backup_emails', ''),
        '#size' => 150,
        '#description' => t("comma seperated list of emails to send info emails too"),
    );

    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data'] = array(
        '#type' => 'fieldset',
        '#title' => t('Email Messages'),
        '#weight' => 1,
        '#collapsible' => TRUE,
        '#collapsed' => true,
    );
    /*
     * New comment email body and title
     */
    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['new_comment'] = array(
        '#type' => 'fieldset',
        '#title' => t('New Comment Email'),
        '#weight' => 0,
        '#collapsible' => TRUE,
        '#collapsed' => true,
    );
    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['new_comment']['oc_comments_email_new_comment_title'] = array(
        '#type' => 'textfield',
        '#title' => t('email title:'),
        '#default_value' => variable_get('oc_comments_email_new_comment_title', ''),
        '#size' => 150,
        '#description' => t("Email title"),
    );
    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['new_comment']['oc_comments_email_new_comment_body'] = array(
        '#type' => 'textarea', //you can find a list of available types in the form api
        '#size' => 50,
        '#required' => TRUE, //make this field required
        '#name' => 'email_new_comment_body',
        '#id' => 'email_new_comment_body',
        '#attributes' => array('placeholder' => t('New comment email body')),
    );
    /*
     * Comment published email body and title
     */
    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['new_published_comment'] = array(
        '#type' => 'fieldset',
        '#title' => t('Comment Published Email'),
        '#weight' => 0,
        '#collapsible' => TRUE,
        '#collapsed' => true,
    );
    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['new_published_comment']['oc_comments_email_published_comment_title'] = array(
        '#type' => 'textfield',
        '#title' => t('email title:'),
        '#default_value' => variable_get('oc_comments_email_published_comment_title', ''),
        '#size' => 150,
        '#description' => t("Email title"),
    );

    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['new_published_comment']['oc_comments_email_published_comment_body'] = array(
        '#type' => 'textarea', //you can find a list of available types in the form api
        '#size' => 50,
        '#required' => TRUE, //make this field required
        '#name' => 'email_published_comment_body',
        '#id' => 'email_published_comment_body',
        '#attributes' => array('placeholder' => t('New comment email body')),
    );

    /*
     * Comment deleted email body and title
     */
    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['deleted_comment'] = array(
        '#type' => 'fieldset',
        '#title' => t('Comment Deleted Email'),
        '#weight' => 0,
        '#collapsible' => TRUE,
        '#collapsed' => true,
    );
    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['deleted_comment']['oc_comments_email_deleted_comment_title'] = array(
        '#type' => 'textfield',
        '#title' => t('email title:'),
        '#default_value' => variable_get('oc_comments_email_published_comment_title', ''),
        '#size' => 150,
        '#description' => t("Email title"),
    );

    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['deleted_comment']['oc_comments_email_deleted_comment_body'] = array(
        '#type' => 'textarea', //you can find a list of available types in the form api
        '#size' => 50,
        '#required' => TRUE, //make this field required
        '#name' => 'email_deleted_comment_body',
        '#id' => 'email_deleted_comment_body',
        '#attributes' => array('placeholder' => t('New comment email body')),
    );

    /*
     * Email replacement patterns
     */
    $form['oc_comments_fieldset_mailset']['oc_comments_mail_data']['token_tree'] = array(
        '#type' => 'fieldset',
        '#title' => t('Replacement patterns'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
        '#description' => theme('token_tree', array('token_types' => array('current-user'))),
        '#weight' => 10,
    );
    return system_settings_form($form);
}

function oc_comment_form_node_type_form_alter(&$form, $form_state) {
    /*
     * How many levels of comments are available ?
     * 1 = only the top level comment can be reply'd too.
     * 2 = top level and users can reply to reply's on the top level.
     * and so on.
     * Normal settings woult be 1 or 2.
     */
    hide($form['comment']['comment_form_location']);
    //hide($form['comment']['comment_preview']);
    hide($form['comment']['comment_default_mode']);
    /*
     * Should the children always be expanded ?
     */
    $form['comment']['oc_hide_children'] = array(
        '#type' => 'checkbox',
        '#title' => t('Hide comment children'),
        '#default_value' => variable_get('oc_hide_children_' . $form['#node_type']->type, 0),
    );

    $form['comment']['oc_comment_reply_limit_active'] = array(
        '#type' => 'checkbox',
        '#title' => t('Activate comment limit'),
        '#default_value' => variable_get('oc_comment_reply_limit_active_' . $form['#node_type']->type, 0),
    );

    $form['comment']['oc_comment_max_reply_level'] = array(
        '#type' => 'select',
        '#title' => t('Maximum reply level:'),
        '#default_value' => variable_get('oc_comment_max_reply_level_' . $form['#node_type']->type, 1),
        '#options' => array(
            0 => '0',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
        ),
        '#required' => TRUE,
    );
    /*
     * How to sort the first level of comments 
     * This is normaly the newest first , so users always get the moste updatet.
     */
    $form['comment']['oc_comment_top_level_sort'] = array(
        '#type' => 'select',
        '#title' => t('Sorting of top level comments'),
        '#default_value' => variable_get('oc_comment_top_level_sort_' . $form['#node_type']->type, 'DESC'),
        '#options' => array(
            'ASC' => 'ASCENDING',
            'DESC' => 'DESCENDING',
        ),
        '#required' => TRUE,
    );

    /*
     * Sorting of children is normaly oldest first , so users the follow
     * the conversation , instead of scrolling to the bottom to get the
     * red line.
     */
    $form['comment']['oc_comment_child_level'] = array(
        '#type' => 'select',
        '#title' => t('Sorting of child level comments'),
        '#default_value' => variable_get('oc_comment_child_level_sort_' . $form['#node_type']->type, 'ASC'),
        '#options' => array(
            'ASC' => 'ASCENDING',
            'DESC' => 'DESCENDING',
        ),
        '#required' => TRUE,
    );

    if (variable_get('oc_comment_reply_limit_active_' . $form['#node_type']->type, 0)) {
        $form['comment']['oc_comment_max_reply_length'] = array(
            '#type' => 'textfield',
            '#title' => t('max reply length:'),
            '#default_value' => variable_get('oc_comment_max_reply_length_' . $form['#node_type']->type, 250),
            '#size' => 25,
            '#description' => t("The maximum length of the comments texts. Is usefull to force the user to keep to the escense"),
        );
    }
}

/*
 * Hook to make ding users real name appear i admin comment manager.
 */

function oc_comment_views_pre_render(&$view) {
    if ($view->name == "admin_views_comment") {
        foreach ($view->result as $index => $comment_data) {
            //is it a ding user ? without a proper name.
            //load the user
            $user = user_load($comment_data->_field_data['cid']['entity']->uid);
            if (isset($user->data['display_name'])) {
                $comment_data->comment_name = $user->data['display_name'];
            }
        }
    }
}
