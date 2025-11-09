/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.5-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: mi_tienda_base
-- ------------------------------------------------------
-- Server version	11.4.5-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `detalle`
--

DROP TABLE IF EXISTS `detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle` (
  `codigo_pedido` int(11) NOT NULL,
  `codigo_producto` int(11) NOT NULL,
  `unidades` int(11) DEFAULT 1,
  `precio_unitario` decimal(8,2) DEFAULT 0.00,
  PRIMARY KEY (`codigo_pedido`,`codigo_producto`),
  KEY `contiene` (`codigo_producto`),
  CONSTRAINT `contiene` FOREIGN KEY (`codigo_producto`) REFERENCES `productos` (`codigo`),
  CONSTRAINT `referentea` FOREIGN KEY (`codigo_pedido`) REFERENCES `pedidos` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle`
--

/*!40000 ALTER TABLE `detalle` DISABLE KEYS */;
INSERT INTO `detalle` VALUES
(4,1,1,27.00),
(4,2,1,33.00),
(4,3,1,30.00),
(4,7,1,30.00),
(4,10,1,40.00),
(4,12,1,50.00),
(4,13,1,30.00),
(4,49,1,30.00),
(5,9,1,60.00),
(6,13,1,30.00),
(7,14,1,30.00),
(8,8,1,200.00),
(9,11,1,30.00),
(10,5,1,30.00),
(11,4,1,30.00),
(12,1,1,27.00),
(12,15,1,45.00),
(13,2,1,33.00),
(14,12,1,50.00),
(15,49,1,30.00),
(16,10,1,40.00),
(17,1,2,27.00),
(18,2,1,33.00),
(18,3,1,30.00),
(19,2,1,33.00);
/*!40000 ALTER TABLE `detalle` ENABLE KEYS */;

--
-- Table structure for table `estados`
--

DROP TABLE IF EXISTS `estados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados`
--

/*!40000 ALTER TABLE `estados` DISABLE KEYS */;
INSERT INTO `estados` VALUES
(1,'PENDIENTE'),
(2,'PROCESADO'),
(3,'ENVIADO'),
(4,'ENTREGADO'),
(5,'CANCELADO');
/*!40000 ALTER TABLE `estados` ENABLE KEYS */;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `persona` int(11) NOT NULL,
  `fecha` date DEFAULT curdate(),
  `importe` decimal(8,2) DEFAULT 0.00,
  `estado` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `pedidopor` (`persona`),
  KEY `enestado` (`estado`),
  CONSTRAINT `enestado` FOREIGN KEY (`estado`) REFERENCES `estados` (`codigo`),
  CONSTRAINT `pedidopor` FOREIGN KEY (`persona`) REFERENCES `usuarios` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES
(4,1,'2025-05-06',270.00,3),
(5,1,'2025-05-06',60.00,5),
(6,1,'2025-05-06',30.00,5),
(7,1,'2025-05-06',30.00,5),
(8,1,'2025-05-06',200.00,5),
(9,1,'2025-05-06',30.00,5),
(10,1,'2025-05-06',30.00,5),
(11,1,'2025-05-06',30.00,5),
(12,1,'2025-05-06',72.00,5),
(13,1,'2025-05-06',33.00,5),
(14,1,'2025-05-06',50.00,5),
(15,1,'2025-05-06',30.00,5),
(16,1,'2025-05-08',40.00,3),
(17,3,'2025-05-09',54.00,5),
(18,3,'2025-05-09',63.00,4),
(19,3,'2025-05-09',33.00,5);
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `precio` decimal(8,2) DEFAULT 0.00,
  `existencias` int(11) DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `nombreAlbum` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES
(1,'Artista: Kendrick Lamar',27.00,3,'GNX.jpg','GNX'),
(2,'Artista: Clairo',33.00,997,'Clairo-Charm.webp','Charm'),
(3,'Artista: El Alfa',30.00,999,'el_rey_del_dembow-El_Alfa.jpg','EL REY DEL DEMBOW'),
(4,'Artista: Dua Lipa',30.00,999,'Future_Nostalgia-Dua_Lipa.jpg','Future Nostalgia'),
(5,'Artista: The Weekend',30.00,0,'Starboy-The_Weekend.jpg','Starboy'),
(6,'Artista: Romeo Santos',30.00,1000,'Utopia-Romeo_Santos.jpg','Utopia'),
(7,'Artista: Bad Bunny',30.00,998,'x100pre-Bad_Bunny.jpg','X 100pre'),
(8,'Artista: Juan Luis Guerra 440',200.00,19,'Bachata_Rosa-Juan_Luis_Guerra_440.jpg','Bachata Rosa'),
(9,'Artista: MFDOOM-Madlib',60.00,4,'Madvillany-MFDOOM.jpg','Madvillany'),
(10,'Artista: Mac Miller',40.00,997,'Circles-Mac_Miller.jpg','Circles'),
(11,'Artista: Rosalia',30.00,999,'Moto_Mami-Rosalia.jpg','MOTOMAMI'),
(12,'Artista: Denzel Curry',50.00,497,'Melt_your_eyes_see_my_future-Denzel_Curry.jpg','Melt My Eyes See Your Future'),
(13,'Artista: J.Cole',30.00,998,'2014_Forest_Hills_Drive-Jcole.jpg','2014 Forest Hills Drive'),
(14,'Artista: Myke Towers',30.00,999,'Easy_Money_Baby-Myke_Towers.jpg','Easy Money Baby'),
(15,'Artista: Eladio Carrion',45.00,999,'Sol_Maria-Eladio_KBRN.jpg','Sol Maria'),
(16,'Artistas: JPEGMAFIA,Danny Brown',500.00,0,'Scaring_the_hoes-JPEGMAFIA.jpg','SCARING THE HOES'),
(49,'Artista: Bad Bunny',30.00,997,'badbo1.jpg','DeBÍ TiRAR MáS FOToS');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(32) NOT NULL,
  `clave` varchar(40) DEFAULT '',
  `activo` int(11) DEFAULT 1,
  `admin` int(11) DEFAULT 0,
  `nombre` varchar(64) DEFAULT NULL,
  `apellidos` varchar(128) DEFAULT NULL,
  `domicilio` varchar(128) DEFAULT NULL,
  `poblacion` varchar(64) DEFAULT NULL,
  `provincia` varchar(32) DEFAULT NULL,
  `cp` char(5) DEFAULT NULL,
  `telefono` char(9) DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES
(1,'diegodrou','1234567890',1,1,'Diego','Droulers','Costa verde calle 6 n 4',NULL,NULL,NULL,'076913928'),
(2,'Nose','56789123',1,0,'Blu','Olivo','La duarte',NULL,NULL,NULL,'294120'),
(3,'ETSE','ETSE12345',1,0,'Diego','De La Cruz','Madrid',NULL,NULL,NULL,'435234534');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;

--
-- Dumping routines for database 'mi_tienda_base'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-05-25 15:55:24
