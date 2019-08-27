CREATE TABLE `users` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`name` varchar(32) NOT NULL,
`gender` tinyint(2) NOT NULL,
`email` varchar(1024) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `users` VALUES (1, 'admin', 1, 'admin@mail.ru') , (2, 'testuser', 2, 'testuser@mail.ru');

CREATE TABLE `categories` (
  `id`    smallint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(32)       NOT NULL,
PRIMARY KEY (`id`)
) ENGINE = InnoDB;

INSERT INTO `categories` VALUES (1, 'News');

CREATE TABLE `posts` (
  `id`          int(10) UNSIGNED  NOT NULL AUTO_INCREMENT,
  `category_id` smallint UNSIGNED NOT NULL,
  `title`       varchar(64)       NOT NULL,
  `content`     varchar(244)      NOT NULL,
  `date`        DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `likes`       int UNSIGNED NOT NULL DEFAULT 0,
PRIMARY KEY (`id`)
) ENGINE = InnoDB;

ALTER TABLE  `posts` ADD INDEX (  `category_id` )

CREATE TABLE `likes` (
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,

  CONSTRAINT `likes_pk` PRIMARY KEY (`post_id`, `user_id`),
  CONSTRAINT `likes_fk_post_id` FOREIGN KEY (`post_id`)
    REFERENCES `posts` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT `likes_fk_user_id` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE = InnoDB

-- Index for user id
CREATE INDEX `likes_idx_user_id` ON `likes` (`user_id`);

-- Trigger for increase likes in `posts` when insert new like
CREATE TRIGGER `insert_likes` AFTER INSERT ON `likes` FOR EACH ROW
  UPDATE `posts` SET `likes` = `likes` + 1 WHERE `id` = NEW.`post_id`;

-- Trigger for decrease likes in `post` when user delete like
CREATE TRIGGER `delete_likes` AFTER DELETE ON `likes` FOR EACH ROW
  UPDATE `posts` SET `likes` = `likes` - 1 WHERE `id` = OLD.`post_id`;
  
  
--
-- Working with tables
--

-- Insert new post in table `posts`
INSERT INTO `posts` (`category_id`, `title`, `content`) VALUES ( :categoryId, :postTitle, :postContent);

-- user like post
INSERT INTO `likes` (`post_id`, `user_id`) VALUES (:postId, :userId);

-- remove user like from post
DELETE FROM `likes` WHERE `post_id` = :postId AND `user_id` = :userId;

-- delete post
BEGIN WORK;
DELETE FROM `likes` WHERE `post_id` = :postId;
DELETE FROM `posts` WHERE `id` = :postId;
COMMIT;

-- read posts from table `posts`
SELECT
  c.title,
  p.*
FROM `posts` p INNER JOIN `categories` c ON p.`category_id` = c.`id`
WHERE `category_id` = :categoryId
ORDER BY p.`id` DESC
LIMIT :offset, :limit;

-- read users likes post
SELECT
  u.*
FROM `likes` l INNER JOIN `users` u ON l.`user_id` = u.`id`
WHERE l.`post_id` = :postId
ORDER BY l.`user_id` ASC
LIMIT :offset, :limit;