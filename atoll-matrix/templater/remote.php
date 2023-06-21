<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor template library remote source.
 *
 * Elementor template library remote source handler class is responsible for
 * handling remote templates from Elementor.com servers.
 *
 * @since 1.0.0
 */
class iMaginem_Templates_Remote extends Elementor\TemplateLibrary\Source_Base {

	protected $template_prefix = 'imaginem_';

	public function get_prefix() {
		return $this->template_prefix;
	}

	/**
	 * Get remote template ID.
	 *
	 * Retrieve the remote template ID.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The remote template ID.
	 */
	public function get_id() {
		return 'imaginem-templates';
	}

	/**
	 * Get remote template title.
	 *
	 * Retrieve the remote template title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The remote template title.
	 */
	public function get_title() {
		return __( 'iMaginem Templates', 'elementor' );
	}

	/**
	 * Register remote template data.
	 *
	 * Used to register custom template data like a post type, a taxonomy or any
	 * other data.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_data() {}

	/**
	 * Get remote templates.
	 *
	 * Retrieve remote templates from Elementor.com servers.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Optional. Filter templates list based on a set of
	 *                    arguments. Default is an empty array.
	 *
	 * @return array Remote templates.
	 */
	public function get_items( $args = [] ) {
		$templates = array();

		$today = date("Ymd");

		$templates_data = array(
			1 	=> array(
				'template_id' => $this->template_prefix .'1',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - About me I',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-1.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/about-me/',
			),
			2 	=> array(
				'template_id' => $this->template_prefix .'2',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - About me II',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-2.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/about-me-ii/',
			),
			3 	=> array(
				'template_id' => $this->template_prefix .'3',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - About me III',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-3.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/about-me-iii/',
			),
			4 	=> array(
				'template_id' => $this->template_prefix .'4',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - About me IV',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-4.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/about-me-iv/',
			),
			5 	=> array(
				'template_id' => $this->template_prefix .'5',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - About Us',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-5.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/about-us/',
			),
			6 	=> array(
				'template_id' => $this->template_prefix .'6',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - About Us',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-6.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/about-us-ii/',
			),
			7 	=> array(
				'template_id' => $this->template_prefix .'7',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Blog Grid',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-7.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/blog-grid/',
			),
			8 	=> array(
				'template_id' => $this->template_prefix .'8',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Blog List',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-8.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/blog-list/',
			),
			9 	=> array(
				'template_id' => $this->template_prefix .'9',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Blog Parallax',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-9.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/blog-parallax/',
			),
			10 	=> array(
				'template_id' => $this->template_prefix .'10',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Blog Small',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-10.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/blog-small/',
			),
			11 	=> array(
				'template_id' => $this->template_prefix .'11',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Contact I',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-11.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/contact-us/',
			),
			12 	=> array(
				'template_id' => $this->template_prefix .'12',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Contact II',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-12.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/contact-us-ii/',
			),
			13 	=> array(
				'template_id' => $this->template_prefix .'13',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Event Gallery',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-13.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/event-gallery-i/',
			),
			14 	=> array(
				'template_id' => $this->template_prefix .'14',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Services',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-14.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/our-services/',
			),
			15 	=> array(
				'template_id' => $this->template_prefix .'15',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Services II',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-15.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/our-services-ii/',
			),
			16 	=> array(
				'template_id' => $this->template_prefix .'16',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Portfolio',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-16.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/portfolio-items/portfolio-classic-3-column/',
			),
			17 	=> array(
				'template_id' => $this->template_prefix .'17',
				'source' => $this->get_id(),
				'type' => 'page',
				'subtype' => 'page',
				'title' => 'superlens - Gallery',
				'thumbnail' => 'https://superlens.imaginem.co/imaginem-templates/superlens/screenshots/template-17.png',
				'date' => date( get_option( 'date_format' ), $today ),
				'author' => 'iMaginem',
				'tags' => array('imaginem'),
				'isPro' => false,
				'hasPageSettings' => false,
				'url' => 'https://superlens.imaginem.co/gallery/gallery-classic-3-column/',
			),
		);

		if ( ! empty( $templates_data ) ) {
			foreach ( $templates_data as $template_data ) {
				$templates[] = $this->get_item( $template_data );
			}
		}

		if ( ! empty( $args ) ) {
			$templates = wp_list_filter( $templates, $args );
		}

		return $templates;
	}

	/**
	 * Get remote template.
	 *
	 * Retrieve a single remote template from Elementor.com servers.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $template_data Remote template data.
	 *
	 * @return array Remote template.
	 */
	public function get_item( $template_data ) {
		$favorite_templates = $this->get_user_meta( 'favorites' );

		return [
			'template_id' => $template_data['template_id'],
			'source' => 'remote',
			'type' => $template_data['type'],
			'subtype' => $template_data['subtype'],
			'title' => $template_data['title'],
			'thumbnail' => $template_data['thumbnail'],
			'date' => $template_data['date'],
			'author' => $template_data['author'],
			'tags' => json_decode( $template_data['tags'] ),
			'isPro' => ( '1' === $template_data['isPro'] ),
			'popularityIndex' => (int) $template_data['popularity_index'],
			'trendIndex' => (int) $template_data['trend_index'],
			'hasPageSettings' => ( '1' === $template_data['has_page_settings'] ),
			'url' => $template_data['url'],
			'favorite' => ( 1 == $template_data['favorite'] ),
		];
	}

	/**
	 * Save remote template.
	 *
	 * Remote template from Elementor.com servers cannot be saved on the
	 * database as they are retrieved from remote servers.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $template_data Remote template data.
	 *
	 * @return bool Return false.
	 */
	public function save_item( $template_data ) {
		return false;
	}

	/**
	 * Update remote template.
	 *
	 * Remote template from Elementor.com servers cannot be updated on the
	 * database as they are retrieved from remote servers.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $new_data New template data.
	 *
	 * @return bool Return false.
	 */
	public function update_item( $new_data ) {
		return false;
	}

	/**
	 * Delete remote template.
	 *
	 * Remote template from Elementor.com servers cannot be deleted from the
	 * database as they are retrieved from remote servers.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return bool Return false.
	 */
	public function delete_template( $template_id ) {
		return false;
	}

	/**
	 * Export remote template.
	 *
	 * Remote template from Elementor.com servers cannot be exported from the
	 * database as they are retrieved from remote servers.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return bool Return false.
	 */
	public function export_template( $template_id ) {
		return false;
	}

	/**
	 * Get remote template data.
	 *
	 * Retrieve the data of a single remote template from Elementor.com servers.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array  $args    Custom template arguments.
	 * @param string $context Optional. The context. Default is `display`.
	 *
	 * @return array Remote Template data.
	 */
	public function get_data( array $args, $context = 'display' ) {
		$url	  = 'https://superlens.imaginem.co/imaginem-templates/superlens/data/'.$args['template_id'].'.json';
		$response = wp_remote_get( $url, array( 'timeout' => 60 ) );
		$body     = wp_remote_retrieve_body( $response );
		$body     = json_decode( $body, true );
		$data     = ! empty( $body['content'] ) ? $body['content'] : false;
		
		$result = array();

		$result['content']       = $this->replace_elements_ids($data);
		$result['content']       = $this->process_export_import_content( $result['content'], 'on_import' );
		$result['page_settings'] = array();

		return $result;
	}
}
