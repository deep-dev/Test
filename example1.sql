CREATE TABLE `users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(32) NOT NULL,
`gender` tinyint(2) NOT NULL,
`email` varchar(1024) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `categories` (
  `id`    smallint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(32)       NOT NULL,
PRIMARY KEY (`id`)
) ENGINE = InnoDB;

INSERT INTO `categories` VALUES (1, 'user1');

CREATE TABLE `posts` (
  `id`          int UNSIGNED  NOT NULL AUTO_INCREMENT,
  `category_id` smallint UNSIGNED NOT NULL,
  `likes`       int UNSIGNED  NOT NULL DEFAULT 0,
  `title`       varchar(32)       NOT NULL,
  `content`     varchar(244)      NOT NULL,
  `date`        DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE = InnoDB;
