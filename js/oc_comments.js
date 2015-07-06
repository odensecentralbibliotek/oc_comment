/* 
 * Handles all the javascript based logic for creating/replying and editing comments
 * for the oc custom comments build.
 */
jQuery('document').ready(function () {
    Init_buttons();
    ajax_pager();
});
function Init_buttons()
{
    //jQuery('body').off('click');
    bindLoginajax();
    bind_form_submit();
    bindReplyajax();
    bindEditajax();
    bindDeleteajax();
    bindApproveajax();
    bind_readComments();
}
function toggle_spinner(selector, width, height, margin_left, margin_right)
{
    var spinnerUrl = Drupal.settings.basePath + "files/362.GIF";
    var spinner = jQuery('<img />');
    spinner.attr('src', spinnerUrl);
    spinner.css({
        'width': '50px',
        'height': '50px',
        'margin-left': '46%'
    });
    jQuery(selector).html(spinner);
}
function bind_form_submit()
{
    jQuery(document.body).on('click','#oc_comment_submit_form_btn', function (e) {
        jQuery(".submit-form-error-message").empty();
        var node_id = Drupal.settings.oc_comment.currentNid;
        var parentid = -1;
        var comment = document.getElementById("reply_comment_submit_message").value;
        var comment_level = 0;
        var comment_subject = jQuery('#reply_comment_submit_subject').val();
        //If id is -1 then we are creating a top-level comment.
        //This is safe as no comment id's are negative.
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/reply/submit/" + node_id + "/" + parentid + "/" + comment + "/"
                    + Drupal.settings.oc_comment.selected_comment_level +"/"+comment_subject
        })
                .done(function (msg) {
                    //insert the created comment.
                    jQuery('#oc-comments-wrap').prepend(msg.markup);
                    if(Drupal.settings.oc_comment.skip_approval)
                    {
                        jQuery(".submit-form-error-message").append('<div class="messages status">Comment Postet</div>');
                     
     
                    }
                    else
                    {
                        jQuery(".submit-form-error-message").append('<div class="messages status">Comment awaiting admin approval</div>');
                    }
                    jQuery('#reply_comment_submit_subject').val(null);
                    jQuery('#reply_comment_submit_message').val(null);
                });

        return false;
    
    
    });
}
function ajax_pager()
{
    jQuery(document.body).on('click', '.pager li a', function (e) {
        jQuery(".submit-form-error-message").empty();
        toggle_spinner();
        //get the target page.
        var page = jQuery(e.currentTarget).attr('href');
        var page_number = getUrlParameter(page, 'page');
        jQuery.ajax({
            method: "GET",
            url: '/oc/comments/ajax/get?page=' + page_number + "&ajax=1&nodeid="
                    + Drupal.settings.oc_comment.currentNid
        })
                .done(function (msg) {
                    //retrive the new comment list and replace.
                    jQuery('#oc-comments-wrap').replaceWith(msg.content).fadeIn("slow");
                    jQuery("body,html").scrollTop(jQuery("#oc-comments-wrap").offset().top - 200);
                });
        return false;
    });
}
function bind_readComments()
{
    jQuery(document.body).on('click', '.oc_comment_read_btn', function (e) {
        jQuery(".submit-form-error-message").empty();
        var elem = jQuery(e.currentTarget).parent().parent(); // find the top div.
        var sibling = elem.next();
        //check if there are existing comments.
        if (sibling.hasClass('indented'))
        {
            if (sibling.is(':visible'))
            {
                sibling.fadeOut("slow");
            }
            else
            {
                sibling.fadeIn("slow");
            }

        }
    });
}
function bindLoginajax()
{
    jQuery('.oc_comment_large_login_btn').off();
    jQuery(document.body).on('click','.oc_comment_large_login_btn', function () {
        jQuery(".submit-form-error-message").empty();
        //alert('Login box show.');
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/login"
        })
                .done(function (msg) {
                    //Show the Login in a dialog.
                    var tmp = jQuery(msg);
                    tmp.dialog({title: "Login",
                        modal: true
                    });
                });
        return false;
    });
    return false;
}
function bindReplyajax()
{
    jQuery('.oc_comment_reply_btn').off('click');
    jQuery(document.body).on('click','.oc_comment_reply_btn', function (e) {
        jQuery(".submit-form-error-message").empty();
        if (jQuery('#oc-comments-wrap #oc_comment_submit_reply_btn').is(':visible'))
        {
            var formbox = jQuery(e.currentTarget).parent().parent().find('.oc-comment-form-box');
            formbox.fadeOut("slow");
            return false;
        }
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/reply/" + Drupal.settings.oc_comment.currentNid
        })
                .done(function (msg) {
                    jQuery('#oc-comment-comment-ajax-reply-form').remove();
                    var tmp = e.currentTarget.getAttribute('id');
                    var level = jQuery(e.currentTarget).parent().parent().find('#comment_level');
                    Drupal.settings.oc_comment.selected_comment = tmp;
                    Drupal.settings.oc_comment.selected_comment_level = parseInt(level.val()) + 1;
                    //Show the Login in a dialog.
                    var tmp = jQuery(msg);
                    var formbox = jQuery(e.currentTarget).parent().parent().find('.oc-comment-form-box');
                    formbox.html(msg);
                    formbox.fadeIn("slow");
                });
        return false;
    });

    jQuery(document.body).on('click', '#oc_comment_submit_reply_btn', function (e) {
        debugger;
        
        //jQuery('.ui-dialog').remove();
        //Get the current comment id being replied too.
        var node_id = Drupal.settings.oc_comment.currentNid;
        var parentid = Drupal.settings.oc_comment.selected_comment;
        var comment = document.getElementById("reply_comment_message").value;
        var comment_level = "";
        var comment_subject = jQuery('#reply_comment_subject').val();
        //If id is -1 then we are creating a top-level comment.
        //This is safe as no comment id's are negative.
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/reply/submit/" + node_id + "/" + parentid + "/" + comment + "/"
                    + Drupal.settings.oc_comment.selected_comment_level +"/"+comment_subject
        })
                .done(function (msg) {
                    //did we submit with success ?
                    var comment = jQuery('#cid-' + Drupal.settings.oc_comment.selected_comment);
                    var formbox = comment.find('.oc-comment-form-box');
                    formbox.fadeOut("slow");
                    InsertCommentReply(msg);
                });

        return false;
    });
}
function InsertCommentReply(comment)
{
        var pid = comment.pid;
        var cid = comment.cid;
        var elem = jQuery('#cid-' + pid);
        var sibling = elem.next();
        debugger;
        //check if there are existing comments.
        if (sibling.hasClass('indented'))
        {
            var new_elem = jQuery(comment.markup);
            new_elem.hide();
            //new_elem.toggle();
            sibling.append(new_elem);
            if (sibling.is(':hidden'))
            {
                sibling.fadeIn("slow");
            }
            new_elem.fadeIn("slow");
            jQuery("body,html").scrollTop(new_elem.offset().top - 200);
            new_elem.pulsate({
                reach: 20, // how far the pulse goes in px
                speed: 1000, // how long one pulse takes in ms
                pause: 0, // how long the pause between pulses is in ms
                glow: true, // if the glow should be shown too
                repeat: 1, // will repeat forever if true, if given a number will repeat for that many times
                onHover: false                          // if true only pulsate if user hovers over the element
            });
        }
        else
        {
            var wrapper = jQuery('<div class="indented"></div>');
            wrapper.append(comment.markup);
            wrapper.hide();
            elem.after(wrapper);
            wrapper.fadeIn("slow");
        }
    if(Drupal.settings.oc_comment.skip_approval){
         jQuery(".submit-form-error-message").append('<div class="messages status">Comment Postet</div>');
    }
    else
    {
        jQuery(".submit-form-error-message").append('<div class="messages status">Comment awaiting admin approval</div>');
        jQuery(".submit-form-error-message").focus();
    }
    
    //Init_buttons();
}
function bindDeleteajax()
{
    //The button hook.
    jQuery('.oc_comment_delete_btn').off();
    jQuery(document.body).on('click','.oc_comment_delete_btn', function (e) {
        jQuery(".submit-form-error-message").empty();
        if (jQuery('#oc_comment_submit_delete_confirm_btn').is(':visible'))
        {
            var formbox = jQuery(e.currentTarget).parent().parent().find('.oc-comment-form-box');
            formbox.fadeOut("slow");
            return false;
        }
        //clean up the dynamic dialogs.
        debugger;
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/delete"
        })
                .done(function (msg) {

                    debugger;
                    //Show the Login in a dialog.
                    var tmp = e.currentTarget.getAttribute('id');
                    Drupal.settings.oc_comment.selected_comment = tmp;
                    var tmp = jQuery(msg);
                    var formbox = jQuery(e.currentTarget).parent().parent().find('.oc-comment-form-box');
                    formbox.html(msg);
                    formbox.fadeIn("slow");

                });
        return false;
    });
    //Dialog submit.
    jQuery(document.body).on('click', '#oc_comment_submit_delete_confirm_btn', function (e) {
        

        //Get the current comment id being replied too.
        var comment_edit_id = Drupal.settings.oc_comment.selected_comment;
        //If id is -1 then we are creating a top-level comment.
        //This is safe as no comment id's are negative.
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/delete/submit/" + comment_edit_id
        })
                .done(function (msg) {
                    //did we submit with success ?
                    jQuery('#oc-comment-comment-ajax-delete-form').dialog('close');
                    var comment = jQuery('#cid-' + Drupal.settings.oc_comment.selected_comment);
                    var formbox = comment.find('.oc-comment-form-box');
                    formbox.fadeOut("slow");
                    //If success inject the new comment @ correct place.
                    var comment = jQuery('#' + Drupal.settings.oc_comment.selected_comment).parent().parent();
                    comment.fadeOut(900, function () {
                        jQuery('#' + Drupal.settings.oc_comment.selected_comment).parent().parent().remove();
                    });
                });

        return false;
    });
}
function bindEditajax()
{
    //Add so the popup opens
    //jQuery('.oc_comment_edit_btn').off();
    jQuery(document.body ).on('click','.oc_comment_edit_btn', function (e) {
        jQuery(".submit-form-error-message").empty();
        if (jQuery('#edit_comment_message').is(':visible'))
        {
            var formbox = jQuery(e.currentTarget).parent().parent().find('.oc-comment-form-box');
            formbox.fadeOut("slow");
            return false;
        }
        debugger;
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/edit"
        })
                .done(function (msg) {
                    jQuery('#oc-comment-comment-ajax-edit-form').remove();
                    //Show the Login in a dialog.
                    var tmp = e.currentTarget.getAttribute('id');
                    var old_text = jQuery(e.currentTarget).parent().parent().find('.content').text();
                    Drupal.settings.oc_comment.selected_comment = tmp;
                    Drupal.settings.oc_comment.selected_comment_old_text = old_text;
                    var tmp = jQuery(msg);
                    var formbox = jQuery(e.currentTarget).parent().parent().find('.oc-comment-form-box');
                    formbox.html(msg);
                    formbox.fadeIn("slow");
                    var tmp = jQuery('#edit_comment_message');
                    tmp.val(old_text);

                });
        return false;
    });
    //Add the submit btn handler.
    jQuery(document.body).on('click', '#oc_comment_submit_edit_btn', function (e) {
        //Get the current comment id being replied too.
        var comment_edit_id = Drupal.settings.oc_comment.selected_comment;
        var comment = jQuery('#edit_comment_message').val();
        //If id is -1 then we are creating a top-level comment.
        //This is safe as no comment id's are negative.
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/edit/submit/" + comment_edit_id + "/" + comment
        })
                .done(function (msg) {
                    //did we submit with success ?
                    var tmp = jQuery('#cid-' + comment_edit_id);
                    var tmp2 = tmp.find('.content');
                    tmp2.text(msg.comment_body.und[0].value);

                    var comment = tmp;
                    var formbox = comment.find('.oc-comment-form-box');
                    formbox.fadeOut("slow");
                });

        return false;
    });
}
function bindApproveajax()
{
    jQuery(document.body).on('click','.oc_comment_approve_btn', function (e) {
        jQuery(".submit-form-error-message").empty();
        var cid = jQuery(e.currentTarget).parent().parent().find('#comment_id').val();
        //are you sure ?
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax/approve/" + cid
        }).done(function (msg) {
            debugger;
            //hide button and somehow show success
            var replybtn = jQuery('<a href="/" class="oc_comment_reply_btn oc_comment_btn">Reply</a>');
            replybtn.prop('id', cid);
            jQuery('#cid-' + cid).find('.oc_comment_approve_btn').replaceWith(replybtn);
            jQuery('#cid-' + cid).css('background-color', 'white');
        });
        //start approving.
        return false;
    });
}
function getUrlParameter(url, sParam)
{
    var sPageURL = url.split('?')[1];
    if (sPageURL === undefined)
    {
        //no Page params found , return first page.
        return 0;
    }
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    }
}


