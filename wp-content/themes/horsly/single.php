<?php
if (in_category(intval(of_get_option('blog_cat_id')))) {
	get_template_part('single', 'blog');
} else {
	get_template_part('single', 'pin');
}
?>