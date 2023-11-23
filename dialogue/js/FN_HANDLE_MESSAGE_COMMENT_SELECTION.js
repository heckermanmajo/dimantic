window.MESSAGE_TEXT_SELECTION = {}

/**
 * This function is used to keep track what text is selected in a dialogue message.
 * This allows us to create comments on a specific text selection.
 *
 * @see cls/data/dialogue/DialogueMessage::get_message_html()
 */
function FN_HANDLE_UPDATE_TEXT_SELECTION(
  message_id
) {
  let selection_text = window.getSelection().toString();
  if (selection_text !== "") {
    if (selection_text === window.MESSAGE_TEXT_SELECTION.text) {
      return;
    }
    window.MESSAGE_TEXT_SELECTION = {
      "message_id": message_id,
      "text": selection_text
    }
    console.log(window.MESSAGE_TEXT_SELECTION)

    // display the creation comment box
    let css_id_of_div = "create_comment_from_selection_" + message_id;
    let css_id_of_textarea = "create_comment_from_selection_" + message_id + "_textarea";
    let css_id_of_span = "create_comment_from_selection_" + message_id + "_span";
    let css_id_of_hidden_input = "create_comment_from_selection_" + message_id + "_hidden_input";
    document.getElementById(css_id_of_div).style.display = "block";
    document.getElementById(css_id_of_span).innerHTML = selection_text;
    document.getElementById(css_id_of_hidden_input).value = selection_text;
  }
}