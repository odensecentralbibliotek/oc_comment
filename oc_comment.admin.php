<?php

function oc_comment_config_form() {
    $form = array();
    $form['oc_comments_fieldset_logo'] = array(
        '#type' => 'fieldset',
        '#title' => t('Employee banner Settings'),
        '#weight' => 0,
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
    /*
     * How many levels of comments are available ?
     * 1 = only the top level comment can be reply'd too.
     * 2 = top level and users can reply to reply's on the top level.
     * and so on.
     * Normal settings woult be 1 or 2.
     */
    $form['oc_comment_max_reply_level'] = array(
        '#type' => 'select',
        '#title' => t('Maximum reply level:'),
        '#default_value' => variable_get('oc_comment_max_reply_level', 1),
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
    $form['oc_comment_top_level_sort'] = array(
        '#type' => 'select',
        '#title' => t('Sorting of top level comments'),
        '#default_value' => variable_get('oc_comment_top_level_sort', 'DESC'),
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
    $form['oc_comment_child_level_sort'] = array(
        '#type' => 'select',
        '#title' => t('Sorting of child level comments'),
        '#default_value' => variable_get('oc_comment_child_level_sort', 'ASC'),
        '#options' => array(
            'ASC' => 'ASCENDING',
            'DESC' => 'DESCENDING',
        ),
        '#required' => TRUE,
    );
    /*
     * Should the children always be expanded ?
     */
    $form['oc_hide_children'] = array(
      '#type' => 'checkbox', 
      '#title' => t('Comment children always shown'),
      );
    
    return system_settings_form($form);
}
