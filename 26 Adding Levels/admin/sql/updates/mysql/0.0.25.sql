ALTER TABLE`#__helloworld` ADD COLUMN `ordering` int(11) NOT NULL DEFAULT '0' AFTER `language`;
UPDATE `#__helloworld` SET `ordering` = `id`;