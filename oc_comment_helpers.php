<?php

/*
 * Builds a proper child node data structure for easier usage later.
 */
function recursive_get_child_arrays($comment)
{
   $result = array();
   $child_nodes = db_query('SELECT * FROM comment WHERE nid = :nid AND pid = :pid', array(':nid' => $comment->nid,':pid' => $comment->cid));
   foreach($child_nodes as $index => $children)
   {
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
function oc_comments_build_comment_array($node)
{
    /*
     * Select all the top level comments.
     */
    $Comments_render_arrary = array();
    $result = db_query('SELECT * FROM comment WHERE nid = :nid AND pid = 0 AND status != 0 ORDER BY created DESC', array(':nid' => $node->nid));
    foreach($result as $index => $top_comment)
    {
        //Find all children og the current top node.
        $child_nodes = db_query('SELECT * FROM comment WHERE nid = :nid AND pid = :pid AND status != 0 ORDER BY created ASC', array(':nid' => $node->nid,':pid' => $top_comment->cid));
        $child_nodes = $child_nodes->fetchAll();
        if(sizeof($child_nodes))
        {
            $obj_comments = new stdClass;
            $obj_comments->parent = $top_comment;
            $obj_comments->children = array();
            
            //add the children
            foreach($child_nodes as $index => $value)
            {
                $obj_child = new stdClass;
                $obj_child->parent = $value;
                $obj_child->children = array();
                $obj_child->children = recursive_get_child_arrays($value);
                $obj_comments->children[$value->cid] = $obj_child;
            }
            //Add the container
            $Comments_render_arrary[$top_comment->cid] = $obj_comments;
        }
        else
        {
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
function oc_comment_get_comment_body($cid)
{
    if(!isset($cid) || $cid == '')
    {
        return "";
    }
    $comment = entity_load('comment',array($cid));
    $comment = reset($comment);
    $entity = entity_metadata_wrapper('comment',   $comment);
    return $entity->comment_body->value();
}
function render_single_comment_entity($entity,$current_level,$is_child = false)
{
    $html = "";
    $comment_user = user_load($entity->parent->uid);
    $full_name = isset($comment_user->data['display_name']) ? $comment_user->data['display_name'] : $comment_user->name;
    /*
     * Check if user is employee/admin and add logo to top right of comment box.
     * makes it all look more official :)
     */
    $site_admin_roles = array('administrator','redaktør');
    
    $html.= "<div id='cid-".$entity->parent->cid."' class='comment "
    . ($is_child == true ? 'oc_comment_child' : 'oc_comment_parent') ."'>";
    foreach($comment_user->roles as $roles)
    {
        if(in_array($roles, $site_admin_roles))
        {
            $html.= "<img title='Biblioteks ansat' id='comment_logo' src='http://dev.odensebib.dk/sites/dev.odensebib.dk/files/logo.png' />";
            break;
        }
    }
    $html.= "<input type='hidden' id='comment_level' value='".$current_level."' />";
    $html.= "<div class='submitted'>" . $full_name . " - " . date("d-m-Y H:i", $entity->parent->created). "</div>";
    $html.= "<div class='content'>".oc_comment_get_comment_body($entity->parent->cid)."</div>";
    $html.= "<div style='text-align: right !important' class='comment_toolbar'>";
    //Is current level allowed to comment ? if not then dont display comment count
    if($current_level < variable_get('oc_comment_max_reply_level', 1) && sizeof($entity->children) != 0)
    {
        $html.= '<a class="oc_comment_btn oc_comment_read_btn">'.sizeof($entity->children).' kommentare</a>';
    }
    if($entity->parent->pid == 0)
    {
        $html .= oc_comment_get_buttons($entity->parent,$current_level);
    }
    else
    {
        //reassign here to avoid strict warning. php dont like too many statements together
        $parent = entity_load('comment',array($entity->parent->pid));
        $parent = reset($parent);
        $tmp = $entity;
        $html .= oc_comment_get_buttons($tmp->parent,$current_level,$parent);
    }
     
     $html.= "</div></div>";
     return $html;
}