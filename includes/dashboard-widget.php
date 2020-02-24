<?php

/**
 *	Adds hidden content to admin_footer, then shows with jQuery, and inserts after welcome panel
 *
 *	@author Ren Ventura <EngageWP.com>
 *	@see http://www.engagewp.com/how-to-create-full-width-dashboard-widget-wordpress
 */

function rv_custom_dashboard_widget() {

    // Bail if not viewing the main dashboard page
    if ( get_current_screen()->base !== 'dashboard' ) {
        return;
    }

    ?>

    <div id="custom-id" class="welcome-panel" style="display: none;">
        <div class="welcome-panel-content">
            <h2>Welcome to PlanetPagan!</h2>
            <p class="about-description">Thank you for being a valued member of our community.</p>
            <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
                    <h3>Get Social; Get Points.</h3>
                    <ul>
                        <li><a href="https://planetpagan.com/longhouse" class="welcome-icon welcome-edit-page">Visit the Longhouse and check in</a></li>
                        <li><a href="https://planetpagan.com/profile/" class="welcome-icon welcome-add-page">Update your Profile periodically</a></li>
                        <li><a href="https://planetpagan.com/" class="welcome-icon welcome-write-blog">Read (and comment on) other people's posts</a></li>
                        <li><a href="http://localhost:8888/uw-website/" class="welcome-icon welcome-view-site">View your site</a></li>
                    </ul>
                </div>
                <div class="welcome-panel-column">
                    <h3>Get Creative!</h3>
                    <ul>
                        <li><a href="http://localhost:8888/uw-website/wp-admin/post.php?post=2557&amp;action=edit" class="welcome-icon welcome-edit-page">Edit your front page</a></li>
                        <li><a href="http://localhost:8888/uw-website/wp-admin/post-new.php?post_type=page" class="welcome-icon welcome-add-page">Add additional pages</a></li>
                        <li><a href="http://localhost:8888/uw-website/wp-admin/post-new.php" class="welcome-icon welcome-write-blog">Add a blog post</a></li>
                        <li><a href="http://localhost:8888/uw-website/" class="welcome-icon welcome-view-site">View your site</a></li>
                    </ul>
                </div>
                <div class="welcome-panel-column welcome-panel-last">
                    <h3>Get Help.</h3>
                    <ul>
                        <li><div class="welcome-icon welcome-widgets-menus">Manage <a href="http://localhost:8888/uw-website/wp-admin/widgets.php">widgets</a> or <a href="http://localhost:8888/uw-website/wp-admin/nav-menus.php">menus</a></div></li>
                        <li><a href="http://localhost:8888/uw-website/wp-admin/options-discussion.php" class="welcome-icon welcome-comments">Turn comments on or off</a></li>
                        <li><a href="https://codex.wordpress.org/First_Steps_With_WordPress" class="welcome-icon welcome-learn-more">Learn more about getting started</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#welcome-panel').after($('#custom-id').show());
        });
    </script>

<?php }
add_action( 'admin_footer', 'rv_custom_dashboard_widget' );