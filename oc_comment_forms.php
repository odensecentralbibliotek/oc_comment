<?php
/*
 * CUSTOM FORMS
 */
function oc_comment_comment_ajax_reply_form($form, &$form_state) {
  $form['comment_message'] = array(
    '#type' => 'textarea', //you can find a list of available types in the form api
    '#size' => 50,
    '#required' => TRUE, //make this field required
    '#name' => 'comment_message',
    '#id' => 'reply_comment_message',
    '#placeholder' => t('Write your comment reply here..')
  );
  $form['#action'] = "/oc/comments/ajax_form/reply/submit"; //the submit will be hijacked by javascript.
  
  $form['submit_button'] = array(
    '#type' => 'button',
    '#attributes' => array(
        'class' => array("oc_comment_btn")
        ),
    '#value' => t('Save'),
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
  
  return $form;
}
/*
 * 
 */
function oc_comment_comment_ajax_edit_form($form, &$form_state) {
    
   $form['comment_message'] = array(
    '#type' => 'textarea', //you can find a list of available types in the form api
    '#size' => 50,
    '#required' => TRUE, //make this field required
    '#name' => 'edit_comment_message',
    '#id' => 'edit_comment_message'
  );
  $form['#action'] = "/oc/comments/ajax_form/reply/edit"; //the submit will be hijacked by javascript.
  
  $form['submit_button'] = array(
    '#type' => 'button',
    '#value' => t('Save'),
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
  
  return $form;
}
/*
 * 
 */
function oc_comment_comment_ajax_delete_form($form, &$form_state)
{
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
        'onclick' => "jQuery(this).parent().fadeOut('slow').toggle();return false;",
        'class' => array("oc_comment_btn"),
        )
  );
  return $form;
}
