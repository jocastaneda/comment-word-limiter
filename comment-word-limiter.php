<?php
/*
Plugin Name: Comment Word Limiter
Description: Limit the word count on a comment.
Author: Jose Castaneda
Version: 0.1.0
Author URI: https://blog.josemcastaneda.com
Text Domain: comment-word-limiter
*/

/**
 * Copyright (c) 2017 Jose Castaneda ( email: jose@josemcastaneda.com )
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Filter the comment before it gets saved to the db.
 */
add_filter( 'preprocess_comment', 'limitcommwords', 10, 1 );
function limitcommwords( $commentdata ) {
	// get the option.
	$limit = intval( get_site_option( 'comm_limit', 500 ) );

	// separate all the words!
	$all_content = explode( ' ', $commentdata['comment_content'] );

	// Count how many words there are.
	$total = count( $all_content );

	// we kill it with fire if it is more than the limit set forth by the user.
	if ( $total > $limit ) {
		// This is how we get ants.
		/* Translators: the number of words allowed. */
		wp_die( sprintf( __( 'The comment length exceeded %d words. Please revise it to meet the maximum limit.', 'comment-word-limiter' ), number_format_i18n( $limit ) ) ); // wpcs: XSS ok.
	}

	// we merge|join|concat|combine|another-synomym back together
	$wrds = implode( ' ', $all_content );
	$commentdata['comment_content'] = $wrds;

	// we return it all.
	return $commentdata;
}

/**
 * Register our setting for the word limit.
 */
add_action( 'admin_init', 'limitcomsetting' );
function limitcomsetting() {
	// Register the setting for the options page.
	register_setting( 'discussion', 'comm_limit', 'absint' );
	add_settings_field( 'comment-limit', __( 'Comment Word Limit', 'comment-word-limiter' ), 'limitcommrender', 'discussion' );
}

/**
 * Field setting callback for the word count.
 * @return void
 */
function limitcommrender() {
	$limit = get_site_option( 'comm_limit', 500 );
	printf( '<input type="number" id="comment-limit" name="comm_limit" value="%d">', number_format_i18n( $limit ) ); // wpcs: XSS ok.
}

/**
 * Add a note on the comment form.
 */
add_filter( 'comment_form_defaults', 'limitcommnote', 10, 1 );
function limitcommnote( $defaults ) {
	$limit = get_site_option( 'comm_limit', 500 );
	/* Translators: the number of words allowed. */
	$defaults['comment_notes_after'] = $defaults['comment_notes_after'] . sprintf( __( 'You are limited to %d words', 'comment-word-limiter' ), number_format_i18n( $limit ) );
	return $defaults;
}

