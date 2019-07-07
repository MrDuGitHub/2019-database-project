/*
Navicat MariaDB Data Transfer

Source Server         : database.intranet
Source Server Version : 100314
Source Host           : database.intranet:3306
Source Database       : database_intrane

Target Server Type    : MariaDB
Target Server Version : 100314
File Encoding         : 65001

Date: 2019-07-01 01:14:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for attachment
-- ----------------------------
DROP TABLE IF EXISTS `attachment`;
CREATE TABLE `attachment` (
  `attachment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `attachment_uid` bigint(20) NOT NULL,
  `attachment_file_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `attachment_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`attachment_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of attachment
-- ----------------------------

-- ----------------------------
-- Table structure for database_answer
-- ----------------------------
DROP TABLE IF EXISTS `database_answer`;
CREATE TABLE `database_answer` (
  `uid` bigint(20) NOT NULL,
  `database_question_id` bigint(20) NOT NULL,
  `database_answer_sql` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `database_answer_is_correct` tinyint(1) DEFAULT 0,
  `database_answer_submit_time` timestamp NOT NULL DEFAULT utc_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`uid`,`database_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of database_answer
-- ----------------------------

-- ----------------------------
-- Table structure for database_question
-- ----------------------------
DROP TABLE IF EXISTS `database_question`;
CREATE TABLE `database_question` (
  `database_question_id` bigint(20) NOT NULL,
  `database_question_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT '',
  `database_question_description` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `database_question_preload_sql` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `database_question_answer_hash` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `database_question_table_digest` text CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`database_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of database_question
-- ----------------------------
INSERT INTO `database_question` VALUES ('1', '示例问题1', '输出所有城市名称', 'CREATE TABLE cities (city string, population number);INSERT INTO cities VALUES (\'Rome\',2863223), (\'Paris\',2249975), (\'Berlin\',3517424),  (\'Madrid\',3041579);', '548b9b3b4f', 'cities (city, population)');

-- ----------------------------
-- Table structure for file_storage
-- ----------------------------
DROP TABLE IF EXISTS `file_storage`;
CREATE TABLE `file_storage` (
  `file_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `file_storage_path` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`file_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of file_storage
-- ----------------------------

-- ----------------------------
-- Table structure for log_login
-- ----------------------------
DROP TABLE IF EXISTS `log_login`;
CREATE TABLE `log_login` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `login_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(255) CHARACTER SET utf8 NOT NULL,
  `ua` text CHARACTER SET utf8 NOT NULL,
  `result` tinyint(1) NOT NULL,
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1779 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of log_login
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` bigint(20) unsigned NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `is_banned` tinyint(11) unsigned NOT NULL DEFAULT 0,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `is_student` tinyint(1) NOT NULL,
  `is_visitor` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('192168000000', '$2y$10$pdq3zXIUgfoBWPKRqWJ/du0MpFU9f4YCWMyhOxu8aD0hnAKhXsvhq', '0', 'mail@example.com', 'ROOT教师', '0', '0');

-- ----------------------------
-- Table structure for user_privilege
-- ----------------------------
DROP TABLE IF EXISTS `user_privilege`;
CREATE TABLE `user_privilege` (
  `uid` bigint(20) NOT NULL,
  `privilege_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `privilege_value` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`privilege_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of user_privilege
-- ----------------------------
INSERT INTO `user_privilege` VALUES ('192168000000', 'is_root_admin', '1');
INSERT INTO `user_privilege` VALUES ('192168000000', 'is_student_admin', '1');
INSERT INTO `user_privilege` VALUES ('192168000000', 'is_teacher_admin', '1');
