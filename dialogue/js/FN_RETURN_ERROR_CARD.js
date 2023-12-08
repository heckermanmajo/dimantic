/**
 *
 * @param json_protocol_feedback
 * @returns {string}
 *
 * @see cls\Protocol
 * @see cls\RequestError::return_json_protocol
 *
 * @example
 *     // php code of return_json_protocol
 *     return json_encode([
 *       "success" => false,
 *       "error" => [
 *         "code" => $this->code,
 *         "dev_message" => $this->dev_message,
 *         "user_message" => $this->user_message,
 *         "extra_data" => $this->extra_data,
 *       ],
 *       "logs" => FN_IS_DEBUG() ? App::get_logs() : [],
 *     ]);
 */
function FN_RETURN_ERROR_CARD(json_protocol_feedback){
  if(json_protocol_feedback.success === true){
    return "";
  }
  var error = json_protocol_feedback.error;
  var logs = json_protocol_feedback.logs;
  var html = "";
  html += `<div class="w3-card-4 w3-margin w3-padding" style="border-color: orangered !important;">`;
  html += `<h3 class="w3-text-red">Error</h3>`;
  html += `<p class="w3-text-red">`;
  html += `  <b>Code:</b> ${error.code}<br>`;
  html += `  <b>Dev Message:</b> ${error.dev_message}<br>`;
  html += `  <b>User Message:</b> ${error.user_message}<br>`;
  html += `</p>`;
  let log_html = "<pre style='font-size: 11px'>";
  for (let i = 0; i < logs.length; i++) {
    let log_string = logs[i];
    if (log_string.trim().startsWith("(err)")) {
      log_string = "<span style='color: red'>" + log_string + "</span>";
    }
    if (log_string.trim().startsWith("(warn)")) {
      log_string = "<span style='color: #e0bb00'>" + log_string + "</span>";
    }
    if (log_string.trim().startsWith("(todo)")) {
      log_string = "<span style='color: #00a2da'>" + log_string + "</span>";
    }
    if(log_string.trim().startsWith("&[")){
      log_string = "<small><b style='color: #0016ff'>" + log_string + "</b></small>";
    }
    log_html += "<br>" + log_string;
  }
  log_html += "</pre>";

  html += log_html;
  html += `</div>`;
  return html;
}