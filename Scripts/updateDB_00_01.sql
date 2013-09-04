
USE `wp_postworld_a1`;
CREATE   OR REPLACE VIEW `get_user_points_view` AS
    select 
        `wp_posts`.`ID` AS `post_id`,
        `wp_postworld_meta`.`points` AS `points`,
        `wp_posts`.`post_author` AS `user_id`
    from
        (`wp_posts`
        join `wp_postworld_meta`)
    where
        (`wp_posts`.`ID` = `wp_postworld_meta`.`id`)
;
