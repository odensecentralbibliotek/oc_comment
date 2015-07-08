<?php

echo "<div id='oc-comments-wrap'>";
    /*
     * If user is not logged in , show the login button on top of the comments.
     */
    if(!user_is_logged_in())
    {
        $html .= "<div id='oc-comment-login-btn-wrap'>";
        $html .= l(t('Login To Comment'),'',array('attributes' => array('class' => 'oc_comment_large_login_btn btn btn-info')));
        $html .= "</div>";
    }
echo $wrap_data;
echo "</div>";

