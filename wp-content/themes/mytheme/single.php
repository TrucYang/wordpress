<?php
get_header();
function greeting($name){
echo "<p>Hi, my name is $name</p>";
bloginfo('name');
}
greeting('Minh Hieu');
echo "<br>";

while(have_posts()){
the_post(); ?>
<h2><?php the_title()?></h2>
<?php the_content(); ?>
<hr>
<?php } 
get_footer();
?>