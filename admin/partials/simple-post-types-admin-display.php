<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/pehaa/pht-simple-post-types
 * @since      1.0.0
 *
 * @package    PHT_Simple_Post_Types
 * @subpackage PHT_Simple_Post_Types/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}?>

<div class="wrap phtspt-wrap">
	
	<?php $this->get_notification(); ?>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2> 
	
	<?php if ( is_array( $this->options[ 'data' ] ) && ( !empty( $this->options[ 'data' ][ 'post_type'] ) || !empty( $this->options[ 'data' ][ 'taxonomy' ] ) ) ) { ?>
	
	<section class="phtspt-section">
	
		<h3><?php _e( 'Registered custom post types and taxonomies', $this->plugin_name); ?></h3>

		<?php foreach ( $this->options[ 'data' ] as $item => $item_options ) {
			if ( count( $item_options ) ) { ?>
			<h4><?php echo 'taxonomy' === $item ? __( 'Taxonomies:', $this->plugin_name ) : __( 'Post types:', $this->plugin_name ); ?></h4>
			<table class='phtspt-items phtspt-items--<?php echo $item; ?>'>
				<tr>
					<th><?php _e( 'Slug', $this->plugin_name ); ?></th>
					<th><?php _e( 'Name', $this->plugin_name ); ?></th>
					<th></th>
				</tr>
				<?php foreach ( $item_options as $key => $array ) { 
					$delete_path = wp_nonce_url( admin_url( 'admin.php?page=' . $this->manage_page . '&deleteid=' . $key . '&itemtype=' . $item ), $this->plugin_name . '_' . $item . '_delete_nonce' ); ?>
				<tr>
					<td><?php echo $key; ?></td>
					<td><?php echo $array[ 'name' ]; ?></td>
					<td class="phtspt-with-button">
						<a id="remove-<?php echo $key; ?>" data-remove="<?php echo $key; ?>" class="js-phtspt-confirm phtspt-button-remove button-primary" href="<?php echo $delete_path; ?>">
							<i class="dashicons dashicons-trash"></i>
							<?php _e( 'Unregister', $this->plugin_name ); ?>
						</a>
					</td>
				</tr>
				<?php if ( 'taxonomy' === $item ) { ?>
				<tr class="phtspt-more">
					<td class="" colspan="3">
						<header class="phtspt-with-button js-phtspt-acordion-trigger-edit"><a id="remove-<?php echo $key; ?>" data-remove="<?php echo $key; ?>" class="button-primary phtspt-button-edit" href="<?php echo $delete_path; ?>"><i class="dashicons 
dashicons-edit"></i><?php _e( 'Quick edit', $this->plugin_name ); ?></a></header>
						
						<form id="phtspt-form-edit-taxonomy" class="phtspt-form phtspt-form-edit" data-actiontype="edit" data-itemtype="post_type" method="POST">

							<?php wp_nonce_field( $this->nonce['edit_taxonomy'] . '_' . $key ); ?>


							<?php $display->hierarchical_form_field( 'phtspt-hierarchical-' . $key, $array['hierarchical'] ); 

							$display->object_types_form_field( $key, $array['object_types'] ); ?>

							<input id="phtspt-key" type="hidden"  name="phtspt_field[key]" value="<?php echo $key; ?>" />
							<input id="phtspt-item" type="hidden"  name="phtspt-item" value="taxonomy" />
							<input id="<?php echo $this->submit;?>" type="submit" class="button-primary phtspt-button phtspt-button-add" name="<?php echo $this->submit['edit_taxonomy'];?>" value="<?php _e( 'Submit', $this->plugin_name ); ?>" />
								
						</form>
				
					</td>
				</tr><?php } ?>
				<?php } ?>
			</table>

		<?php }
	} ?>
	</section>
<?php } ?>

	<div class="phtspt-sections-accordion">	
		<section class="phtspt-section">

			<header class="phtspt-accordion-trigger js-phtspt-acordion-trigger"><h3><?php _e( 'Add new custom post type:', $this->plugin_name); ?></h3><i class="dashicons 
	dashicons-plus-alt"></i></header>

			<form id="phtspt-form-add-post_type" class="phtspt-form phtspt-form-add" data-actiontype="add" data-itemtype="post_type" method="POST">

				<?php wp_nonce_field( $this->nonce['add_post_type'] ); ?>

				<?php 
				$display->slug_form_field( 'post_type' ); 
				$display->singular_name_form_field( 'post_type' );
				$display->name_form_field( 'post_type' );
				?>
				<input id="<?php echo $this->submit['add_post_type'];?>" type="submit" class="button-primary phtspt-button phtspt-button-add" name="<?php echo $this->submit['add_post_type'];?>" value="<?php _e( 'Register', $this->plugin_name ); ?>" />
					
			</form>

		</section>

		<section class="phtspt-section">

			<header class="phtspt-accordion-trigger js-phtspt-acordion-trigger"><h3><?php _e( 'Add new custom taxonomy:', $this->plugin_name); ?></h3><i class="dashicons 
	dashicons-plus-alt"></i></header>

			<form id="phtspt-form-add-taxonomy" class="phtspt-form phtspt-form-add" data-actiontype="add" data-itemtype="post_type" method="POST">

				<?php wp_nonce_field( $this->nonce['add_taxonomy'] ); ?>

				<?php 
				$display->slug_form_field( 'taxonomy' );
				$display->singular_name_form_field( 'taxonomy' );
				$display->name_form_field( 'taxonomy' );
				$display->hierarchical_form_field();
				if ( !empty( $this->options['data']['post_type'] ) ) { 
					$display->object_types_form_field();

				} ?>

				<input id="<?php echo $this->submit['add_taxonomy'];?>" type="submit" class="button-primary phtspt-button phtspt-button-add" name="<?php echo $this->submit['add_taxonomy'];?>" value="<?php _e( 'Register', $this->plugin_name ); ?>" />
					
			</form>

		</section>
	</div>
</div>