 CREATE TABLE `short_urls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sn_md5` varchar(32) not null,
  `url` varchar(256) not null,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn_md5` (`sn_md5`)
) ENGINE=MyISAM AUTO_INCREMENT=801 DEFAULT CHARSET=ascii COLLATE=ascii_bin