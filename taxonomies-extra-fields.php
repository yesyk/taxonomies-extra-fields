<?php
/*
Plugin Name: Taxonomies Extra Fields
Plugin URI:
Description: Taxonomies Extra Fields adds extra fields to taxonomy.
Version: 1.0.0
Author: Yesyk
Author URI:
License: GPLv2 or later
Text Domain: taxonomies-extra-fields
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEF_VERSION', '1.0.0' );
define( 'TEF_WP_VERSION', get_bloginfo( 'version' ) );
define( 'TEF_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'TEF_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

class Taxonomy_Extra_Fields {

	public function __construct() {
		add_action( 'init', array( $this, 'tef_check_capabilities' ) );
		add_action( 'plugins_loaded', array( $this, 'tef_languages_load' ) );
		add_filter( 'get_the_archive_title', array( $this, 'tef_output_media_fields' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'tef_front_styles' ) );
	}

	public function tef_check_capabilities() {
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		add_action( 'category_add_form_fields', array( $this, 'tef_add_image_field' ) );
		add_action( 'category_add_form_fields', array( $this, 'tef_add_video_field' ) );
		add_action( 'category_edit_form_fields', array( $this, 'tef_edit_image_field' ) );
		add_action( 'category_edit_form_fields', array( $this, 'tef_edit_video_field' ) );
		add_action( 'created_category', array( $this, 'tef_save_image_field' ) );
		add_action( 'created_category', array( $this, 'tef_save_video_field' ) );
		add_action( 'edited_category', array( $this, 'tef_save_image_field' ) );
		add_action( 'edited_category', array( $this, 'tef_save_video_field' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tef_load_media' ) );
		add_action( 'admin_print_styles', array( $this, 'tef_admin_styles' ) );
		add_action( 'admin_print_scripts', array( $this, 'tef_admin_scripts' ) );
	}

	public function tef_languages_load() {
		load_plugin_textdomain( 'taxonomies-extra-fields', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	public function tef_load_media() {
		wp_enqueue_media();
	}

	public function tef_admin_styles() {
		wp_enqueue_style( 'tef-admin', TEF_URI . 'css/admin.css', '', filemtime( TEF_DIR . 'css/admin.css' ) );
	}

	public function tef_front_styles() {
		wp_enqueue_style( 'tef-front', TEF_URI . 'css/front.css', '', filemtime( TEF_DIR . 'css/front.css' ) );
	}

	public function tef_admin_scripts() {
		wp_enqueue_script( 'tef-script', TEF_URI . 'js/script.js', '', filemtime( TEF_DIR . 'js/script.js' ), true );
	}

	public function tef_add_image_field() {
		?>
		<div class="form-field term-image-wrap">
			<label for="tef-image"><?php _e( 'Image', 'taxonomies-extra-fields' ); ?></label>
			<input type="hidden" id="tef-image" name="tef-image" value="">
			<div id="tef-term-image"></div>
			<p>
				<input type="button" id="tef-add-media" class="button button-primary"  value="<?php _e( 'Add Image', 'taxonomies-extra-fields' ); ?>" />
				<input type="button" id="tef-remove-media" class="button button-secondary" value="<?php _e( 'Remove Image', 'taxonomies-extra-fields' ); ?>" />
			</p>
			<p class="description"><?php _e( 'The image displays on category archive page under the title of the category.', 'taxonomies-extra-fields' ); ?></p>
		</div>
		<?php
	}

	public function tef_edit_image_field( $term ) {
		?>
		<tr class="form-field term-image-wrap">
			<th scope="row">
				<label for="tef-image"><?php _e( 'Image', 'taxonomies-extra-fields' ); ?></label>
			</th>
			<td>
				<?php $image_id = get_term_meta( $term->term_id, 'tef-image', true ); ?>
				<div id="tef-term-image">
					<?php if ( $image_id ) { ?>
						<?php echo wp_get_attachment_image( $image_id ); ?>
					<?php } ?>
				</div>
				<input type="hidden" id="tef-image" name="tef-image" value="<?php echo esc_attr( $image_id ); ?>">
				<p>
					<input type="button" id="tef-add-media" class="button button-primary" value="<?php _e( 'Add Image', 'taxonomies-extra-fields' ); ?>" />
					<input type="button" id="tef-remove-media" class="button button-secondary" value="<?php _e( 'Remove Image', 'taxonomies-extra-fields' ); ?>" />
				</p>
				<p class="description"><?php _e( 'The image displays on category archive page under the title of the category.', 'taxonomies-extra-fields' ); ?></p>
			</td>
		</tr>
		<?php
	}

	public function tef_save_image_field( $term_id ) {
		$current_image = get_term_meta( $term_id, 'tef-image', true );

		if ( $current_image && ! isset ( $_POST['tef-image'] ) ) {
			return $current_image;
		} elseif ( isset( $_POST['tef-image'] ) && $_POST['tef-image'] !== '' ) {
            $tef_image = sanitize_post( wp_unslash( $_POST['tef-image'] ), 'db' );
			update_term_meta( $term_id, 'tef-image', $tef_image );
		} else {
			update_term_meta( $term_id, 'tef-image', '' );
		}
	}

	public function tef_add_video_field() {
		?>
		<div class="form-field term-video-wrap">
			<label for="tef-video"><?php _e( 'Video', 'taxonomies-extra-fields' ); ?></label>
			<input type="text" name="tef-video" id="tef-video" value="">
			<p class="description"><?php _e( 'The video displays on category archive page under the title of the category.', 'taxonomies-extra-fields' ); ?></p>
		</div>
		<?php
	}

	public function tef_edit_video_field( $term ) {
		?>
		<tr class="form-field term-video-wrap">
			<th scope="row" valign="top">
				<label for="tef-video"><?php _e( 'Video', 'taxonomies-extra-fields' ); ?></label>
			</th>
			<td>
				<?php
					$tef_video = get_term_meta( $term->term_id, 'tef-video', true );
					global $wp_embed;
				if ( $tef_video ) :
					?>
					<div id="tef-term-video">
					<?php
						echo $wp_embed->autoembed( $tef_video );
					?>
					</div>
					<?php
					endif;
				?>
				<input type="text" name="tef-video" id="tef-video" value="<?php echo esc_attr( $tef_video ); ?>">
				<p class="description"><?php _e( 'The video displays on category archive page under the title of the category.', 'taxonomies-extra-fields' ); ?></p>
			</td>
		</tr>
		<?php
	}

	public function tef_save_video_field( $term_id ) {
		$current_video = get_term_meta( $term_id, 'tef-video', true );
		$allowed_hosts = array(
			'youtube.com'     => 'youtube.com',
			'www.youtube.com' => 'www.youtube.com',
			'youtu.be'        => 'youtu.be',
			'vimeo.com'       => 'vimeo.com',
		);
		if ( isset ( $_POST['tef-video'] ) ) {
            $video_host = wp_parse_url( $_POST['tef-video'], PHP_URL_HOST );
        }

		if ( $current_video && ! isset ( $_POST['tef-video'] ) ) {
			return $current_video;
		} elseif ( isset ( $_POST['tef-video'], $allowed_hosts[ $video_host ] ) ) {
            $tef_video = sanitize_post( wp_unslash( $_POST['tef-video'] ), 'db' );
			update_term_meta( $term_id, 'tef-video', $tef_video );
		} else {
			update_term_meta( $term_id, 'tef-video', '' );
		}
	}

	public function tef_output_media_fields( $title ) {
		global $wp_embed;
		$tef_term_id  = get_queried_object()->term_id;
		$tef_image_id = get_term_meta( $tef_term_id, 'tef-image', true );
		$tef_image    = wp_get_attachment_image( $tef_image_id, 'full' );
		$tef_video    = get_term_meta( $tef_term_id, 'tef-video', true );
		$title       .= '<div class="tef-image-wrapper">' . $tef_image . '</div>';
		$title       .= '<div class="tef-iframe-wrapper">' . $wp_embed->autoembed( $tef_video ) . '</div>';

		return $title;
	}

}

new Taxonomy_Extra_Fields();
