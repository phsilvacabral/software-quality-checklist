-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: checklist
-- ------------------------------------------------------
-- Server version	8.0.34

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `checklist`
--

DROP TABLE IF EXISTS `checklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checklist` (
  `Cod_Checklist` int NOT NULL AUTO_INCREMENT,
  `Titulo` varchar(25) NOT NULL,
  `Descricao` varchar(120) DEFAULT NULL,
  `Criador` int DEFAULT NULL,
  `Dt_Modificacao` datetime DEFAULT NULL,
  PRIMARY KEY (`Cod_Checklist`),
  KEY `FK_Checklist_2` (`Criador`),
  CONSTRAINT `FK_Checklist_2` FOREIGN KEY (`Criador`) REFERENCES `usuario` (`Cod_Usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checklist`
--

LOCK TABLES `checklist` WRITE;
/*!40000 ALTER TABLE `checklist` DISABLE KEYS */;
INSERT INTO `checklist` VALUES (1,'Checklist','Checklist de auditoria da materia de qualidade de software',1,'2024-10-20 23:24:47');
/*!40000 ALTER TABLE `checklist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itemchecklist`
--

DROP TABLE IF EXISTS `itemchecklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itemchecklist` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `fk_Cod_Checklist` int NOT NULL,
  `Nome` varchar(120) DEFAULT NULL,
  `Complexidade` varchar(5) DEFAULT NULL,
  `Responsavel` int DEFAULT NULL,
  `Conforme` char(2) DEFAULT NULL,
  `Escalonamento` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`ID`,`fk_Cod_Checklist`),
  KEY `FK_ItemChecklist_2` (`Responsavel`),
  KEY `FK_ItemChecklist_3` (`fk_Cod_Checklist`),
  CONSTRAINT `FK_ItemChecklist_2` FOREIGN KEY (`Responsavel`) REFERENCES `usuario` (`Cod_Usuario`) ON DELETE RESTRICT,
  CONSTRAINT `FK_ItemChecklist_3` FOREIGN KEY (`fk_Cod_Checklist`) REFERENCES `checklist` (`Cod_Checklist`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itemchecklist`
--

LOCK TABLES `itemchecklist` WRITE;
/*!40000 ALTER TABLE `itemchecklist` DISABLE KEYS */;
INSERT INTO `itemchecklist` VALUES (1,1,'Existe um plano de capacitação documentado para a equipe?','Alta',1,'NC','1'),(2,1,'A equipe recebe treinamento regular nas tecnologias utilizadas no projeto?','Baixa',2,'CC','1'),(3,1,'Há um processo para identificar as necessidades de capacitação da equipe?\n','Media',3,'CC','2'),(4,1,'A empresa fornece acesso a recursos de aprendizado, como cursos ou workshops?\n','Media',1,'NA','1'),(5,1,'Existe um registro das capacitações realizadas por cada colaborador?','Alta',1,'CC','1'),(6,1,'Há um processo para acompanhar o progresso dos colaboradores em relação ao plano de capacitação?','Baixa',3,'CC','1'),(7,1,'Os treinamentos são ministrados por profissionais qualificados?','Baixa',2,'CC','1'),(8,1,'Existe um orçamento específico para capacitação e desenvolvimento de pessoas?','Media',2,'CC','1'),(9,1,'A empresa promove a participação em conferências e eventos do setor?','Media',1,'CC','1'),(10,1,'Existe um programa de mentoria ou coaching para novos colaboradores?\n','Media',3,'CC','1'),(11,1,'A equipe tem acesso a materiais de leitura e atualização sobre novas práticas do setor?\n','Baixa',2,'CC','1'),(12,1,'Os treinamentos são avaliados quanto à sua eficácia?\n','Alta',1,'CC','1'),(13,1,'Há um processo de feedback após as capacitações para identificar melhorias?\n','Medai',2,'CC','1'),(14,1,'Os colaboradores têm liberdade para sugerir temas de capacitação?\n','Alta',3,'CC','1'),(15,1,'A capacitação considera as metas estratégicas da empresa?\n','Baixa',2,'CC','1'),(16,1,'Existe um controle sobre a carga horária dedicada à capacitação?\n','Baixa',1,'CC','1'),(17,1,'Os programas de capacitação estão alinhados com as competências necessárias para os projetos?\n','Media',2,'CC','1'),(18,1,'A empresa realiza ações para reter o conhecimento adquirido pela equipe?\n','Alta',3,'CC','1');
/*!40000 ALTER TABLE `itemchecklist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `Cod_Usuario` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(12) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Senha` varchar(12) NOT NULL,
  `Tipo` char(1) NOT NULL,
  PRIMARY KEY (`Cod_Usuario`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Pedro','phsilvacabral@gmail.com','123','1'),(2,'João','joao@gmail.com','123','1'),(3,'daniel','daniel@gmail.com','123','1'),(4,'pedro','ph@gmail.com','123','2'),(5,'Pedro','phsilvacabrall@gmail.com','123','2'),(6,'Pedro','crianecabral.2011@gmail.com','123','1'),(7,'pedro','pedro@gmail.com','123','2');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-21 22:40:18
