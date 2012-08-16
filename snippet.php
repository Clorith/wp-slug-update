<?php
/**
 *
 * Snippet for mass updating slugs in WordPress
 *
 * Usefull if you've run an import ant slugs aren't imported correctly
 *
 * This code is provided AS IS, no responsibility is taken for any potential damage done to your site by the use of this code.
 * You should always back up your database before making custom edits to it or upgrades!
 *
 * There might be a better way to do this, if so feel free to do so at your own leisure.
 *
 */

    //  We prepare a variable to hold arrays with post titles so we don't accidentally make duplicates, that owuld be bad
    $slug_done = array();

    //  Run a query to grab all the posts, we only want posts/pages though
    $posts = $wpdb->get_results( "
        SELECT
            `ID`,
            `post_title`
        FROM
            `" . $wpdb->posts . "`
        WHERE
            `post_type` = 'page'
        OR
            `post_type` = 'post'
    " );

    //  Loop through results
    foreach( $posts AS $single )
    {
        //  Generate a URL friendly slug from the title
        $slug_base = sanitize_title_with_dashes( $single->post_title );
        $this_slug = $slug_base;

        $slug_num = 1;

        //  Check if the slug already exists, if it does, we add an incremental integer ot the end of it
        while (in_array( $this_slug, $slug_done ) )
        {
            $this_slug = $slug_base . '-' . $slug_num;
                $slug_num++;
        }

        $slug_done[] = $this_slug;

        //  We are happy with our slug, update the database table
        $wpdb->query( "
            UPDATE
                `" . $wpdb->posts . "`
            SET
                `post_name` = '" . $this_slug . "'
            WHERE
                `ID` = '" . $single->ID . "'
            LIMIT 1
        " );
    }