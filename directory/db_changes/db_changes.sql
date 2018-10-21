--For saving search reference for the purpose of mainly android bug where back to search buttton was not appearing.
CREATE TABLE `frc_user_metas` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `meta_key` varchar(50) NOT NULL,
  `meta_value` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `frc_user_metas` ADD UNIQUE KEY `id` (`id`), ADD UNIQUE KEY `user_id` (`user_id`,`meta_key`);
ALTER TABLE `frc_user_metas` MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;