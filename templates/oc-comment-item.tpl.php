<?php
/*
 * Handles rendering of a single comment , based on input from 
 * function render_single_comment_entity($entity, $current_level, $is_child = false)
 * $entity = the current comment object.
 * $current_level = the current level in the comment structure.
 * $is_child = shows if we are dealing with a child comment.
 */
    $html = "";
    $node = node_load($entity->parent->nid);
    $comment_user = user_load($entity->parent->uid);
    $full_name = isset($comment_user->data['display_name']) ? $comment_user->data['display_name'] : $comment_user->name;
    /*
     * Check if user is employee/admin and add logo to top right of comment box.
     * makes it all look more official :)
     */
    $site_admin_roles = array('administrator', 'redaktør');
    $wrap_classes = array();
    $wrap_classes[] = "comment";
    $wrap_classes[] = $entity->parent->status == 0 ? 'oc-comment-approval-required' : 'oc-comment-approved';
    $wrap_classes[] = ($is_child == true ? 'oc_comment_child' : 'oc_comment_parent');

    $html.= "<div id='cid-" . $entity->parent->cid . "' class='" . implode(' ', $wrap_classes) . "'>";
    foreach ($comment_user->roles as $roles) {
        if (in_array($roles, $site_admin_roles)) {
            $logo_img = variable_get('oc_comment_file_path', 'https://odensebib.dk/sites/www.odensebib.dk/files/logo.png');
            $html .= "<img title='Biblioteks ansat' id='comment_logo' src='{$logo_img}' />";
            break;
        }
    }
    $html.= "<input type='hidden' id='comment_parent' value='" . $entity->parent->pid . "' />\n";
    $html.= "<input type='hidden' id='comment_level' value='" . $current_level . "' />\n";
    $html.= "<input type='hidden' id='comment_id' value='" . $entity->parent->cid . "' />\n";
    $html.= "<input type='hidden' id='comment_count' value='" . sizeof($entity->children) . "' />\n";
    $html.= "<div class='submitted'><b>" . $full_name . "</b> - " . date("d-m-Y H:i", $entity->parent->created) . "</div>\n";
    $html.= "<div class='comment_content'>";
    if(variable_get('comment_subject_field_' . $node->type, 0))
    {
        $html.= "<h2>" . $entity->parent->subject . "</h2>";
    }
    $html.=  oc_comment_get_comment_body($entity->parent->cid);
    $html.=  "</div>\n";
    $html.= "<div style='text-align: right !important' class='comment_toolbar'>\n";
    //Is current level allowed to comment ? if not then dont display comment count
    if ($current_level < variable_get('oc_comment_max_reply_level_' . $node->type, 1) && sizeof($entity->children) != 0) {
        $html.= '<a class="oc_comment_btn oc_comment_read_btn">' . sizeof($entity->children) . ' ' . t('comments') . '</a>';
    }
    if ($entity->parent->pid == 0) {
        $html .= oc_comment_get_buttons($entity->parent, $current_level,null,$node);
    } else {
        //reassign here to avoid strict warning. php dont like too many statements together
        $parent_entity = entity_load('comment', array($entity->parent->pid));
        $parent = reset($parent_entity);
        $tmp = $entity;
        $html .= oc_comment_get_buttons($tmp->parent, $current_level, $parent,$node);
    }

    $html.= "</div>\n";
    $html .= "<div class='oc-comment-form-box'></div>\n";
    $html .= "</div>";
    echo $html;

