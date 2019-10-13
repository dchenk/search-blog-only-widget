<?php
/*
Plugin Name: Search Blog Only Widget
Plugin URI: https://widerwebs.com
Description: Adds a search field widget for searching only blog posts.
Version: 1.0
Author: Wider Webs
License: GPL2v2
*/

class SearchBlogOnlyWidget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'SearchBlogOnlyWidget',
			'Blog Search',
			['description' => 'Search field for only blog posts.', 'classname' => 'widget_search']
		);
	}

	public function widget($args, $instance) {
		$title = apply_filters('widget_title', $instance['title']);

		echo $args['before_widget'];

		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo '<form method="get" role="search" id="searchform" class="searchform" action="' . home_url('/') . '">
				<div>
					<label class="screen-reader-text" for="s">Search for:</label>	
					<input type="hidden" name="post_type" value="post">
					<input type="text" name="s" id="s" value="' . get_search_query() . '">
					<input type="submit" id="searchsubmit" value="Search">
				</form>
			</div>';
		echo $args['after_widget'];
	}

	public function form($instance) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
				name="<?php echo $this->get_field_name('title'); ?>" type="text"
				value="<?php echo esc_attr($instance['title'] ?? ''); ?>">
		</p>
		<?php
	}

	// Update the instance
	public function update($new_instance, $old_instance) {
		$instance = [];
		$instance['title'] = strip_tags($new_instance['title'] ?? '');
		return $instance;
	}

	// Add functionality to the custom search widget
	public static function blog_search_filter($query) {
		if ($query->is_search && !is_admin()) {
			$post_type = $_GET['post_type'];
			if (!$post_type) {
				$post_type = 'any';
			}
			$query->set('post_type', $post_type);
		}
		return $query;
	}
}

add_action('widgets_init', function () {
	register_widget('SearchBlogOnlyWidget');
});

add_filter('pre_get_posts', ['SearchBlogOnlyWidget', 'blog_search_filter']);
