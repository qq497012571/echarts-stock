-- MySQL dump 10.13  Distrib 8.0.32, for Linux (x86_64)
--
-- Host: localhost    Database: echarts_stock
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `stock_alarm`
--

DROP TABLE IF EXISTS `stock_alarm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_alarm` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT '用户ID',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '代码',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预警价格',
  `status` tinyint DEFAULT '0' COMMENT '报警状态: 0-未触发 1-已触发 2-已过期',
  `timing_type` tinyint DEFAULT '1' COMMENT '触发类型: 1-升破 2-跌破',
  `time_type` char(5) NOT NULL DEFAULT '' COMMENT '预警类型: ONCE-仅一次 ERVER-每次',
  `auto_destory` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '触发后是否删除: 1-是 0-否',
  `push_channel` varchar(255) NOT NULL DEFAULT 'web,email' COMMENT '通知渠道 web端通知, email通知',
  `expire_time` int unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `trigger_time` bigint unsigned NOT NULL DEFAULT '0' COMMENT '触发时间',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `is_del` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否删除: 1-是 0-否',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='股票警报提醒';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_alarm`
--

LOCK TABLES `stock_alarm` WRITE;
/*!40000 ALTER TABLE `stock_alarm` DISABLE KEYS */;
INSERT INTO `stock_alarm` VALUES (3,1,'SH601166',16.75,1,2,'ONCE',1,'web',1680429120,1679641200000,'','SH601166 跌破 16.75',1,'2023-03-26 17:52:26','2023-03-26 17:54:02'),(4,1,'SH601166',16.83,1,1,'ONCE',1,'web',1680429300,1679641200000,'','SH601166 升破 16.83',1,'2023-03-26 17:55:26','2023-03-26 17:55:31'),(5,1,'SH601166',16.83,1,1,'ONCE',1,'web',1680430440,1679641200000,'','SH601166 升破 16.83',1,'2023-03-26 18:14:21','2023-03-26 18:14:30'),(6,1,'SH601166',16.74,1,2,'ONCE',1,'web',1680430440,1679641200000,'','SH601166 跌破 16.74',1,'2023-03-26 18:14:36','2023-03-26 18:14:41'),(7,1,'SH601166',17.08,0,1,'ONCE',1,'web',1680431160,0,'','SH601166 升破 17.08',0,'2023-03-26 18:26:19','2023-03-26 18:26:20'),(8,1,'SZ000625',11.45,0,2,'ONCE',1,'web',1680433020,0,'','SZ000625 跌破 11.45',0,'2023-03-26 18:57:28','2023-03-26 18:57:45'),(9,1,'SH600600',109.40,1,2,'ONCE',1,'web',1680433620,1679641200000,'','SH600600 跌破 109.4',1,'2023-03-26 18:59:43','2023-03-26 19:07:31'),(10,1,'SH600600',109.77,1,2,'ONCE',1,'web',1680433620,1679641200000,'','SH600600 跌破 109.77',1,'2023-03-26 19:07:37','2023-03-26 19:07:42');
/*!40000 ALTER TABLE `stock_alarm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_mark`
--

DROP TABLE IF EXISTS `stock_mark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_mark` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '股票代码',
  `user_id` int unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `overlay_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '标记位置',
  `mark_type` varchar(50) NOT NULL DEFAULT '' COMMENT '标记类型: alarm_line-报警线',
  `alarm_id` int unsigned NOT NULL DEFAULT '0' COMMENT '预警id',
  `option` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '标记配置, json配置',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_mark`
--

LOCK TABLES `stock_mark` WRITE;
/*!40000 ALTER TABLE `stock_mark` DISABLE KEYS */;
INSERT INTO `stock_mark` VALUES (7,'SH601166',1,'overlay_1679826378068_1','alarm_line',7,'{\"currentStep\":-1,\"points\":[{\"timestamp\":1668700800000,\"dataIndex\":199,\"value\":17.084656666666675}],\"_prevPressedPoint\":null,\"_prevPressedPoints\":[],\"name\":\"alarm_line\",\"totalStep\":2,\"lock\":false,\"visible\":true,\"needDefaultPointFigure\":true,\"needDefaultXAxisFigure\":true,\"needDefaultYAxisFigure\":true,\"mode\":\"normal\",\"extendData\":{\"newbar\":{\"timestamp\":1679587200000,\"volume\":37820612,\"open\":16.85,\"high\":16.88,\"low\":16.72,\"close\":16.78,\"chg\":-0.09,\"percent\":-0.53,\"turnoverrate\":0.18,\"amount\":635351703,\"volume_post\":null,\"amount_post\":null},\"code\":\"SH601166\"},\"styles\":{\"line\":{\"style\":\"dashed\",\"color\":\"red\",\"dashedValue\":[4,4]}},\"createXAxisFigures\":null,\"createYAxisFigures\":null,\"performEventPressedMove\":null,\"performEventMoveForDrawing\":null,\"onDrawStart\":null,\"onDrawing\":null,\"onClick\":null,\"onRightClick\":null,\"onPressedMoveStart\":null,\"onPressedMoving\":null,\"onMouseEnter\":null,\"onMouseLeave\":null,\"onSelected\":null,\"onDeselected\":null,\"id\":\"overlay_1679826378068_1\",\"groupId\":\"overlay_1679826378068_1\"}','2023-03-26 18:26:19','2023-03-26 18:26:19'),(8,'SZ000625',1,'overlay_1679828247282_1','alarm_line',8,'{\"currentStep\":-1,\"points\":[{\"value\":11.451161431064572}],\"_prevPressedPoint\":{\"dataIndex\":423,\"timestamp\":1679414400000,\"value\":13.329565445026178},\"_prevPressedPoints\":[{\"value\":13.375433682373473}],\"name\":\"alarm_line\",\"totalStep\":2,\"lock\":false,\"visible\":true,\"needDefaultPointFigure\":true,\"needDefaultXAxisFigure\":true,\"needDefaultYAxisFigure\":true,\"mode\":\"normal\",\"extendData\":{\"newbar\":{\"timestamp\":1679587200000,\"volume\":63506941,\"open\":11.81,\"high\":11.85,\"low\":11.72,\"close\":11.77,\"chg\":-0.03,\"percent\":-0.25,\"turnoverrate\":0.83,\"amount\":748421885,\"volume_post\":null,\"amount_post\":null},\"code\":\"SZ000625\"},\"styles\":{\"line\":{\"style\":\"dashed\",\"color\":\"red\",\"dashedValue\":[4,4]}},\"createXAxisFigures\":null,\"createYAxisFigures\":null,\"performEventPressedMove\":null,\"performEventMoveForDrawing\":null,\"onDrawStart\":null,\"onDrawing\":null,\"onClick\":null,\"onRightClick\":null,\"onPressedMoveStart\":null,\"onPressedMoving\":null,\"onMouseEnter\":null,\"onMouseLeave\":null,\"onSelected\":null,\"onDeselected\":null,\"id\":\"overlay_1679828247282_1\",\"groupId\":\"overlay_1679828247282_1\"}','2023-03-26 18:57:28','2023-03-26 18:57:45');
/*!40000 ALTER TABLE `stock_mark` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_market`
--

DROP TABLE IF EXISTS `stock_market`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_market` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` int unsigned NOT NULL DEFAULT '0' COMMENT '代码',
  `symbol` varchar(50) NOT NULL DEFAULT '' COMMENT '股票代码',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '股票状态',
  `exchange` char(2) NOT NULL COMMENT '交易所',
  `current` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '当前价格',
  `open` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '开盘价',
  `high` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '最高价',
  `low` decimal(10,2) DEFAULT '0.00' COMMENT '最低价',
  `percent` tinyint DEFAULT '0' COMMENT '涨跌幅',
  `chg` tinyint DEFAULT '0' COMMENT '涨跌额',
  `volume` int unsigned NOT NULL DEFAULT '0' COMMENT '成交量',
  `amount` bigint unsigned NOT NULL DEFAULT '0' COMMENT '成交额',
  `market_capital` bigint unsigned NOT NULL DEFAULT '0' COMMENT '市值',
  `timestamp` tinyint DEFAULT NULL COMMENT '时间戳',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '收盘时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='股票市场表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_market`
--

LOCK TABLES `stock_market` WRITE;
/*!40000 ALTER TABLE `stock_market` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_market` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `xueqiu_cookie` text COMMENT '雪球cookie',
  `xueqiu_result` text COMMENT '雪球用户登录信息',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'497012571@qq.com','jlb','device_id=d0c2ab3e20f0efab89b9b2a3d2bd20d1;acw_tc=2760826d16797308959006754e12a28c039f94ad04412ac8100785f53aac6b;remember=;xq_a_token=92c395900bf9ac802a4072b81013daffdb344d99;xqat=92c395900bf9ac802a4072b81013daffdb344d99;xq_id_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOjI4MDYyNjAwNjYsImlzcyI6InVjIiwiZXhwIjoxNjgyMTU3NDE1LCJjdG0iOjE2Nzk3MzA5MTA0OTksImNpZCI6ImQ5ZDBuNEFadXAifQ.pkTF1BFC7dTwWug-pxgdKvG3IfuMSwEIWgpjpUcb7Zj9WhQOVTHRkRSIxugdpm0f468hmC4SUfTD9yzKzQuHKDh8DgwDYS-TO7rYlV1C9lKaS-K3rRCNKMrSK7jTX9wilw1au8Gcp7Z9CbOxZ-A-8nsSIjfrjd9Q8mKtFV20ZpqLCjrA3oV7LeF6XQ5zSfi9yjXAhgxQLtupRBPuWfvQ2NkSVo03IML9ZrMoBGKsfvfosnPuH2zBlSB1Hp1az_6ETSYBfOrYPoN_CZAxiBSJqDZ56piMZ2STfQie7l-3M6dNbA6ETLnC4cw3gjsgUTHL9uS0fv_ozsN4H9l_Tvn0zA;xq_r_token=37668350010e6b3c76b4591feaf9041fc62155fc;xq_is_login=1;u=2806260066','{\"status\":2,\"login_success\":true,\"access_token\":\"92c395900bf9ac802a4072b81013daffdb344d99\",\"expires_in\":40441,\"refresh_token\":\"37668350010e6b3c76b4591feaf9041fc62155fc\",\"uid\":2806260066,\"scope\":\"all\",\"is_new\":false,\"id_token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOjI4MDYyNjAwNjYsImlzcyI6InVjIiwiZXhwIjoxNjgyMTU3NDE1LCJjdG0iOjE2Nzk3MzA5MTA0OTksImNpZCI6ImQ5ZDBuNEFadXAifQ.pkTF1BFC7dTwWug-pxgdKvG3IfuMSwEIWgpjpUcb7Zj9WhQOVTHRkRSIxugdpm0f468hmC4SUfTD9yzKzQuHKDh8DgwDYS-TO7rYlV1C9lKaS-K3rRCNKMrSK7jTX9wilw1au8Gcp7Z9CbOxZ-A-8nsSIjfrjd9Q8mKtFV20ZpqLCjrA3oV7LeF6XQ5zSfi9yjXAhgxQLtupRBPuWfvQ2NkSVo03IML9ZrMoBGKsfvfosnPuH2zBlSB1Hp1az_6ETSYBfOrYPoN_CZAxiBSJqDZ56piMZ2STfQie7l-3M6dNbA6ETLnC4cw3gjsgUTHL9uS0fv_ozsN4H9l_Tvn0zA\",\"user\":{\"id\":2806260066,\"screen_name\":\"且听风吟dvd\",\"name\":null,\"province\":null,\"city\":null,\"location\":null,\"description\":null,\"url\":null,\"domain\":null,\"gender\":null,\"verified\":null,\"created_at\":1554294000173,\"areaCode\":\"86\",\"type\":\"1\",\"followers_count\":null,\"friends_count\":null,\"status_count\":null,\"last_status_id\":null,\"last_comment_id\":null,\"step\":\"null\",\"verified_description\":null,\"blog_description\":null,\"profile\":\"\\/2806260066\",\"recommend\":null,\"stock_status_count\":null,\"intro\":null,\"status\":0,\"st_color\":\"1\",\"following\":false,\"follow_me\":false,\"blocking\":false,\"allow_all_stock\":false,\"truncated\":false,\"stocks_count\":null,\"verified_type\":null,\"ability\":null,\"donate_snowcoin\":0,\"donate_count\":0,\"lastRecordAt\":null,\"maskedEmail\":null,\"anonymous\":false,\"reg_time\":0,\"profile_image_url\":\"community\\/20193\\/1554294012472-1554294012599.jpg,community\\/20193\\/1554294012472-1554294012599.jpg!180x180.png,community\\/20193\\/1554294012472-1554294012599.jpg!50x50.png,community\\/20193\\/1554294012472-1554294012599.jpg!30x30.png\",\"photo_domain\":\"\\/\\/xavatar.imedao.com\\/\",\"fund_cube_count\":null,\"cube_count\":null}}',NULL,'2023-03-25 15:55:11'),(2,'aaaa','aaaa',NULL,NULL,NULL,'2023-03-20 22:28:47'),(3,'497012257@qq.com','497012257@qq.com',NULL,NULL,'2023-03-25 13:23:17','2023-03-25 13:23:17');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_stock`
--

DROP TABLE IF EXISTS `user_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_stock` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '股票名称',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '股票代码',
  `user_id` int unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `ref` varchar(50) NOT NULL DEFAULT '' COMMENT '股票来源: dfcf-东方财富 xq-雪球',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_stock`
--

LOCK TABLES `user_stock` WRITE;
/*!40000 ALTER TABLE `user_stock` DISABLE KEYS */;
INSERT INTO `user_stock` VALUES (16,'青岛啤酒','SH600600',1,'','2023-03-26 18:58:51','2023-03-26 18:58:53');
/*!40000 ALTER TABLE `user_stock` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-03-27 13:34:09
