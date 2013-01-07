<?php
/**
 * The Widget class
  **/
class adManagerWidget extends WP_Widget {
	// Instantiate the parent object
	function adManagerWidget() {
		$config = array(
			'description' => 'Використайте цей віджет для показу рекламу на вашій Боковій панелі.',
		);
		parent::WP_Widget( false, 'Sidebar Ad', $config );
	}

	// Widget output
	function widget( $args, $instance ) {
		if( empty( $instance['ad_id'] ) )
			$content = adManager::random_ad();
		else
			$content = adManager::specific_ad( $instance['ad_id'] );

		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'] );
		echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;
			echo '<div style="max-width: '.$instance['max_width'].'px">'.$content.'</div>';
		echo $after_widget;
	}

	// Save widget options
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['ad_id']     = $new_instance['ad_id'];
		$instance['max_width'] = $new_instance['max_width'];
		return $instance;
	}

	// Output admin widget options form
	function form( $instance ) {
		if( ! isset( $instance['title'] ) )     $instance['title'] = '';
		if( ! isset( $instance['ad_id'] ) )     $instance['ad_id'] = '';
		if( ! isset( $instance['max_width'] ) ) $instance['max_width'] = '';
		?>
		<p>
			<label for="widget-meta-3-title">Назва:</label>
			<input class="widefat" name="<?=$this->get_field_name('title');?>" id="<?=$this->get_field_id('title');?>" type="text" value="<?=$instance['title']?>">
		</p>
		<p>
			<label for="widget-meta-3-title">ID реклами (Залиште порожнім для випадкового показу реклами):</label>
			<input class="widefat" name="<?=$this->get_field_name('ad_id');?>" id="<?=$this->get_field_id('ad_id');?>" type="text" value="<?=$instance['ad_id']?>">
		</p>
		<p>
			<label for="widget-meta-3-title">Максимальна ширина реклами (у пікселях):</label>
			<input class="widefat" name="<?=$this->get_field_name('max_width');?>" id="<?=$this->get_field_id('max_width');?>" type="text" value="<?=$instance['max_width']?>">
		</p>
		<?php
	}
}