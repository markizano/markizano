/**
 *  Table for maintaining the relation between users and groups.
 */
DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE IF NOT EXISTS `user_groups` (
    `user_id` INT (4) NOT NULL AUTO_INCREMENT,
    `group_id` INT (4) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`user_id`, `group_id`),
    KEY `user_idx` (`user_id`),
    KEY `group_idx` (`group_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0;

