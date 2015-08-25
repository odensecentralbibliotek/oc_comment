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
 * option: make it more drupal and reduce number of sql queries when generating.
 */

function oc_comments_build_comment_array($node) {
    /*
     * Select all the top level comments.
     */
    $Comments_render_arrary = array();
    $Top_level_sort = variable_get('oc_comment_top_level_sort_' . $node->type, 'DESC');
    $Child_level_sort = variable_get('oc_comment_child_level_sort_' . $node->type, 'ASC');
    $result = db_query("SELECT * FROM comment WHERE nid = :nid AND pid = 0  ORDER BY created {$Top_level_sort} ", array(':nid' => $node->nid));
    foreach ($result as $index => $top_comment) {
        //Find all children og the current top node.
        
        $child_nodes = db_query("SELECT * FROM comment WHERE nid = :nid AND pid = :pid  ORDER BY created {$Child_level_sort}", array(':nid' => $node->nid, ':pid' => $top_comment->cid));
        $child_nodes = $child_nodes->fetchAll();
        
        $obj_comments = new stdClass;
        $obj_comments->parent = $top_comment;
        $obj_comments->children = array();
        
        if (sizeof($child_nodes)) {
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
    $value = $entity->comment_body->value();
    if (is_array($value)) {
        return decode_entities(check_plain($value['value']));
    } else {
        return decode_entities(check_plain($value));
    }
}

/*
 * Rendering of comment entities.
 * The \n are added to make it easier to debug in browser.
 */

function render_single_comment_entity($entity, $current_level, $is_child = false) {

    return theme('oc_comment_item', array('entity' => $entity, 'current_level' => $current_level, 'is_child' => $is_child));
}

/*
 * 
 */

function oc_comments_user_check_validation($msg = null, $required_role = null) {
    if (!user_is_logged_in()) {
        return false;
    }
    return true;
}

/*
 * deletes comment and all its children
 */

function oc_comment_recursive_delete($cid) {
    //are just starting ?
    $node = node_load($cid);
    $Top_level_sort = variable_get('oc_comment_top_level_sort_' . $node->type, 'DESC');
    $result = db_query("SELECT * FROM comment WHERE cid = :cid  ORDER BY created {$Top_level_sort} ", array(':cid' => $cid));
    $delete_count = 0;

    foreach ($result as $index => $top_comment) {
        $child_nodes = db_query("SELECT * FROM comment WHERE pid = :pid  ORDER BY created {$Child_level_sort}", array(':pid' => $top_comment->cid));
        $child_nodes = $child_nodes->fetchAll();
        //do we have more children ? 
        if (sizeof($child_nodes)) {
            //For all children
            foreach ($child_nodes as $i => $child) {
                //Check if deeper level children and delete.
                oc_comment_recursive_delete($child->cid);
                entity_delete('comment', $child->cid);
            }
        }
        entity_delete('comment', $top_comment->cid);
    }
    return true;
}

/*
 * function to check if comment array contains cid.
 */

function contains_comment_id($comments, $cid) {
    foreach ($comments as $index => $obj) {
        if ($obj->parent->cid == $cid) {
            return true;
        } else {
            $return = contains_comment_id($obj->children, $cid);
            if ($return === true) {
                return $return;
            }
        }
    }
}
