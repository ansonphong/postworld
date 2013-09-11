<?php 

global $pw_queries;
$pw_queries = array(

	'FK'=> array(
		0 	=> array(
			"description"=>"FK_wp_postworld_user_meta_wp_users",
			"contraint_check"=> "select if (exists (select Null FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME   = 'FK_wp_postworld_user_meta_wp_users'),1,0)",
			"query"	=> "ALTER TABLE `wp_postworld_a1`.`wp_postworld_user_meta` ADD CONSTRAINT `FK_wp_postworld_user_meta_wp_users` FOREIGN KEY (`user_id`) REFERENCES `wp_postworld_a1`.`wp_users` (`ID`) 
						ON DELETE cascade
	  					ON UPDATE NO ACTION;"
			)
		),
		
		
		
	'Triggers'		=>array(
		0 => array( 
			"description"=>"wp_postworld_points_AfterInsert",
			"drop" =>"DROP TRIGGER IF EXISTS wp_postworld_a1.wp_postworld_points_AfterInsert;",
			"create" =>"CREATE TRIGGER `wp_postworld_points_AfterInsert` AFTER INSERT ON `wp_postworld_points` FOR EACH ROW update wp_postworld_meta set points = (select Sum(points) from wp_postworld_points where post_id = NEW.post_id) where post_id = NEW.post_id;"
		),
		1 => array(
			"description"=>"wp_postworld_points_AfterDelete",
			"drop" =>"DROP TRIGGER IF EXISTS wp_postworld_a1.wp_postworld_points_AfterDelete;",
			"create" =>"CREATE TRIGGER `wp_postworld_points_AfterDelete` AFTER DELETE ON `wp_postworld_points` FOR EACH ROW update wp_postworld_meta set points = (select Sum(points) from wp_postworld_points where post_id = OLD.post_id) where post_id = OLD.post_id;"
		),
		2 => array(
			"description"=>"wp_postworld_points_AfterUpdate",
			"drop" =>"DROP TRIGGER IF EXISTS wp_postworld_a1.wp_postworld_points_AfterUpdate;",
			"create" =>"CREATE TRIGGER `wp_postworld_points_AfterUpdate` AFTER UPDATE ON `wp_postworld_points` FOR EACH ROW update wp_postworld_meta set points = (select Sum(points) from wp_postworld_points where post_id = NEW.post_id) where post_id = NEW.post_id;"
		)
	
	)
);





?>