<?php 

function pw_get_install_queries(){
	global $wpdb;
	return array(

		'FK'=> array(
			0 	=> array( // add fk from table user_meta to table wp_users
				"description"=>"FK_wp_postworld_user_meta_wp_users",
				"contraint_check"=> "select if (exists (select Null FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME   = 'FK_wp_postworld_user_meta_wp_users'),1,0)",
				"query"	=> "ALTER TABLE `".$wpdb->postworld_prefix.'user_meta'."` ADD CONSTRAINT `FK_wp_postworld_user_meta_wp_users` FOREIGN KEY (`user_id`) REFERENCES `wp_users` (`ID`) 
							ON DELETE cascade
		  					ON UPDATE NO ACTION;"
				)
			),
			
		'Triggers'		=>array(
			0 => array( 
				"description"=>"wp_postworld_post_points_AfterInsert",
				"drop" =>"DROP TRIGGER IF EXISTS wp_postworld_post_points_AfterInsert;",
				"create" =>"
					CREATE TRIGGER `wp_postworld_post_points_AfterInsert`
					AFTER INSERT ON `".$wpdb->postworld_prefix.'post_points'."`
					FOR EACH ROW UPDATE ".$wpdb->postworld_prefix.'post_meta'."
					SET post_points = (select COALESCE(SUM(post_points),0)
					FROM ".$wpdb->postworld_prefix.'post_points'."
					WHERE post_id = NEW.post_id)
					WHERE post_id = NEW.post_id;"
			),
			1 => array(
				"description"=>"wp_postworld_post_points_AfterDelete",
				"drop" =>"DROP TRIGGER IF EXISTS wp_postworld_post_points_AfterDelete;",
				"create" =>"CREATE TRIGGER `wp_postworld_post_points_AfterDelete` AFTER DELETE ON `".$wpdb->postworld_prefix.'post_points'."` FOR EACH ROW update ".$wpdb->postworld_prefix.'post_meta'." set post_points = (select COALESCE(SUM(post_points),0) from ".$wpdb->postworld_prefix.'post_points'." where post_id = OLD.post_id) where post_id = OLD.post_id;"
			),
			2 => array(
				"description"=>"wp_postworld_post_points_AfterUpdate",
				"drop" =>"DROP TRIGGER IF EXISTS wp_postworld_post_points_AfterUpdate;",
				"create" =>"CREATE TRIGGER `wp_postworld_post_points_AfterUpdate` AFTER UPDATE ON `".$wpdb->postworld_prefix.'post_points'."` FOR EACH ROW update ".$wpdb->postworld_prefix.'post_meta'." set post_points = (select COALESCE(SUM(post_points),0) from ".$wpdb->postworld_prefix.'post_points'." where post_id = NEW.post_id) where post_id = NEW.post_id;"
			)
		
		)
	);


}
