<?php
/**
 * @file
 * oc_comments_rules_features.rules_defaults.inc
 */

/**
 * Implements hook_default_rules_configuration().
 */
function oc_comments_rules_features_default_rules_configuration() {
  $items = array();
  $items['rules_comment_delete'] = entity_import('rules_config', '{ "rules_comment_delete" : {
      "LABEL" : "Comment Delete",
      "PLUGIN" : "reaction rule",
      "REQUIRES" : [ "rules", "cache_actions", "comment" ],
      "ON" : [ "comment_delete" ],
      "DO" : [
        { "cache_actions_action_clear_cache_cid" : {
            "bin" : "cache",
            "cid" : "oc_comments_data_[comment:node:nid]",
            "wildcard" : 0
          }
        }
      ]
    }
  }');
  $items['rules_comment_publish'] = entity_import('rules_config', '{ "rules_comment_publish" : {
      "LABEL" : "comment publish",
      "PLUGIN" : "reaction rule",
      "REQUIRES" : [ "rules", "cache_actions", "comment" ],
      "ON" : [ "comment_update" ],
      "IF" : [
        { "data_is" : { "data" : [ "comment:status" ], "value" : "1" } },
        { "data_is" : { "data" : [ "comment-unchanged:status" ], "value" : "0" } }
      ],
      "DO" : [
        { "drupal_message" : { "message" : "Publiceret node er k\\u00f8rt." } },
        { "mail" : {
            "to" : "tfpet@odense.dk",
            "subject" : "Din Kommentar er udgivet",
            "message" : "Din kommentar til \\u003Cb\\u003E\\u003Ca href=\\u0022[comment:url]\\u0022 target=\\u0022_blank\\u0022\\u003E[entity-fetched:title]\\u003C\\/a\\u003E\\u003C\\/b\\u003E er blevet udgivet\\u003Cbr\\/\\u003E\\r\\nDu kan rediger kommentaren direkte \\u003Ca href=\\u0022[comment:edit-url]\\u0022\\u003Eher\\u003C\\/a\\u003E eller p\\u00e5 indholds siden direkte.\\r\\n\\r\\nVenlig hilsen\\r\\n[site:name]",
            "from" : "system@odense.dk",
            "language" : [ "" ]
          }
        },
        { "cache_actions_action_clear_cache_cid" : {
            "bin" : "cache",
            "cid" : "oc_comments_data_[comment:node:nid]",
            "wildcard" : 0
          }
        }
      ]
    }
  }');
  $items['rules_comment_unpublish'] = entity_import('rules_config', '{ "rules_comment_unpublish" : {
      "LABEL" : "comment unpublish",
      "PLUGIN" : "reaction rule",
      "REQUIRES" : [ "rules", "cache_actions", "comment" ],
      "ON" : [ "comment_update" ],
      "IF" : [
        { "data_is" : { "data" : [ "comment-unchanged:status" ], "value" : "1" } },
        { "data_is" : { "data" : [ "comment:status" ], "value" : "0" } }
      ],
      "DO" : [
        { "cache_actions_action_clear_cache_cid" : {
            "bin" : "cache",
            "cid" : "oc_comments_data_[comment:node:nid]",
            "wildcard" : 0
          }
        }
      ]
    }
  }');
  $items['rules_new_comment'] = entity_import('rules_config', '{ "rules_new_comment" : {
      "LABEL" : "New Comment",
      "PLUGIN" : "reaction rule",
      "REQUIRES" : [ "rules", "comment" ],
      "ON" : [ "comment_insert" ],
      "DO" : [
        { "entity_fetch" : {
            "USING" : { "type" : "node", "id" : [ "comment:node:nid" ] },
            "PROVIDE" : { "entity_fetched" : { "entity_fetched" : "Fetched entity" } }
          }
        },
        { "entity_fetch" : {
            "USING" : { "type" : "user", "id" : [ "entity-fetched:author:uid" ] },
            "PROVIDE" : { "entity_fetched" : { "user_entity" : "User_entity" } }
          }
        },
        { "mail" : {
            "to" : "[user-entity:mail]",
            "subject" : "Ny kommentar",
            "message" : "Der er kommet en ny kommentar til \\u003Cb\\u003E\\u003Ca href=\\u0022[comment:url]\\u0022 target=\\u0022_blank\\u0022\\u003E[entity-fetched:title]\\u003C\\/a\\u003E\\u003C\\/b\\u003E\\u003Cbr\\/\\u003E\\r\\n\\u003Cb\\u003E[comment:name]\\u003C\\/b\\u003E Skriver:\\r\\n\\u003Ch2\\u003E[comment:title]\\u003C\\/h2\\u003E\\r\\n\\u003Cp\\u003E\\r\\n\\u0022[comment:body]\\u0022\\r\\n\\u003Cp\\u003E\\r\\n\\u003Cbr\\/\\u003E\\r\\nDu kan rediger kommentaren direkte \\u003Ca href=\\u0022[comment:edit-url]\\u0022\\u003Eher\\u003C\\/a\\u003E eller p\\u00e5 indholds siden direkte.",
            "language" : [ "" ]
          }
        }
      ]
    }
  }');
  return $items;
}
