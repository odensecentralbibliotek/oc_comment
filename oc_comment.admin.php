<?php

function oc_comment_config_form() {
  $form = array();
  $form['oc_comments_fieldset_logo'] = array(
    '#type' => 'fieldset',
    '#title' => t('Employee banner Settings'),
    '#weight' => 0,
  );
  
   $form['oc_comments_fieldset_logo']['oc_comment_file_path'] = array(
    '#type' => 'textfield',
    '#title' => t('image file:'),
    '#default_value' => variable_get('oc_comment_file_path', ''),
    '#size' => 20,
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
  $form['oc_comment_max_reply_level'] = array(
  '#type' => 'select',
  '#title' => t('Maximum levels allowed to reply too'),
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

  return system_settings_form($form);

}

