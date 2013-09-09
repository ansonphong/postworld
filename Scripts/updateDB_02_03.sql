USE `wp_postworld_a1`;

/* ADD fk to table users from postworldusers */
ALTER TABLE `wp_postworld_a1`.`wp_postworld_user_meta` 
CHANGE COLUMN `user_id` `user_id` BIGINT(20) UNSIGNED NOT NULL ,
ADD PRIMARY KEY (`user_id`);



  
  
  ALTER TABLE `wp_postworld_a1`.`wp_postworld_user_meta` 
ADD CONSTRAINT `FK_wp_postworld_user_meta_wp_users`
  FOREIGN KEY (`user_id`)
  REFERENCES `wp_postworld_a1`.`wp_users` (`ID`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

