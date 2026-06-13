-- MySQL dump 10.13  Distrib 8.4.9, for macos26.2 (arm64)
--
-- Host: 103.82.241.100    Database: db_leuwigoong
-- ------------------------------------------------------
-- Server version	5.5.68-MariaDB

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
-- Table structure for table `set_tempkontrol`
--

DROP TABLE IF EXISTS `set_tempkontrol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `set_tempkontrol` (
  `id_set` int(11) NOT NULL AUTO_INCREMENT,
  `id_logger` int(11) NOT NULL,
  `id_pintu` int(11) NOT NULL,
  `set_value` float NOT NULL,
  `status` int(11) NOT NULL,
  `sensor_kontrol` varchar(25) NOT NULL,
  PRIMARY KEY (`id_set`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `set_tempkontrol`
--

LOCK TABLES `set_tempkontrol` WRITE;
/*!40000 ALTER TABLE `set_tempkontrol` DISABLE KEYS */;
INSERT INTO `set_tempkontrol` VALUES (1,10349,210,0,0,'sensor10'),(2,10349,211,0,0,'sensor16'),(3,10349,212,0,0,'sensor22'),(4,10349,213,0,0,'sensor28'),(5,10350,214,0,0,'sensor10'),(6,10350,215,0,0,'sensor16'),(7,10350,216,0,0,'sensor22');
/*!40000 ALTER TABLE `set_tempkontrol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parameter_pintu`
--

DROP TABLE IF EXISTS `parameter_pintu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parameter_pintu` (
  `id_param` int(11) NOT NULL AUTO_INCREMENT,
  `id_pintu` int(11) NOT NULL,
  `nama_parameter` varchar(255) NOT NULL,
  `kolom_sensor` varchar(50) NOT NULL,
  `satuan` varchar(150) NOT NULL,
  `analisa` varchar(2) NOT NULL,
  `tipe_graf` varchar(255) NOT NULL,
  `icon_sensor` varchar(255) NOT NULL,
  PRIMARY KEY (`id_param`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parameter_pintu`
--

LOCK TABLES `parameter_pintu` WRITE;
/*!40000 ALTER TABLE `parameter_pintu` DISABLE KEYS */;
INSERT INTO `parameter_pintu` VALUES (1,210,'Level_Gate_Floodway_1','sensor5','cm','1','spline','GATE_LEVEL'),(2,210,'Phase_R','sensor6','VAC','','',''),(3,210,'Phase_S','sensor7','VAC','','',''),(4,210,'Phase_T','sensor8','VAC','','',''),(13,210,'Status_Controller','sensor9','','','',''),(14,211,'Level_Gate_Floodway_2','sensor11','cm','1','spline','GATE_LEVEL'),(15,211,'Phase_R','sensor12','VAC','','',''),(16,211,'Phase_S','sensor13','VAC','','',''),(17,211,'Phase_T','sensor14','VAC','','',''),(18,211,'Status_Controller','sensor15','','','',''),(19,212,'Level_Gate_Floodway_3','sensor17','cm','1','spline','GATE_LEVEL'),(20,212,'Phase_R','sensor18','VAC','','',''),(21,212,'Phase_S','sensor19','VAC','','',''),(22,212,'Phase_T','sensor20','VAC','','',''),(23,212,'Status_Controller','sensor21','','','',''),(24,213,'Level_Gate_Scouring','sensor23','dm','1','spline','GATE_LEVEL'),(25,213,'Phase_R','sensor24','VAC','','',''),(26,213,'Phase_S','sensor25','VAC','','',''),(27,213,'Phase_T','sensor26','VAC','','',''),(28,213,'Status_Controller','sensor27','','','',''),(29,214,'Level_Gate_Intake_1','sensor5','cm','1','spline','GATE_LEVEL'),(30,214,'Phase_R','sensor30','VAC','','',''),(31,214,'Phase_S','sensor31','VAC','','',''),(32,214,'Phase_T','sensor32','VAC','','',''),(33,214,'Status_Controller','sensor33','','','',''),(34,215,'Level_Gate_Intake_2','sensor11','cm','1','spline','GATE_LEVEL'),(35,215,'Phase_R','sensor36','VAC','','',''),(36,215,'Phase_S','sensor37','VAC','','',''),(37,215,'Phase_T','sensor38','VAC','','',''),(38,215,'Status_Controller','sensor39','','','',''),(39,210,'Status_AWGC_1','sensor10','','','',''),(40,211,'Status_AWGC_2','sensor16','','','',''),(41,212,'Status_AWGC_3','sensor22','','','',''),(42,213,'Status_AWGC_4','sensor28','','','',''),(43,214,'Status_AWGC_5','sensor34','','','',''),(44,216,'Status_AWGC_6','sensor40','','','',''),(45,216,'Level_Gate_Intake_3','sensor17','cm','1','spline','GATE_LEVEL'),(46,216,'Phase_R','sensor42','VAC','','',''),(47,216,'Phase_S','sensor43','VAC','','',''),(48,216,'Phase_T','sensor44','VAC','','',''),(49,216,'Status_Controller','sensor45','','','',''),(50,216,'Status_AWGC_7','sensor46','','','','');
/*!40000 ALTER TABLE `parameter_pintu` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-12 20:18:19
