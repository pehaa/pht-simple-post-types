<?php

/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @link       https://github.com/pehaa/pht-simple-post-types
 * @since      1.0.0
 *
 * @package    PHT_Simple_Post_Types
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$clean_data = true;

if ( $clean_data ) {

	global $wpdb;

	$option_name = 'pht_simple_post_types';
	$activation_option_name = 'phtspt-activation';

	$option = get_option( $option_name );

	delete_option( $option_name );
	delete_option( $activation_option_name );

	if ( isset( $option['post_type'] ) ) {
		$post_types_array = array_keys( $option['post_type'] );
		
		if ( ! empty( $post_types_array ) ) {
			
			foreach ( $post_types_array as $post_type_2_delete ) {

				$wpdb->query( 
					$wpdb->prepare( 
						"
						DELETE FROM {$wpdb->posts}
						WHERE post_type IN (%s)
						",
						$post_type_2_delete
					)
				);

			}

			$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
			$wpdb->query( "DELETE term_relationships FROM {$wpdb->term_relationships} term_relationships LEFT JOIN {$wpdb->posts} posts ON posts.ID = term_relationships.object_id WHERE posts.ID IS NULL;" );
			
		}
	}
	// Delete taxonomies + data
	if ( isset( $option['taxonomy'] ) ) {
		$taxonomies_array = array_keys( $option['taxonomy'] );
		if ( ! empty( $taxonomies_array ) ) {
			foreach ( $taxonomies_array as $taxonomy ) {

				$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );
				  
					if ( $terms ) {
						foreach ( $terms as $term ) {
							$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
							$wpdb->delete( $wpdb->term_relationships, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
							$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );	
						}
					}

					$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
			}
		}

	}

} ?>
