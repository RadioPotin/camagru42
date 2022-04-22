// submit_comment button
let submit_comment_btns = document.getElementsByClassName('submit_comment');
// display_comments button
let display_comments_btns = document.getElementsByClassName('display_comments');
// like button
let like_btns = document.getElementsByClassName('like');

// Do not run if no button is found
if (submit_comment_btns != null)
{
  // this part handles the comment submission process
  // Each pic will have a comment section and a form
  // that logged in users may fill to comment the art
  function sendCommentToPhp(text, img_id, author, token)
  {
    let entext = encodeURIComponent(text);
    let enimg_id = encodeURIComponent(img_id);
    let enauthor = encodeURIComponent(author);
    let entoken = encodeURIComponent(token);

    let params = "comment=" + entext + "&img_id=" + enimg_id + "&author=" + enauthor + "&token=" + entoken + "&submit=submit";

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

if (like_btns != null)
{
  function sendLikeToPhp(img_id, liker, token) {
    let enimg_id = encodeURIComponent(img_id);
    let enliker = encodeURIComponent(liker);
    let entoken = encodeURIComponent(token);

    let params = "img_id=" + enimg_id + "&liker=" + enliker + "&token=" + entoken + "&submit=submit";

    let httpRequest = new XMLHttpRequest();
    httpRequest.open('POST', 'savelike.php', true);
    httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    httpRequest.send(params);
    return;
  }

  function like_unlike_pic(e, i) {
    let btns = document.getElementsByClassName("like");
    let like_btn_img_id = document.getElementsByName('imgidl');
    let token_field = document.getElementsByName('token');

    // change display of like button
    if (btns[i].innerHTML === "LIKE") {
      btns[i].innerHTML = "UNLIKE";
    } else {
      btns[i].innerHTML = "LIKE";
    }

    // get specific info on like
    let imgid = like_btn_img_id[i].value;
    let token = token_field[i].value;
    let liker = e.target.value;

    sendLikeToPhp(imgid, liker, token);
  }

  function handleLikeEvent(i) {
    return function(e) {
      like_unlike_pic(e, i);
    };
  }

  for (var i = 0; i < like_btns.length; i++) {
    like_btns[i].addEventListener('click', handleLikeEvent(i), false);
  }

}
