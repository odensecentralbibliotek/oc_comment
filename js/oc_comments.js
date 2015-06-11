/* 
 * Handles all the javascript based logic for creating/replying and editing comments
 * for the oc custom comments build.
 */
jQuery( 'document' ).ready(function() {
 Init_buttons();
 ajax_pager();
});
function Init_buttons()
{
    jQuery('body').off('click');
    bindLoginajax();
    bindReplyajax();
    bindEditajax();
    bindDeleteajax();
    bind_readComments();
}
function toggle_spinner(selector,width,height,margin_left,margin_right)
{
    var spinnerUrl = Drupal.settings.basePath + "files/362.GIF";
    var spinner = jQuery('<img />');
    spinner.attr('src',spinnerUrl);
    spinner.css({
        'width': '50px',
        'height': '50px',
        'margin-left' : '46%'
    });
    jQuery(selector).html(spinner);
}
function ajax_pager()
{
    jQuery(document.body).on('click','.pager li a',function(e){
        toggle_spinner();
        //get the target page.
        var page = jQuery(e.currentTarget).attr('href');
        var page_number = getUrlParameter(page,'page');
        jQuery.ajax({
        method: "GET",
        url: '/oc/comments/ajax/get?page=' + page_number + "&ajax=1&nodeid="
            +Drupal.settings.oc_comment.currentNid
      })
        .done(function( msg ) {
            //retrive the new comment list and replace.
            jQuery('#oc_comments_wrap').replaceWith(msg.content).fadeIn("slow");
            jQuery("body").scrollTop(jQuery("#oc_comments_wrap").offset().top-200);
        });
       return false;     
    });
}
function bind_readComments()
{
    jQuery(document.body).on('click','.oc_comment_read_btn',function(e){
        var elem = jQuery(e.currentTarget).parent().parent(); // find the top div.
        var sibling = elem.next();
        //check if there are existing comments.
        if(sibling.hasClass('indented'))
        {
            if(sibling.is(':visible'))
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
   jQuery('.oc_comment_login_btn').off();
   jQuery('.oc_comment_login_btn').on('click',function(){
      //alert('Login box show.');
      jQuery.ajax({
        method: "GET",
        url: "/oc/comments/ajax_form/login"
      })
        .done(function( msg ) {
            debugger;
         //Show the Login in a dialog.
         var tmp = jQuery(msg);
         tmp.dialog({ title: "Login",
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
      jQuery('.oc_comment_reply_btn').on('click',function(e){
          
     jQuery.ajax({
        method: "GET",
        url: "/oc/comments/ajax_form/reply"
      })
        .done(function( msg ) {
          debugger;
         jQuery('#oc-comment-comment-ajax-reply-form').remove();
         var tmp = e.currentTarget.getAttribute('id');
         Drupal.settings.oc_comment.selected_comment = tmp;
         //Show the Login in a dialog.
         var tmp = jQuery(msg);
         tmp.dialog({ title: "Reply",
                      modal: true
               });
        });
        return false;
    });
    
    jQuery(document.body).on('click','#oc_comment_submit_reply_btn',function(e){
        jQuery('.ui-dialog').remove();
        //Get the current comment id being replied too.
        var node_id = Drupal.settings.oc_comment.currentNid; 
        var parentid = Drupal.settings.oc_comment.selected_comment;
        var comment = document.getElementById("edit-comment-message").value;

        //If id is -1 then we are creating a top-level comment.
        //This is safe as no comment id's are negative.
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/reply/submit/" + node_id +"/"+ parentid +"/" + comment + "/"
          })
            .done(function( msg ) {
                debugger;
                //alert(msg);
               //did we submit with success ?
                InsertCommentReply(msg);
               //If success inject the new comment @ correct place.
               jQuery('#oc-comment-comment-ajax-reply-form').dialog('close');
            });
        
        return false;
    });
}
function InsertCommentReply(comment)
{
    var pid = comment.pid;
    var cid = comment.cid;
    var elem = jQuery('#cid-' +pid);
    var sibling = elem.next();
    //check if there are existing comments.
    if(sibling.hasClass('indented'))
    {
        var new_elem = jQuery(comment.markup);
        new_elem.hide();
        //new_elem.toggle();
        sibling.append(new_elem);
        new_elem.fadeIn("slow");
        
    }
    else
    {
        var wrapper = jQuery('<div class="indented"></div>');
        wrapper.append(comment.markup);
        wrapper.hide();
        elem.after(wrapper);
        wrapper.fadeIn("slow");
    }
    Init_buttons();
}
function bindDeleteajax()
{
     //The button hook.
     jQuery('.oc_comment_delete_btn').off();
     jQuery('.oc_comment_delete_btn').on('click',function(e){
         debugger;
        jQuery.ajax({
          method: "GET",
          url: "/oc/comments/ajax_form/delete"
        })
          .done(function( msg ) {
            debugger;
           //Show the Login in a dialog.
           var tmp = e.currentTarget.getAttribute('id');
           Drupal.settings.oc_comment.selected_comment = tmp;
           var tmp = jQuery(msg);
           tmp.dialog({ title: "delete comment",
                     modal: true
                 });
          });
          return false;
    });
    //Dialog submit.
    jQuery(document.body).on('click','#oc_comment_submit_delete_confirm_btn',function(e){
      //clean up the dynamic dialogs.
      jQuery('.ui-dialog').remove();
      //Get the current comment id being replied too.
      var comment_edit_id = Drupal.settings.oc_comment.selected_comment;
      //If id is -1 then we are creating a top-level comment.
      //This is safe as no comment id's are negative.
      jQuery.ajax({
          method: "GET",
          url: "/oc/comments/ajax_form/delete/submit/" + comment_edit_id
        })
          .done(function( msg ) {
             //did we submit with success ?
             jQuery('#oc-comment-comment-ajax-delete-form').dialog('close');
             //If success inject the new comment @ correct place.
             var comment =  jQuery('#'+Drupal.settings.oc_comment.selected_comment).parent().parent();
             comment.fadeOut(900,function(){
                 jQuery('#'+Drupal.settings.oc_comment.selected_comment).parent().parent().remove();
             });
          });

      return false;
    });
}
function bindEditajax()
{
    //Add so the popup opens
      jQuery('.oc_comment_edit_btn').off();
      jQuery('.oc_comment_edit_btn').on('click',function(e){
         debugger;
        jQuery.ajax({
          method: "GET",
          url: "/oc/comments/ajax_form/edit"
        })
          .done(function( msg ) {
            debugger;
           //Show the Login in a dialog.
           var tmp = e.currentTarget.getAttribute('id');
           Drupal.settings.oc_comment.selected_comment = tmp;
           var tmp = jQuery(msg);
           tmp.dialog({ title: "Edit comment",
                     modal: true
                 });
          });
          return false;
    });
    //Add the submit btn handler.
      jQuery(document.body).on('click','#oc_comment_submit_edit_btn',function(e){
        //Get the current comment id being replied too.
         
        var comment_edit_id = Drupal.settings.oc_comment.selected_comment;
        var comment = jQuery('#edit-comment-message').val();
        //If id is -1 then we are creating a top-level comment.
        //This is safe as no comment id's are negative.
        jQuery.ajax({
            method: "GET",
            url: "/oc/comments/ajax_form/edit/submit/" + comment_edit_id + "/" +comment
          })
            .done(function( msg ) {
               //did we submit with success ?
               var tmp = jQuery('#cid-' + comment_edit_id);
               var tmp2 = tmp.find('.content');
               tmp2.text(comment);
               //If success inject the new comment @ correct place.
               jQuery('#oc-comment-comment-ajax-edit-form').dialog('close');
               jQuery('#oc-comment-comment-ajax-edit-form').remove();
            });
        
        return false;
    });
}
function getUrlParameter(url,sParam)
{
    debugger;
    var sPageURL = url.split('?')[1];
    if(sPageURL === undefined)
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


