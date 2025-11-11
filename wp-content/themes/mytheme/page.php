<?php
get_header();
function greeting($name){
echo "<p>Hi, my name is $name</p>";
bloginfo('name');
}
greeting('Minh Hieu');
echo "<br>";

$page = get_page_by_path('about');
if ($page) {
    echo '<h2>' . esc_html($page->post_title) . '</h2>';
    echo apply_filters('the_content', $page->post_content);
}
get_footer();
?>