<?php
/**
 * Shows a "Vote" box with the number of votes.
 *
 * @package WDPV
 */

class Wdpv_WidgetVoting extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'wdpv_widget_voting',
			'description' => __( 'Shows "Vote" box for current post/page with number of votes.', 'wdpv' ),
		);

		parent::__construct(
			'wdpv_widget_voting',
			__( 'Voting Widget', 'wdpv' ),
			$widget_ops
		);
	}

	/**
	 * Displays the widget settings form.
	 *
	 * @param array $instance Current widget settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'            => '',
				'show_vote_up'     => 0,
				'show_vote_down'   => 0,
				'show_vote_result' => 0,
			)
		);

		$title            = isset( $instance['title'] ) ? $instance['title'] : '';
		$show_vote_up     = ! empty( $instance['show_vote_up'] );
		$show_vote_down   = ! empty( $instance['show_vote_down'] );
		$show_vote_result = ! empty( $instance['show_vote_result'] );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'wdpv' ); ?>
			</label>
			<input
				type="text"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				class="widefat"
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>

		<p>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $this->get_field_name( 'show_vote_up' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'show_vote_up' ) ); ?>"
				value="1"
				<?php checked( $show_vote_up ); ?>
			/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_vote_up' ) ); ?>">
				<?php esc_html_e( 'Show "Vote up" button:', 'wdpv' ); ?>
			</label>
		</p>

		<p>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $this->get_field_name( 'show_vote_down' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'show_vote_down' ) ); ?>"
				value="1"
				<?php checked( $show_vote_down ); ?>
			/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_vote_down' ) ); ?>">
				<?php esc_html_e( 'Show "Vote down" button:', 'wdpv' ); ?>
			</label>
		</p>

		<p>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $this->get_field_name( 'show_vote_result' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'show_vote_result' ) ); ?>"
				value="1"
				<?php checked( $show_vote_result ); ?>
			/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_vote_result' ) ); ?>">
				<?php esc_html_e( 'Show voting results:', 'wdpv' ); ?>
			</label>
		</p>

		<?php
	}

	/**
	 * Sanitizes and saves widget settings.
	 *
	 * @param array $new_instance New widget settings.
	 * @param array $old_instance Previous widget settings.
	 * @return array Updated widget settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = isset( $new_instance['title'] )
			? sanitize_text_field( $new_instance['title'] )
			: '';

		$instance['show_vote_up'] = ! empty( $new_instance['show_vote_up'] ) ? 1 : 0;

		$instance['show_vote_down'] = ! empty( $new_instance['show_vote_down'] ) ? 1 : 0;

		$instance['show_vote_result'] = ! empty( $new_instance['show_vote_result'] ) ? 1 : 0;

		return $instance;
	}

	/**
	 * Displays the voting widget.
	 *
	 * @param array $args     Widget display arguments.
	 * @param array $instance Saved widget settings.
	 */
	public function widget( $args, $instance ) {
		if ( ! is_singular() ) {
			return;
		}

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'            => '',
				'show_vote_up'     => 0,
				'show_vote_down'   => 0,
				'show_vote_result' => 0,
			)
		);


		$title = apply_filters(
			'widget_title',
			isset( $instance['title'] ) ? $instance['title'] : '',
			$instance,
			$this->id_base
		);
		$show_vote_up     = ! empty( $instance['show_vote_up'] );
		$show_vote_down   = ! empty( $instance['show_vote_down'] );
		$show_vote_result = ! empty( $instance['show_vote_result'] );

		$show_entire_widget = $show_vote_up && $show_vote_down && $show_vote_result;

		$codec = new Wdpv_Codec();

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress widget wrapper HTML.

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Standard WordPress widget title markup.
		}

		if ( $show_entire_widget ) {
			echo wp_kses_post( $codec->process_vote_widget_code( array() ) );
		} else {
			if ( $show_vote_up ) {
				echo wp_kses_post( $codec->process_vote_up_code( array( 'standalone' => 'no' ) ) );
			}

			if ( $show_vote_result ) {
				echo wp_kses_post( $codec->process_vote_result_code( array( 'standalone' => 'no' ) ) );
			}

			if ( $show_vote_down ) {
				echo wp_kses_post( $codec->process_vote_down_code( array( 'standalone' => 'no' ) ) );
			}

			echo '<div class="wdpv_clear"></div>';
		}

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress widget wrapper HTML.
	}
}
