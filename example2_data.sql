INSERT INTO `users` (`name`, `gender`, `email`) VALUES
 ('user01', 1, 'user01@mail.ru'),
 ('user02', 1, 'user02@yandex.ru, user02@gmail.com, user02@yahoo.com'),
 ('user03', 1, 'user03@freemail.ru'),
 ('user04', 1, 'user04@hotmail.com , user04@pochta.ru'),
 ('user05', 1, 'user05@vk.com'),
 ('user06', 1, 'user06@microsoft.com, user06@facebook.com, user06@mail.ru'),
 ('user07', 1, 'user07@yandex.ru'),
 ('user08', 1, 'user08@rambler.ru, user08@begun.ru, user08@rbc.ru'),
 ('user09', 1, 'user09@yahoo.com, user09@microsoft.com, user09@amazon.com'),
 ('user10', 1, 'user10@lenta.ru, user10@mtu.ru, user10@mail.ru'),
 ('user11', 1, 'user11@yandex.ru'),
 ('user12', 1, 'user12@hotmail.com, user12@ya.ru, user12@mail.ru'),
 ('user13', 1, ''),
 ('user14', 1, 'user14@vk.com, user14@microsoft.com, user14@facebook.com'),
 ('user15', 1, 'user15@mail.ru ,user15@yandex.ru, user15@gmail.com'),
 ('user16', 1, 'user16@gmail.com ,user16@invalid.com, user16@domain2.com'),
 ('user17', 1, 'user17@yahoo.com'),
 ('user18', 1, 'user18@mail.ru, user18@rbc.ru'),
 ('user19', 1, 'user19@yandex.ru, user19@mail.ru'),
 ('user20', 1, 'user20@gmail.com, user20@rbc.ru, user20@yahoo.com');
 
CREATE TABLE _domains (
  `domain`  VARCHAR(128) NOT NULL,
  `counter` int(10)      NOT NULL,
  PRIMARY KEY (`domain`)
) ENGINE = Memory