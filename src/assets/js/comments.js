// submit_comment button
let submit_comment_btns = document.getElementsByClassName('submit_comment');
// submit_comment button
let display_comments_btns = document.getElementsByClassName('display_comments');

// Do not run if no button is found
if (submit_comment_btns != null)
{
  // this part handles the comment submission process
  // Each pic will have a comment section and a form
  // that logged in users may fill to comment the art
  function sendCommentToPhp(text, img_id, author, token)
  {
    // TODO URL ENCODING
    let params = "comment=" + text + "&img_id=" + img_id + "&author=" + author + "&token=" + token + "&submit=submit";

    let httpRequest = new XMLHttpRequest();
    httpRequest.open('POST', 'savecomment.php', true);
    httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    httpRequest.send(params);
    return;
  }

  // When user clicks on submit comment to add comment under img
  //
  function submit_comment(e, i) {
    e.preventDefault();
    let img_id_field = document.getElementsByClassName('img_id');
    let textarea = document.getElementsByClassName('comment_text');
    let token_field = document.getElementsByName('token');

    let img_id = img_id_field[i].value;
    let token = token_field[i].value;
    let author = e.target.value;
    let comment_text = textarea[i].value;

    if (comment_text === "" ) {
      return;
    }

    sendCommentToPhp(comment_text, img_id, author, token);
  }

  function handleSubmissionEvent(i) {
    return function(e) {
      submit_comment(e, i);
    };
  }

  for (var i = 0; i < submit_comment_btns.length; i++) {
    submit_comment_btns[i].addEventListener('click', handleSubmissionEvent(i), false);
  }
}

// Do not run if no button is found
if (display_comments_btns != null)
{
  // COMMENT SECTION DISPLAY
  function hide_show_comment_section(e, i) {
    let sections = document.getElementsByClassName("comment_section");
    let btns = document.getElementsByClassName("display_comments");
    if (sections[i].style.display === "none") {
      sections[i].style.display = "block";
      btns[i].innerHTML = "Hide Comments";
    } else {
      sections[i].style.display = "none";
      btns[i].innerHTML = "Display Comments";
    }
  }

  function handleCommentSectionDisplayEvent(i) {
    return function(e) {
      hide_show_comment_section(e, i);
    };
  }

    for (var i = 0; i < display_comments_btns.length; i++) {
      display_comments_btns[i].addEventListener('click', handleCommentSectionDisplayEvent(i), false);
    }
}
