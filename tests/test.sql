
CREATE TABLE `people` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) character set gb2312 NOT NULL,
  `sex` varchar(2) character set gb2312 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;


INSERT INTO `people` (`id`, `name`, `sex`) VALUES
(1, '张三', '男'),
(2, '李四', '女'),
(3, '王五', '男'),
(4, '赵六', '女'),
(5, '小七', '男'),
(6, '小八', '男'),
(7, '小九', '男'),
(8, '小十', '女'),
(9, '小十一', '男'),
(10, '小十二', '男');
