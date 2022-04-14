<?php
include_once "dbh.php";
include_once "lib.php";
include_once 'include.php';
connect_todb();
$pic_limit = 5;
$pagenum = 1;

if (isset($_GET["page"])) {
    $pagenum = $_GET["page"];
}
// counting the offset
$offset = ($pagenum - 1) * $pic_limit;

$body = '<h1>Welcome to dapinto\'s Cumagru</h1>
    ';
$all_galleries = fetch_pagination_elements_from_all_galleries($offset, $pic_limit);
if ($all_galleries !== null) {
    $body .= output_gallery($all_galleries);
} else {
    $body = "<h2>We desperately need users, WHO IS GOING TO PAY FOR MY RENT ?? jk i dont get moey from this shit websi</h2>";
    $pagenum = 0;
}

// how many rows we have in table
$rownum = count_img_entries();

// how many pages we have when using paging?
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
    <hr class="featurette-divider"/>
    <div id="pagination">
        '. $first . $prev . " Showing page $pagenum of $maxpage pages " . $next . $last .
        '
    </div>';
$body .= $pagination;

$script = '<script type="text/javascript" src="assets/js/comments.js" defer></script>';

include("template.php");
?>
