ALTER TABLE `#__helloworld` DROP COLUMN `ordering`;
ALTER TABLE `#__helloworld` ADD COLUMN `parent_id` INT(10) NOT NULL DEFAULT '1' AFTER `language`;
ALTER TABLE `#__helloworld` ADD COLUMN `level`	int(10)    NOT NULL DEFAULT '0' AFTER `parent_id`;
ALTER TABLE `#__helloworld` ADD COLUMN `path`	varchar(400)    NOT NULL DEFAULT '' AFTER `level`;
ALTER TABLE `#__helloworld` ADD COLUMN `lft`	int(11)    NOT NULL DEFAULT '0' AFTER `path`;
ALTER TABLE `#__helloworld` ADD COLUMN `rgt`	int(11)    NOT NULL DEFAULT '0' AFTER `lft`;
UPDATE `#__helloworld` SET `path` = `alias`;