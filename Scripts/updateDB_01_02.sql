USE `wp_postworld_a1`;

DELIMITER $$

USE `wp_postworld_a1`$$
DROP TRIGGER IF EXISTS `wp_postworld_a1`.`wp_postworld_points_AINS` $$
DELIMITER ;
USE `wp_postworld_a1`;

DELIMITER $$

DROP TRIGGER IF EXISTS wp_postworld_a1.wp_postworld_points_AfterInsert$$
USE `wp_postworld_a1`$$
CREATE TRIGGER `wp_postworld_points_AfterInsert` AFTER INSERT ON `wp_postworld_points` FOR EACH ROW
-- Edit trigger body code below this line. Do not edit lines above this one
begin
update wp_postworld_meta set points = (select Sum(points) from wp_postworld_points where id = NEW.id) where id = NEW.id;
end
$$
DELIMITER ;



/* ************************************** */

USE `wp_postworld_a1`;

DELIMITER $$

DROP TRIGGER IF EXISTS wp_postworld_a1.wp_postworld_points_AfterDelete$$
USE `wp_postworld_a1`$$


CREATE TRIGGER `wp_postworld_points_AfterDelete` AFTER DELETE ON `wp_postworld_points` FOR EACH ROW
-- Edit trigger body code below this line. Do not edit lines above this one
begin
update wp_postworld_meta set points = (select Sum(points) from wp_postworld_points where id = OLD.id) where id = OLD.id;
end
$$
DELIMITER ;


/* ************************************** */

USE `wp_postworld_a1`;

DELIMITER $$

USE `wp_postworld_a1`$$
DROP TRIGGER IF EXISTS `wp_postworld_a1`.`wp_postworld_points_AfterUpdatee` $$
DELIMITER ;
USE `wp_postworld_a1`;

DELIMITER $$

DROP TRIGGER IF EXISTS wp_postworld_a1.wp_postworld_points_AfterUpdate$$
USE `wp_postworld_a1`$$
CREATE TRIGGER `wp_postworld_points_AfterUpdate` AFTER UPDATE ON `wp_postworld_points` FOR EACH ROW
-- Edit trigger body code below this line. Do not edit lines above this one
begin
if NEW.id = old.id then
	update wp_postworld_meta set points = (select Sum(points) from wp_postworld_points where id = NEW.id) where id = NEW.id;
else
	update wp_postworld_meta set points = (select Sum(points) from wp_postworld_points where id = NEW.id) where id = NEW.id;
	update wp_postworld_meta set points = (select Sum(points) from wp_postworld_points where id = OLD.id) where id = OLD.id;
end if;
end
$$
DELIMITER ;
