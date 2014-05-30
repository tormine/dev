<div id="sidebar-right" class="sidebar<?php if (is_single() && !in_category(intval(of_get_option('blog_cat_id')))) { echo ' sidebar-right-single'; } ?>">
<?php if (!dynamic_sidebar('sidebar-right')) : ?>
<?php endif ?>
</div>