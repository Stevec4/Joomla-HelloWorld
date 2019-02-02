ALTER TABLE `#__helloworld` ADD COLUMN `language` CHAR(7) NOT NULL DEFAULT '*' AFTER `alias`;

DROP INDEX `aliasindex` on `#__helloworld`;
CREATE UNIQUE INDEX `aliasindex` ON `#__helloworld` (`alias`, `catid`);