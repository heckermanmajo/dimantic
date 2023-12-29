window.MESSAGE_TEXT_SELECTION = {}

/**
 * This function is used to keep track what text is selected in a dialogue message.
 * This allows us to create comments on a specific text selection
 * or like the text selection.
 *
 * @see cls/data/dialogue/DialogueMessage::get_message_html()
 */
function FN_HANDLE_UPDATE_TEXT_SELECTION(
  message_id,
  max_number_of_characters_to_like
) {
  // This function is called "onmousemove"
  // therefore we need to ignore empty selection text
  // otherwise we would lose the selection just by moving the mouse
  // over another message
  let selection_text = window.getSelection().toString();
  if (selection_text !== "") {
    if (selection_text === window.MESSAGE_TEXT_SELECTION.text) {
      return; // don't do anything if the selection is the same
    }
    // else update the selection
    window.MESSAGE_TEXT_SELECTION = {
      "message_id": message_id,
      "text": selection_text
    }
    console.log(window.MESSAGE_TEXT_SELECTION)

    ////////////////////////////////////////
    // COMMENT RELATED
    ////////////////////////////////////////

    // display the creation comment box
    let css_id_of_div = "create_comment_from_selection_" + message_id;
    // span to display the selected text above the comment form
    let css_id_of_span = "create_comment_from_selection_" + message_id + "_span";
    // hidden input for the comment form
    let css_id_of_hidden_input = "create_comment_from_selection_" + message_id + "_hidden_input";
    document.getElementById(css_id_of_div).style.display = "block";
    document.getElementById(css_id_of_span).innerHTML = selection_text;
    document.getElementById(css_id_of_hidden_input).value = selection_text;

    ////////////////////////////////////////
    // LIKE RELATED
    ////////////////////////////////////////
    let like_selection_hidden_input = "like_selection_" + message_id + "_hidden_input";
    document.getElementById(like_selection_hidden_input).value = selection_text;

    // if selection is too long, don't allow to like it
    let like_selection_form = "like_form_message_" + message_id;
    let like_selection_error_div = "like_error_div_" + message_id;
    let selection_is_too_long = selection_text.length > max_number_of_characters_to_like;
    let len_of_selected_text_in_like_form_span = "len_of_selected_text_in_like_form_" + message_id;
    let cost_of_like_of_selected_text_div = "cost_of_like_of_selected_text_" + message_id;
    document.getElementById(cost_of_like_of_selected_text_div).innerHTML = selection_text.length.toString();
    document.getElementById(len_of_selected_text_in_like_form_span).innerHTML = selection_text.length.toString()
    if(selection_is_too_long) {
      document.getElementById(like_selection_form).style.display = "none";
      document.getElementById(like_selection_error_div).style.display = "block";
    } else {
      document.getElementById(like_selection_form).style.display = "block";
      document.getElementById(like_selection_error_div).style.display = "none";
    }
  }
}