/**
 * Observe a post/tree -> This function is called from the observe button
 * at the top header of a post or tree.
 * It observes or un-observes a post, based on the observe status of the post.
 * @param id The id of the post (a number as a string, f.e. "134")
 * @param type The type of the content ("post" or "tree")
 * @param btn The button element that was clicked - given via "this" in the onclick event
 *
 * @todo: add an error handler that shows an error message to the user in case of an error
 *
 * @see '/ajax/observe.php'
 */
function observe(id, type, btn) {
  $.post(
    '/ajax/observe.php',
    {
      id: id,
      type: type
    },
    // on success
    function (data) {
      console.log(data);
      btn.parentElement.innerHTML = data;
    }
  ).fail(function (data) {
    alert(data.responseText);
  })
}