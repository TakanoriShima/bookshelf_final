-- MySQL dump 10.13  Distrib 5.7.35, for Linux (x86_64)
--
-- Host: localhost    Database: bookshelf
-- ------------------------------------------------------
-- Server version	5.7.35

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `books`
--
DROP DATABASE IF EXISTS `bookshelf_final`;

CREATE DATABASE `bookshelf_final` DEFAULT CHARACTER SET UTF8;

USE `bookshelf_final`;

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `image_url` varchar(100) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (2,'詳しい解説付き！HTML5','./images/item_book_4.jpg','finished','2021-11-09 09:39:36','pass'),(3,'実践Webアプリケーション開発','./images/item_book_3.jpg','reading','2021-11-09 09:41:05','pass'),(4,'実践で学ぶSEO入門','./images/item_book_2.jpg','reading','2021-11-09 09:41:56','pass'),(5,'初めてのプログラミング','./images/item_book_1.jpg','pending','2021-11-09 09:42:34','pass'),(7,'コミック','./uploads/91UvGWmqdkL.jpg','finished','2021-11-15 04:10:20','pass'),(8,'PHP（下）','./uploads/41vb-rC5lOL._AC_SY200_.jpg','unread','2021-11-15 04:33:00','pass'),(9,'PHP（上）','./uploads/41zPn5lFAgL._AC_SY200_.jpg','pending','2021-11-17 07:22:45','pass'),(10,'実践　PHP+MySQL入門','./uploads/41N2XZC06FL._AC_SY200_.jpg','unread','2021-11-20 05:06:17','pass'),(13,'Numbers 久保','./uploads/100000009003176744_10203_001.jpg','reading','2021-12-09 03:35:42','pass'),(14,'人を動かす','./uploads/41LVhY-YlhL._AC_SY200_.jpg','finished','2021-12-16 00:47:57','hoge');
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-12-16 10:07:17

create table users (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL UNIQUE,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
UPDATE users SET id=1;
ALTER TABLE books ADD user_id int NOT NULL;
UPDATE books SET user_id=1;
ALTER TABLE books ADD FOREIGN KEY(user_id) REFERENCES users(id);
