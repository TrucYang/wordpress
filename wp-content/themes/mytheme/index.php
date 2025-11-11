<?php
get_header();
function greeting($name){
echo "<p>Hi, my name is $name</p>";
bloginfo('name');
}
greeting('Minh Hieu');
echo "<br>"; ?>

<?php $about = get_page_by_path('about'); ?>
<?php if($about){ ?>
    <h2><a href="<?php the_permalink($about->ID);?>">Trang About</a></h2>
<?php }?>

<?php while(have_posts()){
the_post(); ?>

<?php the_excerpt(); ?>
<hr>
<?php } ?>
<?php get_footer();?>