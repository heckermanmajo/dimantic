/**
 * This class acts as a helper to ensure the correct
 * structure of the data that is passed to the tree function
 * and used in the callback functions.
 *
 * @see cls/data/Node::get_me_as_json_for_js_tree()
 */
class OriginalNodeData {
  constructor(data) {
    this.ref = data.ref;
    this.db_id = data.id;
  }
}

/**
 * The JS-Tree.
 * @see https://www.jstree.com/
 *
 * @param css_id The css id of the Tree element (cls/data/Tree::get_css_id_for_expanded_tree())
 * @param tree_type_as_string cls/data/Tree::$type, later on trees can also be conversations
 * @param current_user_is_owner author_id == current_user_id
 * @param current_user_is_member  currently not implemented
 * @param initial_data json_encode($root_node->get_me_as_json_for_js_tree(), JSON_PRETTY_PRINT)
 *
 * @see cls/data/Node::get_me_as_json_for_js_tree()
 */
function tree(
  css_id,
  tree_type_as_string,
  current_user_is_owner,
  current_user_is_member,
  initial_data
) {

  const TREE = $('#' + css_id)

  TREE.jstree({

    'core': {

      "themes": {
        "variant": "large"
      },

      data: initial_data,

      // Executed on every action - currently not used, later maybe
      'check_callback': function (operation, node, node_parent, node_position, more) {
        //  if (operation === 'move_node') {
        //   return true;
        //}
        return true;
      },

    },

    'plugins': [
      'themes',         // allows to make the tree bigger
      'dnd',            // drag and drop
      'contextmenu',    // context menu on right click -> add node functionality
      'state'           // save the state of the tree between page reloads
    ],

    'contextmenu': {
      'items':
        /**
         * This function is called when the user right-clicks on a node.
         * It returns the context menu options for the clicked node.
         */
        function (node) {

          let context_menu_options = {};

          context_menu_options["NewsNodeEmpty"] = {
            // css class of the menu item
            "_class": "tree_menu_item",
            // the label of the menu item -> the text that is displayed
            "label": (
              "<span class='label_text_context_menu_tree'>" +
              "Create new Node with empty content" +
              "</span>"
            ),


            "action": function (obj) {

              let original_node_data = new OriginalNodeData(node.original);

              $.post({
                url: "/ajax/create_node_with_empty_post.php",
                data: {
                  parent_node_id: original_node_data.db_id,
                },
                success: function (data) {
                  if (data != "") {
                    alert("Success: " + data)
                  }
                  window.location.reload();
                }
              }).fail(function (jqXHR, textStatus, errorThrown) {
                alert("Error: " + errorThrown);
              });
            }
          }

          context_menu_options["NewNode"] = {
            "_class": "tree_menu_item",
            "label": "<span class='label_text_context_menu_tree'>Create new Node based on existing Post</span>",
            "action": function (obj) {
              let original_node_data = new OriginalNodeData(node.original);
              $.post({
                url: "/ajax/select_post_for_new_node.php",
                data: {
                  parent_node_id: original_node_data.db_id,
                },
                success: function (data) {
                  // this is currently a quickfix for not really working overlays
                  $('#tree_view_content').hide();
                  $('#select_posts_for_new_node').html(data);
                  $('#select_posts_for_new_node').show();
                }
              }).fail(function (jqXHR, textStatus, errorThrown) {
                alert("Error: " + errorThrown);
              });
            }
          }

          return context_menu_options;

        }
    },
  }); // end of jstree


  // on move -> when you drag and drop a node
  TREE.on('move_node.jstree', function (e, data) {
    let myNode = new OriginalNodeData(data.node.original);
    $.post(
      {
        url: "/ajax/handle_move_node.php",
        data: {
          id: myNode.db_id,
          new_parent_id: data.parent,
          new_position_id: data.position,
        },
        success: function (data) {
          console.log("Success" + data);
        }
      }
    ).fail(function (jqXHR, textStatus, errorThrown) {
      alert("Error: " + errorThrown);
    });
  });


  // on select -> when you click on a node
  // one node is always selected by default
  TREE.on('select_node.jstree', function (e, data) {
    let myNode = new OriginalNodeData(data.node.original);
    $.ajax(
      {
        url: "/ajax/get_post_read_view.php",
        data: {
          id: myNode.ref,
        },
        success: function (data) {
          $('#right_tree_details').html(data);
          console.log("Success" + data);
        }
      }
    ).fail(function (jqXHR, textStatus, errorThrown) {
      alert("Error: " + errorThrown);
    });
  });

}