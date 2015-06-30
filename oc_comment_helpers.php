<?php

/*
 * Builds a proper child node data structure for easier usage later.
 */

function recursive_get_child_arrays($comment) {
    $result = array();
    $child_nodes = db_query('SELECT * FROM comment WHERE nid = :nid AND pid = :pid', array(':nid' => $comment->nid, ':pid' => $comment->cid));
    foreach ($child_nodes as $index => $children) {
        $obj_comments = new stdClass;
        $obj_comments->parent = $children;
        $obj_comments->children = recursive_get_child_arrays($children);
        $result[$children->cid] = $obj_comments;
    }
    //Build comments and get their children
    return $result;
}

/*
 * Builds a new and better comments array for the custom render function.
 * It improves the thread handling immensely and makes working with the comments
 * easier.
 * option: make it more drupal.
 */

function oc_comments_build_comment_array($node) {
    /*
     * Select all the top level comments.
     */
    $Comments_render_arrary = array();
    $Top_level_sort = variable_get('oc_comment_top_level_sort', 'DESC');
    $result = db_query("SELECT * FROM comment WHERE nid = :nid AND pid = 0  ORDER BY created {$Top_level_sort} ", array(':nid' => $node->nid));
    foreach ($result as $index => $top_comment) {
        //Find all children og the current top node.
        $Child_level_sort = variable_get('oc_comment_child_level_sort', 'ASC');
        $child_nodes = db_query("SELECT * FROM comment WHERE nid = :nid AND pid = :pid  ORDER BY created {$Child_level_sort}", array(':nid' => $node->nid, ':pid' => $top_comment->cid));
        $child_nodes = $child_nodes->fetchAll();
        if (sizeof($child_nodes)) {
            $obj_comments = new stdClass;
            $obj_comments->parent = $top_comment;
            $obj_comments->children = array();

            //add the children
            foreach ($child_nodes as $index => $value) {
                $obj_child = new stdClass;
                $obj_child->parent = $value;
                $obj_child->children = array();
                $obj_child->children = recursive_get_child_arrays($value);
                $obj_comments->children[$value->cid] = $obj_child;
            }
            //Add the container
            $Comments_render_arrary[$top_comment->cid] = $obj_comments;
        } else {
            $obj_comments = new stdClass;
            $obj_comments->parent = $top_comment;
            $obj_comments->children = array();
            $Comments_render_arrary[$top_comment->cid] = $obj_comments;
        }
    }
    return $Comments_render_arrary;
}

/*
 * Retrive the body of the given comment from the related field table.
 */

function oc_comment_get_comment_body($cid) {
    if (!isset($cid) || $cid == '') {
        return "";
    }
    $comment = entity_load('comment', array($cid));
    $comment = reset($comment);
    $entity = entity_metadata_wrapper('comment', $comment);
    return check_markup($entity->comment_body->value());
}

/*
 * Rendering of comment entities.
 * The \n are added to make it easier to debug in browser.
 */

function render_single_comment_entity($entity, $current_level, $is_child = false) {
    
    return theme('oc_comment_item',array('entity' => $entity,'current_level' => $current_level,'is_child' => $is_child));
}

function oc_comments_user_check_validation($msg = null, $required_role = null) {
    if (!user_is_logged_in()) {
        drupal_exit();
    }
}
