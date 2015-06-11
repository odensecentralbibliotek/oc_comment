<?php
/*
 * CUSTOM FORMS
 */
function oc_comment_comment_ajax_reply_form($form, &$form_state) {
  $form['comment_message'] = array(
    '#type' => 'textarea', //you can find a list of available types in the form api
    '#size' => 50,
    '#required' => TRUE, //make this field required
    '#name' => 'comment_message'
  );
  $form['#action'] = "/oc/comments/ajax_form/reply/submit"; //the submit will be hijacked by javascript.
  
  $form['submit_button'] = array(
    '#type' => 'button',
    '#value' => t('Click Here!'),
    '#id' => 'oc_comment_submit_reply_btn',
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
    '#name' => 'comment_message'
  );
  $form['#action'] = "/oc/comments/ajax_form/reply/edit"; //the submit will be hijacked by javascript.
  
  $form['submit_button'] = array(
    '#type' => 'button',
    '#value' => t('Click Here!'),
    '#id' => 'oc_comment_submit_edit_btn',
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
    '#value' => t('Slet!'),
    '#id' => 'oc_comment_submit_delete_confirm_btn',
  );
  
  $form['cancel_button'] = array(
    '#type' => 'button',
    '#value' => t('annuler'),
    '#id' => 'oc_comment_submit_delete_cancel_btn',
      '#attributes' => array(
        'onclick' => "jQuery('#oc-comment-comment-ajax-delete-form').dialog('close');return false;",
        )
  );
  return $form;
}
