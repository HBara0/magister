
  `alias` varchar(220) DEFAULT NULL,
  `iatacode` varchar(4) DEFAULT NULL,
  `icaocode` varchar(4) DEFAULT NULL,
  `callSign` varchar(100) DEFAULT NULL,
  `coid` int(10) NOT NULL,
  `country` varchar(100) NOT NULL,
DROP TABLE IF EXISTS `travelmanager_hotels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_hotels` (
  `tmhid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `country` int(10) NOT NULL,
  `city` int(10) NOT NULL,
  `addressLine1` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine2` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postCode` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poBox` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mainEmail` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `stars` tinyint(1) NOT NULL,
  `mgmtReview` text COLLATE utf8_unicode_ci,
  `isContracted` tinyint(1) NOT NULL DEFAULT '0',
  `isApproved` tinyint(1) NOT NULL DEFAULT '0',
  `avgPrice` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`tmhid`),
  KEY `country` (`country`,`city`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;