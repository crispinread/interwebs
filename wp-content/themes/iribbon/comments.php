<?php
/**
 * Comments Template
 *
 * Please do not edit this file. This file is part of the Cyber Chimps Framework and all modifications
 * should be made in a child theme.
 *
 * @category CyberChimps Framework
 * @package  Framework
 * @since    1.0
 * @author   CyberChimps
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     http://www.cyberchimps.com/
 */
?>

<?php
	/*
	 * If the current post is protected by a password and
	 * the visitor has not yet entered the password we will
	 * return early without loading the comments.
	 */
	if ( post_password_required() )
		return;
?>


<!------------- Display all comments -------------->
<div id="comments_wrapper">

	<?php
	if ( have_comments() ) {
	?>
	
		<div class="ribbon-top">
			<div class="ribbon-more"> </div>
			<h2 class="comments-title">
				<?php
					printf( _n( 'One Response', '%1$s Responses', get_comments_number(), 'cyberchimps' ),
						number_format_i18n( get_comments_number() ) );
				?>
			</h2>
			<div class="ribbon-shadow"></div>
		</div> <!-- .ribbon-top -->
	<?php
	}?>

	<div id="comments" class="comments-area">

		<?php // You can start editing here -- including this comment! ?>

		<?php if ( have_comments() ) : ?>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<nav role="navigation" id="comment-nav-above" class="site-navigation comment-navigation">
				<h4 class="assistive-text"><?php _e( 'Comment navigation', 'cyberchimps' ); ?></h4>
				<div class="nav-previous"><?php previous_comments_link( '&larr; ' . __( 'Older Comments', 'cyberchimps' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'cyberchimps' ) . ' &rarr;' ); ?></div>
			</nav>
			<?php endif; // check for comment navigation ?>

			<ol class="commentlist">
				<?php
					/* Loop through and list the comments. Tell wp_list_comments()
					 * to use cyberchimps_comment() to format the comments.
					 * If you want to overload this in a child theme then you can
					 * define cyberchimps_comment() and that will be used instead.
					 * See cyberchimps_comment() in functions.php for more.
					 */
					wp_list_comments( array( 'callback' => 'cyberchimps_comment' ) );
				?>
			</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<nav role="navigation" id="comment-nav-below" class="site-navigation comment-navigation">
				<h4 class="assistive-text"><?php _e( 'Comment navigation', 'cyberchimps' ); ?></h4>
				<div class="nav-previous"><?php previous_comments_link( '&larr; ' . __( 'Older Comments', 'cyberchimps' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'cyberchimps' ) . ' &rarr;' ); ?></div>
			</nav>
			<?php endif; // check for comment navigation ?>

		<?php endif; // have_comments() ?>

		<?php
			// If comments are closed and there are comments, let's leave a little note, shall we?
			if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
			<p class="nocomments"><?php _e( 'Comments are closed.', 'cyberchimps' ); ?></p>
		<?php endif; ?>
		
	</div><!-- #comments .comments-area -->
</div>
<!------------- All comments ends -------------->

<?php
/* If comment is allowed on the post and the post is not password protected then display form to give comment. */
if( comments_open() && ! post_password_required() ) {
?>
	<div id="respond_wrapper">
	
		<div class="ribbon-top">
			<div class="ribbon-more"></div>
			<h2 class="respond-title">Leave a Reply</h2>
			<div class="ribbon-shadow"></div>
		</div><!-- ribbon top -->

		<div id="respond">
			<?php
			if( get_option('comment_registration') && !is_user_logged_in() ) {
			?>
				<p>
                    <?php
                        printf( __( 'You must be %1$s logged in %2$s to post a comment', 'cyberchimps' ),
                            '<a href="' . wp_login_url( get_permalink() ) . '" title="' . __( 'logged in', 'cyberchimps' ) . '">',
                            '</a>' );
                    ?>
				</p>
			<?php
			}
			else {
				comment_form();
			}?>
		</div>
	</div>
<?php
}?>