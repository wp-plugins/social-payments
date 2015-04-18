<?php
/*
Plugin Name: Social Payments
Version: 1.6
Plugin URI: http://getbutterfly.com/wordpress-plugins-free/
Description: The Social Payments plugin for WordPress allows customization of Facebook page, Google+ page, Twitter text, link and hashtags details. It also uses HTML5 version of all social scripts and very clean source code for theme compatibility.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/

Copyright 2014, 2015 Ciprian Popescu (email: getbutterfly@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define('SP_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('SP_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('SP_PLUGIN_VERSION', '1.6');

function spjscss() {
    wp_enqueue_style('sp-main', plugins_url('css/sp-main-min.css', __FILE__));
}
function spjscss_admin() {
    wp_enqueue_style('fa', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
}

function social_sell($atts, $content = null) {
	extract(shortcode_atts(array(
		'type' => '',
	), $atts));

    $display = '<div id="fb-root"></div>';
    $display .= '<script>
    function sp_butterfly() {
        jQuery("#restricted").html("' . preg_replace("/\r|\n/", "", addslashes($content)) . '");
    }

    window.fbAsyncInit = function() {
        FB.init({ status : true, cookie : true, xfbml : true });
        FB.Event.subscribe("edge.create", function(response) { sp_butterfly(); });
    };

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, "script", "facebook-jssdk"));

    window.twttr = (function (d,s,id) {
        var t, js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
        js.src="//platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);
        return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
    }(document, "script", "twitter-wjs"));

    twttr.ready(function(twttr) {
        twttr.events.bind("tweet", function(event) {
            sp_butterfly();
        });
    });

    (function() {
        var po = document.createElement("script");
        po.type = "text/javascript"; po.async = true;
        po.src = "https://apis.google.com/js/platform.js";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(po, s);
    })();
    </script>';

    if(function_exists('settings_fields'))
        settings_fields('sp_options_options');
    $options = get_option('sp_options');

    if($options['sp_facebook'] != '')
        $sp_facebook = $options['sp_facebook'];
    else
        $sp_facebook = get_permalink();

    if($options['sp_google'] != '')
        $sp_google = $options['sp_google'];
    else
        $sp_google = get_permalink();

    if($options['sp_tweet'] != '')
        $sp_tweet = $options['sp_tweet'];
    else
        $sp_tweet = get_the_title() . ' ' . get_permalink();

    $sp_tweet_via = $options['sp_tweet_via'];
    $sp_tweet_related = $options['sp_tweet_related'];
    $sp_tweet_hashtags = $options['sp_tweet_hashtags'];

    $display .= '<ul class="social">';
        if($options['sp_f'] == 1) {
            $display .= '<li><div class="fb-like" data-href="' . $sp_facebook . '" data-width="200" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li>';
        }
        if($options['sp_t'] == 1) {
            $display .= '<li><a href="https://twitter.com/share" class="twitter-share-button" data-text="' . $sp_tweet . '" data-via="' . $sp_tweet_via . '" data-related="' . $sp_tweet_related . '" data-hashtags="' . $sp_tweet_hashtags . '">Tweet</a></li>';
        }
        if($options['sp_g'] == 1) {
            $display .= '<li><g:plusone size="medium" callback="sp_butterfly" href="' . $sp_google . '"></g:plusone></li>';
        }
    $display .= '</ul><div id="restricted"></div>';

    return $display;
}

register_activation_hook(__FILE__, 'sp_install');

function sp_install() {
    add_option('sp_f', 0);
    add_option('sp_g', 0);
    add_option('sp_t', 0);

    add_option('sp_facebook', '');
    add_option('sp_google', '');
    add_option('sp_tweet', '');
    add_option('sp_tweet_via', '');
    add_option('sp_tweet_related', '');
    add_option('sp_tweet_hashtags', '');
}

add_action('wp_head', 'spjscss');
add_action('admin_enqueue_scripts', 'spjscss_admin');
add_action('admin_menu', 'spconfig');
add_action('admin_init', 'sp_options_init');

add_shortcode('social-sell', 'social_sell'); // shortcode, function

function sp_options_init(){
    register_setting('sp_options_options', 'sp_options');
}

function spconfig() {
    add_options_page('Social Payments', 'Social Payments', 'manage_options', 'sp_options', 'sp_options');
}

function sp_options() {
    ?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Social Payments</h2>
		<div id="poststuff" class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3>About Social Payments <small>(<a href="http://getbutterfly.com/wordpress-plugins/social-payments/" rel="external">official web site</a>)</small></h3>
				<div class="inside">
					<p>
                        <small>You are using <b>Social Payments</b> plugin version <strong><?php echo SP_PLUGIN_VERSION; ?></strong>.</small><br>
                        <small>Dependencies: <a href="http://fontawesome.io/" rel="external">Font Awesome</a> 4.3.0.</small>
                    </p>

					<form action="options.php" method="post" >
                        <h2>
                            General Options
                            <br><small>Tick boxes to enable/disable each module</small>
                        </h2>

                        <?php if(function_exists('settings_fields')) settings_fields('sp_options_options'); ?>
                        <?php $options = get_option('sp_options'); ?>

                        <p>
                            <input name="sp_options[sp_f]" type="checkbox" value="1" <?php checked('1', $options['sp_f']); ?>>
                            <input type="text" name="sp_options[sp_facebook]" value="<?php echo $options['sp_facebook']; ?>" placeholder="Facebook Page" class="regular-text">
                            <label for="sp_facebook"> <i class="fa fa-facebook-square fa-fw"></i> Facebook Page</label>
                            <br><small>Leave blank to allow current post/page.</small>
                        </p>
                        <p>
                            <input name="sp_options[sp_g]" type="checkbox" value="1" <?php checked('1', $options['sp_g']); ?>>
                            <input type="text" name="sp_options[sp_google]" value="<?php echo $options['sp_google']; ?>" placeholder="Google+ Page" class="regular-text">
                            <label for="sp_google"> <i class="fa fa-google-plus-square fa-fw"></i> Google+ Page</label>
                            <br><small>Leave blank to allow current post/page.</small>
                        </p>
                        <p>
                            <input name="sp_options[sp_t]" type="checkbox" value="1" <?php checked('1', $options['sp_t']); ?>>
                            <input type="text" name="sp_options[sp_tweet]" value="<?php echo $options['sp_tweet']; ?>" placeholder="Twitter Text" class="regular-text">
                            <label for="sp_tweet"> <i class="fa fa-twitter-square fa-fw"></i> Twitter Text</label>
                            <br><small>This text will be tweeted. Leave blank to allow current post/page title and permalink.</small>
                            <br><small><b>Hint #1:</b> Do not use a web link, the current page will be automatically included.</small>
                        </p>

                        <p>
                            <input type="text" name="sp_options[sp_tweet_via]" value="<?php echo $options['sp_tweet_via']; ?>" placeholder="@via" class="text" title="@via">
                            <input type="text" name="sp_options[sp_tweet_related]" value="<?php echo $options['sp_tweet_related']; ?>" placeholder="@related" class="text" title="@related">
                            <input type="text" name="sp_options[sp_tweet_hashtags]" value="<?php echo $options['sp_tweet_hashtags']; ?>" placeholder="#hashtag" class="text" title="hashtag">
                            <label> Twitter Details</label>
                            <br><small>Optional tweet details. Fill them in for a more relevant tweet. Hover for more info.</small>
                            <br><small><b>Hint #2:</b> Do not use <code>#</code> for hashtag, it will be automatically attached.</small>
                            <br><small><b>Hint #3:</b> Do not use <code>@</code> for username, it will be automatically attached.</small>
                            <br><small><b>Hint #4:</b> Only use one word inside each of the text boxes above.</small>
                        </p>
						<p>
							<input name="sp-submit" type="submit" class="button-primary" value="Save Changes">
						</p>
                    </form>

                    <h2>Available Shortcode Samples</h2>
                    <p>
                        This shortcode displays the three social buttons:<br>
                        <code>[social-sell]Your content here[/social-sell]</code>
                        <br><small>Add any content you want inside the shortcode, images, text, links, videos, downloads and more.</small>
                    </p>
				</div>
			</div>
            <div class="postbox">
                <div class="inside">
                    <p>For support, feature requests and bug reporting, please visit the <a href="//getbutterfly.com/" rel="external">official website</a>.</p>
                    <p>&copy;<?php echo date('Y'); ?> <a href="//getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <a href="//getbutterfly.com/forums/" rel="external">Support forums</a> &middot; <a href="//getbutterfly.com/trac/" rel="external">Trac</a> &middot; <small>Code wrangling since 2005</small></p>
                </div>
            </div>
		</div>
	</div>

    <?php
}
?>
