<?php
include_once 'include.php';
include_once 'dbh.php';
include_once 'lib.php';
include_once 'user.php';

//if you did, but token does NOT match
if (!isset($_SESSION["user"]) && !isset($_SESSION["username"])) {
    // TODO PROPRE ERROR PAGE
    err("<h1>You need to register or log in !</h1>");
    exit;
} else {
    $pic_limit = 5;
    $pagenum = 1;

    if (isset($_GET["page"])) {
        $pagenum = $_GET["page"];
    }
    // counting the offset
    $offset = ($pagenum - 1) * $pic_limit;

    // new instance of given user object
    $username = $_SESSION["username"];
    $email = $_SESSION["email"];
    $user = new User($username, "", $email);
    $gallery = $user->fetch_pagination_elements_from_given_user($offset, $pic_limit);

    if ($gallery !== null) {
        $body = output_gallery($gallery);
    } else {
        $body = "<h1>nothing here, pal, try taking a pic or two</h1>";
        $pagenum = 0;
    }

    // how many rows we have in table
    $rownum = $user->count_img_entries_of_user();

    // how many pages we have when using paging
    $maxpage = ceil($rownum/$pic_limit);

    // print the link to access each page
    $self = $_SERVER['PHP_SELF'];

    // creating previous and next link
    // plus the link to go straight to
    // the first and last page

    if ($pagenum > 1) {
        $page  = $pagenum - 1;
        $prev  = "<a href=\"$self?page=$page\">[Prev]</a>";

        $first = "<a href=\"$self?page=1\">[First Page]</a>";
    } else {
        $prev  = '&nbsp;'; // we're on page one, don't print previous link
        $first = '&nbsp;'; // nor the first page link
    }

    if ($pagenum < $maxpage) {
        $page = $pagenum + 1;
        $next = "<a href=\"$self?page=$page\">[Next]</a>";

        $last = "<a href=\"$self?page=$maxpage\">[Last Page]</a> ";
    } else {
        $next = '&nbsp;'; // we're on the last page, don't print next link
        $last = '&nbsp;'; // nor the last page link
    }

    $pagination ='
    <hr class="featurette-divider" />
    <div id="pagination">
        '. $first . $prev . " Showing page $pagenum of $maxpage pages " . $next . $last .
        '
    </div>';
    $body .= $pagination;
    $script = '<script type="text/javascript" src="assets/js/comments.js" defer></script>';
    include("template.php");
}
?>
