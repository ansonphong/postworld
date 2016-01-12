<?php

///// GET DISQUS COMMENTS EMBED CODE /////
function pw_get_comments_disqus( $vars ){
    global $pw;
    global $post;

    // Get the saved options array
    $options = pw_grab_option( PW_OPTIONS_COMMENTS, 'disqus' );

    // If not enabled, or no shortname provided, return empty
    if( $options['enable'] == false || empty( $options['shortname'] ) )
        return '';

    // Setup default vars
    $defaultVars = array(
        'id'    =>  'post-' . _get( $pw, 'view.post.ID' ),
        'title' =>  _get( $pw, 'view.post.post_title' ),
        'url'   =>  _get( $pw, 'view.url' ),
        );

    // Merge default vars
    $vars = array_replace_recursive( $defaultVars, $vars );

    // Use output buffering to capture HTML as a string
    ob_start(); ?>

        <div id="disqus_thread"></div>
        <script type="text/javascript">
            var disqus_shortname = '<?php echo $options["shortname"] ?>';
            var disqus_identifier = '<?php echo $vars["id"] ?>';
            var disqus_title = '<?php echo addslashes($vars["title"]) ?>';
            var disqus_url = '<?php echo $vars["url"] ?>';
            (function() {
                var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
            })();
        </script>
        <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

    <?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}


?>