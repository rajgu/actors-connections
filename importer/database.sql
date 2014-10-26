
CREATE TABLE `a2m` (
  `actor_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  KEY `actor_id` (`actor_id`),
  KEY `movie_id` (`movie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

 CREATE TABLE `actors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor1` int(11) NOT NULL,
  `actor2` int(11) NOT NULL,
  `used_cnt` int(11) NOT NULL,
  `last_used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `actors` (`actor1`,`actor2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `movies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `stats` (
  `day` date NOT NULL,
  `queries` int(11) NOT NULL DEFAULT '0',
  `cached` int(11) NOT NULL DEFAULT '0',
  `found` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `day` (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
