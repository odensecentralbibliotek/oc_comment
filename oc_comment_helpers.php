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
    $Top_level_sort = variable_get('oc_comment_top_level_sort','DESC');
    $result = db_query("SELECT * FROM comment WHERE nid = :nid AND pid = 0  ORDER BY created {$Top_level_sort} ", array(':nid' => $node->nid));
    foreach($result as $index => $top_comment)
    {
        //Find all children og the current top node.
        $Child_level_sort = variable_get('oc_comment_child_level_sort','ASC');
        $child_nodes = db_query("SELECT * FROM comment WHERE nid = :nid AND pid = :pid  ORDER BY created {$Child_level_sort}", array(':nid' => $node->nid,':pid' => $top_comment->cid));
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
/*
 * Rendering of comment entities.
 * The \n are added to make it easier to debug in browser.
 */
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
    $wrap_classes = array();
    $wrap_classes[] = "comment";
    $wrap_classes[] = $entity->parent->status == 0 ? 'oc-comment-approval-required' : 'oc-comment-approved';
    $wrap_classes[] = ($is_child == true ? 'oc_comment_child' : 'oc_comment_parent');
    
    $html.= "<div id='cid-".$entity->parent->cid."' class='" .implode(' ', $wrap_classes) ."'>";
    foreach($comment_user->roles as $roles)
    {
        if(in_array($roles, $site_admin_roles))
        {
            $logo_img = variable_get('oc_comment_file_path','https://odensebib.dk/sites/www.odensebib.dk/files/logo.png');
            $html .= "<img title='Biblioteks ansat' id='comment_logo' src='{$logo_img}' />";
            break;
        }
    }
    $html.= "<input type='hidden' id='comment_level' value='".$current_level."' />\n";
    $html.= "<input type='hidden' id='comment_id' value='".$entity->parent->cid."' />\n";
    $html.= "<div class='submitted'><b>" . $full_name . " - " . date("d-m-Y H:i", $entity->parent->created). "</b></div>\n";
    $html.= "<div class='content'>".oc_comment_get_comment_body($entity->parent->cid)."</div>\n";
    $html.= "<div style='text-align: right !important' class='comment_toolbar'>\n";
    //Is current level allowed to comment ? if not then dont display comment count
    if($current_level < variable_get('oc_comment_max_reply_level', 1) && sizeof($entity->children) != 0)
    {
        $html.= '<a class="oc_comment_btn oc_comment_read_btn">'.sizeof($entity->children).' ' . t('comments').'</a>';
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
     
     $html.= "</div>\n";
     $html .= "<div class='oc-comment-form-box'></div>\n";
     $html .= "</div>";
     return $html;
}
function oc_comments_user_check_validation($msg = null,$required_role = null)
{
    if(!user_is_logged_in())
    {
        drupal_exit();
    }
}