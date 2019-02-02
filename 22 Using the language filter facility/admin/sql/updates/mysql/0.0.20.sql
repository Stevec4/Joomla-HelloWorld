ALTER TABLE`#__helloworld` ADD COLUMN `alias` VARCHAR(40) NOT NULL DEFAULT '' AFTER `greeting`;
UPDATE `#__helloworld` AS h1
SET alias = (SELECT CONCAT('id-', ID) FROM (SELECT * FROM `#__helloworld`) AS h2 WHERE h1.id = h2.id);
CREATE UNIQUE INDEX `aliasindex` ON `#__helloworld` (`alias`);
