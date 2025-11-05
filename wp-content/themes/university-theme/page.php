<?php get_header() ?>
<div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(images/ocean.jpg)"></div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"> <?php the_title(); ?></h1>
        <div class="page-banner__intro">
            <p>Learn how the school of your dreams got started.</p>
        </div>
    </div>
</div>

<div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
        <?php
        $theParent = wp_get_post_parent_id(get_the_ID());

        if ($theParent) : ?>
            <p>
                <a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($theParent); ?></a> <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        <?php endif ?>
    </div>
    <?php
    $hasChildren = get_pages(array('child_of' => get_the_ID()));
    ?>

    <?php if ($theParent || $hasChildren) : ?>
        <div class="page-links">
            <h2 class="page-links__title">
                <a href="<?php echo get_permalink($theParent ? $theParent : get_the_ID()); ?>">
                    <?php echo get_the_title($theParent ? $theParent : get_the_ID()); ?>
                </a>
            </h2>

            <ul class="min-list">
                <?php
                $findChildrenOf = $theParent ? $theParent : get_the_ID();

                wp_list_pages(array(
                    'title_li'    => '',
                    'child_of'    => $findChildrenOf,
                    'sort_column' => 'menu_order'
                ));
                ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="generic-content">
        <!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p> -->
        <?php the_content(); ?>
    </div>
</div>

<!-- <div class="page-section page-section--beige">
    <div class="container container--narrow generic-content">
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p>

        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p>
    </div>
</div> -->

<!-- <div class="page-section page-section--white">
    <div class="container container--narrow">
        <h2 class="headline headline--medium">Biology Professors:</h2>

        <ul class="professor-cards">
            <li class="professor-card__list-item">
                <a href="#" class="professor-card">
                    <img class="professor-card__image" src="images/barksalot.jpg" />
                    <span class="professor-card__name">Dr. Barksalot</span>
                </a>
            </li>
            <li class="professor-card__list-item">
                <a href="#" class="professor-card">
                    <img class="professor-card__image" src="images/meowsalot.jpg" />
                    <span class="professor-card__name">Dr. Meowsalot</span>
                </a>
            </li>
        </ul>
        <hr class="section-break" />

        <div class="row group generic-content">
            <div class="one-third">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p>
            </div>

            <div class="one-third">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p>
            </div>

            <div class="one-third">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p>
            </div>
        </div>
    </div>
</div> -->
<?php get_footer(); ?>