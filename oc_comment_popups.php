<?php

/*
 * ajax based form popups.
 * Could be made drupaly. but the custom js is pure jquery anyways and 
 * adds improved flexability with effects.
 */

function oc_comment_ajax_login_popup() {
    $form = drupal_get_form('user_login');
    $tmp = parse_url($_SERVER['HTTP_REFERER']);
    $form['#action'] = "/user/login?destination=" . ltrim($tmp['path'], '/');
    //$form['#submit'][0] = 'oc_comment_login_redirect_overwrite';
    // Just print the form directly if this is an AJAX request
    echo render($form);
    // Halt page processing
    drupal_exit();
}

function oc_comment_ajax_reply_popup() {
    $form = drupal_get_form('oc_comment_comment_ajax_reply_form');
    echo render($form);
    drupal_exit();
}

function oc_comment_ajax_edit_popup() {
    $form = drupal_get_form('oc_comment_comment_ajax_edit_form');
    echo render($form);
    drupal_exit();
}

function oc_comment_ajax_delete_popup() {
    $form = drupal_get_form('oc_comment_comment_ajax_delete_form');
    echo render($form);
    drupal_exit();
}
