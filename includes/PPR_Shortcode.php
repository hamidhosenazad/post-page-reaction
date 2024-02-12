<?php
/**
 * Core plugin file
 *
 * @since      1.0
 * @package    post-page-reaction
 * @author     Hamid Azad
 */

/*
 * If this file is called directly, abort.
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode class
 */
class PPR_ShortCode
{
    public function ppr_register_shortcode()
    {
        add_shortcode('post-page-reaction', array($this, 'ppr_render_shortcode_content'));
    }

    public function ppr_render_shortcode_content($atts, $content = null)
    {
        $user_id = get_current_user_id();
        $post_type = null;
        $post_or_page_id = null;

        if (is_single() || is_page()) {
            $post_or_page_id = get_the_ID();
            $post_type = is_single() ? 'post' : 'page';
        }

        global $wpdb;

        $table_reactions = $wpdb->prefix . 'ppr_post_page_reactions';

        // Query to count total straight_face_count
        $total_straight_face_count = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(straight_face_count) FROM $table_reactions WHERE post_type = %s AND post_or_page_id = %d",
            $post_type,
            $post_or_page_id
        )
        );

        // Query to count total total_smiley_face_count
        $total_smiley_face_count = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(smiley_face_count) FROM $table_reactions WHERE post_type = %s AND post_or_page_id = %d",
            $post_type,
            $post_or_page_id
        )
        );

        // Query to count total sad_face_count
        $total_sad_face_count = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(sad_face_count) FROM $table_reactions WHERE post_type = %s AND post_or_page_id = %d",
            $post_type,
            $post_or_page_id
        )
        );

        $query = $wpdb->prepare(
            "SELECT id, straight_face_count, smiley_face_count, sad_face_count FROM $table_reactions WHERE user_id = %d AND post_type = %s AND post_or_page_id = %d",
            $user_id,
            $post_type,
            $post_or_page_id
        );
        $existing_row = $wpdb->get_row($query);

        $icon_sizes = array(
            'meh' => 'fa-sm',
            'smile' => 'fa-sm',
            'sad' => 'fa-sm',
        );

        if ($existing_row) {
            $existing_reactions = array(
                'meh' => $existing_row->straight_face_count,
                'smile' => $existing_row->smiley_face_count,
                'sad' => $existing_row->sad_face_count,
            );

            foreach ($existing_reactions as $reaction => $count) {
                if ($count) {
                    $icon_sizes[$reaction] = 'fa-xl';
                }
            }
        }

        $left_icons = '<div class="left-reaction-icons" data-reaction-count="' . esc_attr($total_sad_face_count) . '">
            <div class="reaction-icon">
                <i class="fa-sharp fa-solid fa-face-meh ppr-reaction ' . esc_attr($icon_sizes['meh']) . '" data-reaction="meh" id="reaction-meh" data-user-id="' . esc_attr($user_id) . '" data-post-type="' . esc_attr($post_type) . '" data-post-or-page-id="' . esc_attr($post_or_page_id) . '" ></i>
            </div>
            <div class="reaction-icon">
                <i class="fa-sharp fa-solid fa-face-smile ppr-reaction ' . esc_attr($icon_sizes['smile']) . '" data-reaction="smile" id="reaction-smile" data-user-id="' . esc_attr($user_id) . '" data-post-type="' . esc_attr($post_type) . '" data-post-or-page-id="' . esc_attr($post_or_page_id) . '" ></i>
            </div>
            <div class="reaction-icon">
                <i class="fa-sharp fa-solid fa-face-sad-tear ppr-reaction ' . esc_attr($icon_sizes['sad']) . '" data-reaction="sad" id="reaction-sad" data-user-id="' . esc_attr($user_id) . '" data-post-type="' . esc_attr($post_type) . '" data-post-or-page-id="' . esc_attr($post_or_page_id) . '" ></i>
            </div>
        </div>';

        if (!is_user_logged_in()) {
            $left_icons = '<div class="left-reaction-icons">
                <div class="reaction-icon icon-disabled">
                    <i class="fa-sharp fa-solid fa-face-meh"></i>
                </div>
                <div class="reaction-icon icon-disabled">
                    <i class="fa-sharp fa-solid fa-face-smile"></i>
                </div>
                <div class="reaction-icon icon-disabled">
                    <i class="fa-sharp fa-solid fa-face-sad-tear"></i>
                </div>
            </div>';
        }

        $right_icons = '<div class="right-reaction-icons">
            <div class="reaction-icon">
                <i class="fa-sharp fa-solid fa-face-meh"></i>
                <div class="reaction-count" id="reaction-meh-count">' . esc_attr($total_straight_face_count) . '</div>
            </div>
            <div class="reaction-icon">
                <i class="fa-sharp fa-solid fa-face-smile" ></i>
                <div class="reaction-count" id="reaction-smiley-count">' . esc_attr($total_smiley_face_count) . '</div>
            </div>
            <div class="reaction-icon">
                <i class="fa-sharp fa-solid fa-face-sad-tear"></i>
                <div class="reaction-count" id="reaction-sad-count">' . esc_attr($total_sad_face_count) . '</div>
            </div>
        </div>';

        return '<div class="reaction-container">' . $left_icons . $right_icons . '</div>';
    }

    public function ppr_save_reaction_data()
    {
        global $wpdb;
        $table_reactions = $wpdb->prefix . 'ppr_post_page_reactions';

        $user_id = intval($_POST['user_id']);
        $post_type = sanitize_text_field($_POST['post_type']);
        $post_or_page_id = intval($_POST['post_or_page_id']);
        $clicked_reaction_id = sanitize_text_field($_POST['clicked_reaction_id']);

        $query = $wpdb->prepare(
            "SELECT id FROM $table_reactions WHERE user_id = %d AND post_type = %s AND post_or_page_id = %d",
            $user_id,
            $post_type,
            $post_or_page_id
        );
        $existing_row_id = $wpdb->get_var($query);

        if ($existing_row_id) {
            $update_data = array(
                'straight_face_count' => 0,
                'smiley_face_count' => 0,
                'sad_face_count' => 0,
            );

            if ($clicked_reaction_id === 'reaction-meh') {
                $update_data['straight_face_count'] = 1;
            } elseif ($clicked_reaction_id === 'reaction-smile') {
                $update_data['smiley_face_count'] = 1;
            } elseif ($clicked_reaction_id === 'reaction-sad') {
                $update_data['sad_face_count'] = 1;
            }

            $wpdb->update($table_reactions, $update_data, array('id' => $existing_row_id));
        } else {
            $data = array(
                'user_id' => $user_id,
                'post_type' => $post_type,
                'post_or_page_id' => $post_or_page_id,
                'straight_face_count' => 0,
                'smiley_face_count' => 0,
                'sad_face_count' => 0,
                'created_at' => current_time('mysql', 1),
            );

            if ($clicked_reaction_id === 'reaction-meh') {
                $data['straight_face_count'] = 1;
            } elseif ($clicked_reaction_id === 'reaction-smile') {
                $data['smiley_face_count'] = 1;
            } elseif ($clicked_reaction_id === 'reaction-sad') {
                $data['sad_face_count'] = 1;
            }

            $format = array('%d', '%s', '%d', '%d', '%d', '%d', '%s');
            $wpdb->insert($table_reactions, $data, $format);
        }

        if ($wpdb->last_error) {
            wp_send_json(array('error' => 'Error inserting data into the table: ' . $wpdb->last_error));
        } else {
            wp_send_json($user_id);
        }
    }
    public function ppr_save_reaction_count_data()
    {
        // Make sure to sanitize the input values to prevent SQL injection
        $post_type = sanitize_text_field($_POST['post_type']);
        $post_or_page_id = intval($_POST['post_or_page_id']);

        global $wpdb;
        $table_reactions = $wpdb->prefix . 'ppr_post_page_reactions';

        $total_straight_face_count = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(straight_face_count) FROM $table_reactions WHERE post_type = %s AND post_or_page_id = %d",
            $post_type,
            $post_or_page_id
        )
        );
        $total_smiley_face_count = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(smiley_face_count) FROM $table_reactions WHERE post_type = %s AND post_or_page_id = %d",
            $post_type,
            $post_or_page_id
        )
        );
        $total_sad_face_count = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(sad_face_count) FROM $table_reactions WHERE post_type = %s AND post_or_page_id = %d",
            $post_type,
            $post_or_page_id
        )
        );

        if (!$total_straight_face_count && !$total_smiley_face_count && !$total_sad_face_count) {
            wp_send_json(array('error' => 'No data found for the given criteria'));
        } else {
            wp_send_json(
                array(
                    'total_straight_face_count' => $total_straight_face_count,
                    'total_smiley_face_count' => $total_smiley_face_count,
                    'total_sad_face_count' => $total_sad_face_count
                )
            );
        }
    }
}
