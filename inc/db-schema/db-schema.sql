
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
DROP TABLE IF EXISTS `affiliatedemployees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliatedemployees` (
  `aeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `isMain` tinyint(1) NOT NULL DEFAULT '0',
  `canHR` tinyint(1) NOT NULL DEFAULT '0',
  `canAudit` tinyint(1) NOT NULL DEFAULT '0',
  `since` bigint(30) unsigned NOT NULL,
  `title` varchar(220) DEFAULT NULL,
  PRIMARY KEY (`aeid`,`affid`,`uid`),
  KEY `affid` (`affid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=4181 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliatedentities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliatedentities` (
  `aeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) unsigned NOT NULL,
  `eid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`aeid`,`affid`,`eid`),
  KEY `affid` (`affid`,`eid`)
) ENGINE=MyISAM AUTO_INCREMENT=17936 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliates` (
  `affid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `brandingColor` varchar(7) DEFAULT NULL,
  `alias` varchar(110) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `legalName` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `generalManager` int(10) unsigned NOT NULL,
  `supervisor` int(10) unsigned NOT NULL,
  `hrManager` int(10) unsigned NOT NULL,
  `finManager` int(10) unsigned DEFAULT NULL,
  `mailingList` varchar(200) NOT NULL,
  `altMailingList` varchar(200) NOT NULL,
  `vacanciesEmail` varchar(220) NOT NULL,
  `description` text,
  `country` int(10) unsigned NOT NULL,
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `postCode` varchar(10) DEFAULT NULL,
  `addressLine1` varchar(200) DEFAULT NULL,
  `addressLine2` varchar(100) DEFAULT NULL,
  `floor` tinyint(3) DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `poBox` int(10) DEFAULT NULL,
  `mainEmail` varchar(220) NOT NULL,
  `website` varchar(220) DEFAULT NULL,
  `qrAlwaysCopy` text NOT NULL,
  `vrAlwaysNotify` text,
  `defaultWorkshift` smallint(10) NOT NULL,
  `integrationOBOrgId` varchar(32) DEFAULT NULL,
  `defaultLang` varchar(50) NOT NULL DEFAULT 'english',
  `mainCurrency` int(3) DEFAULT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `isIntReinvoiceAffiliate` tinyint(1) NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`affid`),
  KEY `name` (`name`),
  KEY `generalManager` (`generalManager`,`supervisor`,`hrManager`),
  KEY `country` (`country`),
  KEY `defaultWorkshift` (`defaultWorkshift`),
  KEY `geoLocation` (`geoLocation`(25)),
  KEY `finManager` (`finManager`),
  FULLTEXT KEY `name_2` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliates2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliates2` (
  `affid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(110) NOT NULL,
  `name` varchar(220) NOT NULL,
  `legalName` varchar(220) NOT NULL,
  `generalManager` int(10) unsigned NOT NULL,
  `supervisor` int(10) unsigned NOT NULL,
  `hrManager` int(10) unsigned NOT NULL,
  `finManager` int(10) unsigned DEFAULT NULL,
  `mailingList` varchar(200) NOT NULL,
  `altMailingList` varchar(200) NOT NULL,
  `description` text,
  `country` int(10) unsigned NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postCode` varchar(10) DEFAULT NULL,
  `addressLine1` varchar(200) DEFAULT NULL,
  `addressLine2` varchar(100) DEFAULT NULL,
  `floor` tinyint(3) DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `poBox` int(10) DEFAULT NULL,
  `mainEmail` varchar(220) NOT NULL,
  `qrAlwaysCopy` text NOT NULL,
  `vrAlwaysNotify` text,
  `defaultWorkshift` smallint(10) NOT NULL,
  `integrationOBOrgId` varchar(32) DEFAULT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `defaultLang` varchar(100) NOT NULL DEFAULT 'english',
  PRIMARY KEY (`affid`),
  KEY `name` (`name`),
  KEY `generalManager` (`generalManager`,`supervisor`,`hrManager`),
  KEY `country` (`country`),
  KEY `defaultWorkshift` (`defaultWorkshift`),
  KEY `geoLocation` (`geoLocation`(25)),
  KEY `finManager` (`finManager`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliates3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliates3` (
  `affid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(110) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `legalName` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `generalManager` int(10) unsigned NOT NULL,
  `supervisor` int(10) unsigned NOT NULL,
  `hrManager` int(10) unsigned NOT NULL,
  `finManager` int(10) unsigned DEFAULT NULL,
  `coo` int(10) DEFAULT NULL,
  `regionalSupervisor` int(10) DEFAULT NULL,
  `globalPurchaseManager` int(10) DEFAULT NULL,
  `cfo` int(10) DEFAULT NULL,
  `logisticsManager` int(10) DEFAULT NULL,
  `vacanciesEmail` varchar(220) DEFAULT NULL,
  `mailingList` varchar(200) NOT NULL,
  `altMailingList` varchar(200) NOT NULL,
  `description` text,
  `country` int(10) unsigned NOT NULL,
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `postCode` varchar(10) DEFAULT NULL,
  `addressLine1` varchar(200) DEFAULT NULL,
  `addressLine2` varchar(100) DEFAULT NULL,
  `floor` tinyint(3) DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `poBox` int(10) DEFAULT NULL,
  `mainEmail` varchar(220) NOT NULL,
  `website` varchar(220) DEFAULT NULL,
  `qrAlwaysCopy` text NOT NULL,
  `vrAlwaysNotify` text,
  `defaultWorkshift` smallint(10) NOT NULL,
  `integrationOBOrgId` varchar(32) DEFAULT NULL,
  `defaultLang` varchar(50) NOT NULL DEFAULT 'english',
  `mainCurrency` int(3) DEFAULT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `isIntReinvoiceAffiliate` tinyint(1) NOT NULL DEFAULT '0',
  `chartSpec` varchar(250) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `chartColor` varchar(6) NOT NULL,
  PRIMARY KEY (`affid`),
  KEY `name` (`name`),
  KEY `generalManager` (`generalManager`,`supervisor`,`hrManager`),
  KEY `country` (`country`),
  KEY `defaultWorkshift` (`defaultWorkshift`),
  KEY `geoLocation` (`geoLocation`(25)),
  KEY `finManager` (`finManager`),
  FULLTEXT KEY `name_2` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliates_accountingtree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliates_accountingtree` (
  `affid` smallint(5) NOT NULL,
  `acckey` varchar(15) CHARACTER SET latin1 NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(10) CHARACTER SET latin1 NOT NULL,
  `sign` varchar(1) CHARACTER SET latin1 NOT NULL,
  `document` varchar(2) CHARACTER SET latin1 NOT NULL,
  `summary` varchar(3) CHARACTER SET latin1 NOT NULL,
  `default` varchar(200) CHARACTER SET latin1 NOT NULL,
  `parent` int(15) NOT NULL,
  `level` varchar(1) CHARACTER SET latin1 NOT NULL,
  `operands` varchar(10) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`affid`,`acckey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliates_commissiondist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliates_commissiondist` (
  `acdid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `stid` smallint(10) NOT NULL,
  `spid` int(10) NOT NULL,
  `percentage` float NOT NULL DEFAULT '0',
  `invoicingAffid` smallint(5) NOT NULL DEFAULT '0',
  `effectiveFrom` bigint(30) NOT NULL,
  `effectiveTo` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`acdid`),
  KEY `affid` (`affid`,`stid`,`spid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliates_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliates_temp` (
  `affid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(110) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `legalName` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `generalManager` int(10) unsigned NOT NULL,
  `supervisor` int(10) unsigned NOT NULL,
  `hrManager` int(10) unsigned NOT NULL,
  `finManager` int(10) unsigned DEFAULT NULL,
  `coo` int(10) DEFAULT NULL,
  `regionalSupervisor` int(10) DEFAULT NULL,
  `globalPurchaseManager` int(10) DEFAULT NULL,
  `cfo` int(10) DEFAULT NULL,
  `logisticsManager` int(10) DEFAULT NULL,
  `vacanciesEmail` varchar(220) DEFAULT NULL,
  `mailingList` varchar(200) NOT NULL,
  `altMailingList` varchar(200) NOT NULL,
  `description` text,
  `country` int(10) unsigned NOT NULL,
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `postCode` varchar(10) DEFAULT NULL,
  `addressLine1` varchar(200) DEFAULT NULL,
  `addressLine2` varchar(100) DEFAULT NULL,
  `floor` tinyint(3) DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `poBox` int(10) DEFAULT NULL,
  `mainEmail` varchar(220) NOT NULL,
  `website` varchar(220) DEFAULT NULL,
  `qrAlwaysCopy` text NOT NULL,
  `vrAlwaysNotify` text,
  `defaultWorkshift` smallint(10) NOT NULL,
  `integrationOBOrgId` varchar(32) DEFAULT NULL,
  `defaultLang` varchar(50) NOT NULL DEFAULT 'english',
  `mainCurrency` int(3) DEFAULT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `isIntReinvoiceAffiliate` tinyint(1) NOT NULL DEFAULT '0',
  `chartSpec` varchar(250) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `brandingColor` varchar(6) NOT NULL,
  PRIMARY KEY (`affid`),
  KEY `name` (`name`),
  KEY `generalManager` (`generalManager`,`supervisor`,`hrManager`),
  KEY `country` (`country`),
  KEY `defaultWorkshift` (`defaultWorkshift`),
  KEY `geoLocation` (`geoLocation`(25)),
  KEY `finManager` (`finManager`),
  FULLTEXT KEY `name_2` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliatesleavespolicies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliatesleavespolicies` (
  `alpid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` int(10) unsigned NOT NULL,
  `ltid` smallint(5) unsigned NOT NULL,
  `promotionPolicy` text,
  `basicEntitlement` int(2) NOT NULL,
  `canAccumulateFor` tinyint(2) NOT NULL,
  `maxAccumulateDays` int(2) NOT NULL DEFAULT '0',
  `entitleAfter` tinyint(2) NOT NULL,
  `oneTimeBonusDays` tinyint(2) DEFAULT NULL,
  `oneTimeBonusAfter` tinyint(2) DEFAULT NULL,
  `halfDayMargin` float NOT NULL DEFAULT '0',
  `useFirstJobDate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`alpid`,`affid`,`ltid`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_approvalchain_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_approvalchain_policies` (
  `aapcid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` int(10) NOT NULL,
  `purchaseType` int(10) NOT NULL,
  `effectiveFrom` bigint(30) NOT NULL,
  `effectiveTo` bigint(30) NOT NULL,
  `approvalChain` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `informCoordinators` tinyint(1) NOT NULL,
  `informGlobalCFO` tinyint(1) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`aapcid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_documentsequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_documentsequences` (
  `adsid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(10) NOT NULL,
  `ptid` int(10) NOT NULL,
  `effectiveFrom` bigint(30) NOT NULL,
  `effectiveTo` bigint(30) NOT NULL,
  `prefix` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `incrementBy` smallint(100) NOT NULL,
  `nextNumber` smallint(100) NOT NULL,
  `suffix` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`adsid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_netmargin_parameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_netmargin_parameters` (
  `anpid` int(10) NOT NULL AUTO_INCREMENT,
  `aorid` int(10) NOT NULL,
  `localBankInterestRate` float NOT NULL,
  `localPeriodOfInterest` int(11) NOT NULL,
  `localRiskRatio` float NOT NULL,
  `intermedBankInterestRate` float NOT NULL,
  `intermedPeriodOfInterest` int(11) NOT NULL,
  `intermedRiskRatio` float NOT NULL,
  `warehouse` int(10) NOT NULL,
  `warehousingRate` float NOT NULL,
  `warehousingPeriod` int(11) NOT NULL,
  `warehousingTotalLoad` float NOT NULL,
  `uom` smallint(10) NOT NULL,
  `interestValue` float NOT NULL,
  PRIMARY KEY (`anpid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_order_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_order_customers` (
  `aocid` int(10) NOT NULL AUTO_INCREMENT,
  `aorid` int(10) NOT NULL,
  `ptid` int(10) NOT NULL,
  `paymentTermDesc` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `paymentTermBaseDate` bigint(30) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `inputChecksum` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cid` int(10) NOT NULL,
  PRIMARY KEY (`aocid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_policies` (
  `apid` int(11) NOT NULL AUTO_INCREMENT,
  `affid` smallint(6) NOT NULL,
  `purchaseType` int(11) NOT NULL,
  `effectiveFrom` bigint(30) NOT NULL,
  `effectiveTo` bigint(30) NOT NULL,
  `riskRatio` decimal(10,0) NOT NULL,
  `yearlyInterestRate` decimal(10,0) NOT NULL,
  `commissionCharged` decimal(10,0) NOT NULL,
  `riskRatioDiffCurrCP` decimal(10,0) NOT NULL,
  `riskRatioMonthlyIncreaseDiffCurrCN` decimal(10,0) NOT NULL,
  `riskRatioSameCurrCN` decimal(10,0) NOT NULL,
  `riskRatioIncreaseDiffCurrCN` float DEFAULT NULL,
  `riskRatioDays` float DEFAULT NULL,
  `defaultIntermed` smallint(5) NOT NULL,
  `defaultPaymentTerm` int(10) NOT NULL,
  `defaultAcceptableMargin` float NOT NULL,
  `defaultCurrency` int(3) NOT NULL,
  `defaultIncoterms` int(5) NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`apid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_requests` (
  `aorid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `affid` int(10) NOT NULL,
  `orderType` int(10) NOT NULL,
  `orderReference` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `inspectionType` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `currency` int(10) NOT NULL,
  `exchangeRateToUSD` float NOT NULL,
  `referenceNumber` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `avgLocalInvoiceDueDate` bigint(30) NOT NULL,
  `revision` int(2) NOT NULL DEFAULT '0',
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`aorid`),
  UNIQUE KEY `aoiid` (`aorid`),
  FULLTEXT KEY `orderReference` (`orderReference`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_requests_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_requests_approvals` (
  `araid` int(10) NOT NULL AUTO_INCREMENT,
  `arid` int(10) NOT NULL,
  `aorid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `isApproved` tinyint(1) NOT NULL,
  `timeApproved` bigint(30) NOT NULL,
  `sequence` tinyint(1) NOT NULL,
  PRIMARY KEY (`araid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_requests_curstksupervision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_requests_curstksupervision` (
  `arcssid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `aorid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `packing` int(10) NOT NULL,
  `quantity` float NOT NULL,
  `stockValue` float NOT NULL,
  `stockEntryDate` bigint(30) NOT NULL,
  `dateOfStockEntry` bigint(30) NOT NULL,
  `expiryDate` bigint(30) NOT NULL,
  `estDateOfSale` bigint(30) NOT NULL,
  PRIMARY KEY (`arcssid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_requests_fundsengaged`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_requests_fundsengaged` (
  `arfeid` int(10) NOT NULL AUTO_INCREMENT,
  `aorid` int(10) NOT NULL,
  `orderShpInvOverdue` double NOT NULL,
  `orderShpInvNotDue` double NOT NULL,
  `ordersAppAwaitingShp` double NOT NULL,
  `odersWaitingApproval` double NOT NULL,
  `totalFunds` float NOT NULL,
  PRIMARY KEY (`arfeid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_requests_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_requests_lines` (
  `arlid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `aorid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `psid` int(10) NOT NULL,
  `packing` int(10) NOT NULL,
  `quantity` float NOT NULL,
  `uom` int(10) NOT NULL,
  `daysInStock` int(11) NOT NULL,
  `qtyPotentiallySold` float NOT NULL,
  `qtyPotentiallySoldPerc` float NOT NULL,
  `intialPrice` float NOT NULL,
  `affBuyingPrice` float NOT NULL,
  `totalBuyingValue` float NOT NULL,
  `costPrice` float NOT NULL,
  `costPriceAtRiskRatio` float NOT NULL,
  `sellingPrice` float NOT NULL,
  `grossMarginAtRiskRatio` float NOT NULL,
  `netMargin` float NOT NULL,
  `netMarginPerc` float NOT NULL,
  `fees` float NOT NULL,
  PRIMARY KEY (`arlid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_requests_linessupervision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_requests_linessupervision` (
  `arlsid` int(11) NOT NULL,
  `inputChecksum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `pid` int(10) NOT NULL,
  `aorid` int(10) NOT NULL,
  `packing` int(10) NOT NULL,
  `totalValue` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `estTimeOfArrival` bigint(30) NOT NULL,
  `estDateOfStockEntry` bigint(30) NOT NULL,
  `shelfLife` float NOT NULL,
  `estDateOfSale` bigint(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_requests_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_requests_messages` (
  `armid` int(10) NOT NULL AUTO_INCREMENT,
  `arid` int(10) NOT NULL,
  `aorid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `msgId` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `inReplyTo` int(10) NOT NULL,
  `inReplyToMsgId` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `viewPermission` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`armid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_requests_partiesinformation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_requests_partiesinformation` (
  `apiid` int(10) NOT NULL AUTO_INCREMENT,
  `aorid` int(10) NOT NULL,
  `intermedAff` smallint(5) NOT NULL,
  `intermedIncoterms` int(10) NOT NULL,
  `intermedIncotermsDesc` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `intermedPaymentTerm` int(10) NOT NULL,
  `intermedPaymentTermDesc` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `ptAcceptableMargin` int(10) NOT NULL,
  `promiseOfPayment` bigint(30) NOT NULL,
  `intermedEstDateOfPayment` bigint(30) NOT NULL,
  `commission` int(10) NOT NULL,
  `totalDiscount` int(10) NOT NULL,
  `vendorEid` int(10) NOT NULL,
  `vendorIsAff` tinyint(1) NOT NULL,
  `vendorAff` smallint(5) NOT NULL,
  `vendorIncoterms` int(10) NOT NULL,
  `vendorIncotermsDesc` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `vendorPaymentTerm` int(10) NOT NULL,
  `vendorPaymentTermDesc` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `vendorEstDateOfPayment` bigint(30) NOT NULL,
  `estDateOfShipment` bigint(30) NOT NULL,
  `transitTime` int(10) NOT NULL,
  `clearanceTime` int(10) NOT NULL,
  `shipmentCountry` int(10) NOT NULL,
  `originCountry` int(10) NOT NULL,
  `freight` float NOT NULL,
  `bankFees` float NOT NULL,
  `insurance` float NOT NULL,
  `legalization` float NOT NULL,
  `courier` float NOT NULL,
  `otherFees` float NOT NULL,
  PRIMARY KEY (`apiid`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aro_wareshouses_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aro_wareshouses_policies` (
  `awpid` int(10) NOT NULL AUTO_INCREMENT,
  `warehouse` int(10) NOT NULL,
  `effectiveFrom` bigint(30) NOT NULL,
  `effectiveTo` bigint(30) NOT NULL,
  `rate` float NOT NULL,
  `currency` int(10) NOT NULL,
  `rate_uom` float NOT NULL,
  `datePeriod` float NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`awpid`),
  UNIQUE KEY `awpid` (`awpid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `asid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `tag` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(5) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `editedOn` bigint(30) NOT NULL,
  `editedBy` int(10) NOT NULL,
  PRIMARY KEY (`asid`,`affid`,`type`),
  KEY `affid` (`affid`),
  KEY `editedBy` (`editedBy`),
  KEY `createdBy` (`createdBy`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assets_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets_types` (
  `astid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`astid`),
  UNIQUE KEY `astid` (`astid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assets_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets_users` (
  `auid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `asid` int(10) NOT NULL,
  `fromDate` bigint(30) NOT NULL,
  `toDate` bigint(30) NOT NULL,
  `conditionOnHandover` text COLLATE utf8_unicode_ci NOT NULL,
  `conditionOnReturn` text COLLATE utf8_unicode_ci NOT NULL,
  `assignedBy` int(10) NOT NULL,
  `assignedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`auid`,`uid`,`asid`),
  KEY `uid` (`uid`,`asid`),
  KEY `assignedBy` (`assignedBy`),
  KEY `assignedBy_2` (`assignedBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assignedemployees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignedemployees` (
  `aseid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  `isValidator` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aseid`,`eid`,`uid`,`affid`),
  KEY `eid` (`eid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=65036 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `aid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `timeIn` bigint(30) NOT NULL DEFAULT '0',
  `timeOut` bigint(30) NOT NULL DEFAULT '0',
  `date` bigint(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=39732 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance_additionalleaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_additionalleaves` (
  `adid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(10) NOT NULL,
  `numDays` float NOT NULL,
  `date` bigint(30) NOT NULL,
  `correspondToDate` tinyint(1) NOT NULL DEFAULT '1',
  `remark` text COLLATE utf8_unicode_ci NOT NULL,
  `addedBy` int(10) NOT NULL,
  `isApproved` tinyint(1) NOT NULL DEFAULT '0',
  `isCounted` tinyint(1) NOT NULL DEFAULT '0',
  `approvedOn` bigint(30) DEFAULT NULL,
  `requestedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`adid`),
  KEY `uid` (`uid`),
  KEY `addedBy` (`addedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=299 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance_attrecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_attrecords` (
  `aarid` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `time` bigint(30) NOT NULL,
  `operation` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `lastupdateTime` bigint(30) NOT NULL,
  `lastupdateOperation` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`aarid`)
) ENGINE=MyISAM AUTO_INCREMENT=77345 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance_leaveexptypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_leaveexptypes` (
  `aletid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `isAirFare` tinyint(1) NOT NULL DEFAULT '0',
  `isAccommodation` tinyint(1) NOT NULL DEFAULT '0',
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  PRIMARY KEY (`aletid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance_leaves_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_leaves_expenses` (
  `aleid` int(10) NOT NULL AUTO_INCREMENT,
  `alteid` mediumint(10) NOT NULL,
  `lid` int(10) NOT NULL,
  `expectedAmt` float NOT NULL,
  `actualAmt` float NOT NULL,
  `currency` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `usdFxrate` float DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`aleid`),
  KEY `alteid` (`alteid`,`lid`)
) ENGINE=MyISAM AUTO_INCREMENT=1058 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance_leavetypes_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_leavetypes_expenses` (
  `alteid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `ltid` smallint(5) NOT NULL,
  `aletid` smallint(10) NOT NULL,
  `titleOverwrite` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isRequired` tinyint(1) NOT NULL DEFAULT '1',
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  `hasComments` tinyint(1) NOT NULL DEFAULT '0',
  `requireComments` tinyint(1) NOT NULL DEFAULT '0',
  `commentsTitleLangVar` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `commentsTitle` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`alteid`),
  KEY `ltid` (`ltid`),
  KEY `aletid` (`aletid`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banks` (
  `bnkid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `affid` int(10) NOT NULL,
  PRIMARY KEY (`bnkid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `basic_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `basic_ingredients` (
  `biid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`biid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_accountstrees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_accountstrees` (
  `batid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `accountType` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `accountLevel` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `sequence` int(8) NOT NULL,
  `sourceTable` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sourceAttr` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `accountSign` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `ophrand` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`batid`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_bankfacilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_bankfacilities` (
  `bbfid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `bnkid` int(10) NOT NULL,
  `bfbid` int(10) NOT NULL,
  `hasFacilities` tinyint(1) NOT NULL,
  `overDraft` float NOT NULL,
  `loan` float NOT NULL,
  `forexForward` float NOT NULL,
  `billsDiscount` float NOT NULL,
  `othersGuarantees` float NOT NULL,
  `facilityCurrency` int(10) NOT NULL,
  `interestRate` float NOT NULL,
  `premiumCommission` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `totalAmount` float NOT NULL,
  `endquarterAmount` float NOT NULL,
  `comfortLetter` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `LastIssuanceDate` bigint(30) NOT NULL,
  `LastRenewalDate` bigint(30) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`bbfid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_budgets` (
  `bid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `year` int(4) unsigned NOT NULL,
  `description` text NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  `spid` int(10) unsigned NOT NULL,
  `isLocked` smallint(1) DEFAULT '0',
  `isFinalized` tinyint(1) DEFAULT '0',
  `finalizedBy` int(10) NOT NULL,
  `lockedBy` tinyint(1) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bid`),
  KEY `affid` (`affid`,`spid`)
) ENGINE=MyISAM AUTO_INCREMENT=1515 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_budgets2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_budgets2` (
  `bid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `year` int(4) unsigned NOT NULL,
  `description` text NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  `spid` int(10) unsigned NOT NULL,
  `isLocked` smallint(1) DEFAULT '0',
  `isFinalized` tinyint(1) DEFAULT '0',
  `finalizedBy` int(10) NOT NULL,
  `lockedBy` tinyint(1) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bid`),
  KEY `affid` (`affid`,`spid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_budgets_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_budgets_lines` (
  `blid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(100) NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `altPid` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `psid` smallint(5) DEFAULT NULL,
  `bid` int(10) unsigned NOT NULL,
  `cid` int(10) NOT NULL,
  `altCid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `prevblid` int(10) DEFAULT NULL,
  `customerCountry` int(10) NOT NULL DEFAULT '0',
  `businessMgr` int(10) NOT NULL,
  `actualQty` float NOT NULL,
  `actualIncome` float NOT NULL,
  `actualAmount` float NOT NULL,
  `amount` float NOT NULL,
  `unitPrice` float NOT NULL,
  `income` float NOT NULL,
  `incomePerc` float NOT NULL,
  `localIncomePercentage` float DEFAULT NULL,
  `localIncomeAmount` float DEFAULT NULL,
  `invoicingEntityIncome` float NOT NULL DEFAULT '0',
  `invoice` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `invoiceAffid` smallint(5) DEFAULT NULL,
  `commissionSplitAffid` smallint(5) NOT NULL DEFAULT '0',
  `purchasingEntity` varchar(50) NOT NULL,
  `purchasingEntityId` int(10) NOT NULL,
  `quantity` float NOT NULL,
  `createdBy` int(10) NOT NULL DEFAULT '0',
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL DEFAULT '0',
  `originalCurrency` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `saleType` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `s1Perc` float NOT NULL,
  `s2Perc` float NOT NULL,
  `interCompanyPurchase` smallint(5) DEFAULT NULL,
  `linkedBudgetLine` int(10) DEFAULT NULL,
  PRIMARY KEY (`blid`),
  KEY `createdBy` (`createdBy`),
  KEY `businessMgr` (`businessMgr`)
) ENGINE=MyISAM AUTO_INCREMENT=18067 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_budgets_lines2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_budgets_lines2` (
  `blid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(100) NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `altPid` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `psid` smallint(5) DEFAULT NULL,
  `bid` int(10) unsigned NOT NULL,
  `cid` int(10) NOT NULL,
  `altCid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `prevblid` int(10) DEFAULT NULL,
  `customerCountry` int(10) NOT NULL DEFAULT '0',
  `businessMgr` int(10) NOT NULL,
  `actualQty` float NOT NULL,
  `actualIncome` float NOT NULL,
  `actualAmount` float NOT NULL,
  `amount` float NOT NULL,
  `unitPrice` float NOT NULL,
  `income` float NOT NULL,
  `incomePerc` float NOT NULL,
  `localIncomePercentage` float DEFAULT NULL,
  `localIncomeAmount` float DEFAULT NULL,
  `invoicingEntityIncome` float NOT NULL DEFAULT '0',
  `invoice` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `invoiceAffid` smallint(5) DEFAULT NULL,
  `commissionSplitAffid` smallint(5) NOT NULL DEFAULT '0',
  `purchasingEntity` varchar(50) NOT NULL,
  `purchasingEntityId` int(10) NOT NULL,
  `quantity` float NOT NULL,
  `createdBy` int(10) NOT NULL DEFAULT '0',
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL DEFAULT '0',
  `originalCurrency` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `saleType` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `s1Perc` float NOT NULL,
  `s2Perc` float NOT NULL,
  `interCompanyPurchase` smallint(5) DEFAULT NULL,
  `linkedBudgetLine` int(10) DEFAULT NULL,
  PRIMARY KEY (`blid`),
  UNIQUE KEY `inputChecksum` (`inputChecksum`),
  KEY `createdBy` (`createdBy`),
  KEY `businessMgr` (`businessMgr`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_budgets_lines_local`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_budgets_lines_local` (
  `blid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `altPid` varchar(200) DEFAULT NULL,
  `psid` smallint(5) DEFAULT NULL,
  `bid` int(10) unsigned NOT NULL,
  `cid` int(10) NOT NULL,
  `altCid` varchar(50) NOT NULL,
  `prevblid` int(10) DEFAULT NULL,
  `customerCountry` int(10) NOT NULL DEFAULT '0',
  `businessMgr` int(10) NOT NULL,
  `actualQty` float NOT NULL,
  `actualIncome` float NOT NULL,
  `actualAmount` float NOT NULL,
  `amount` float NOT NULL,
  `unitPrice` float NOT NULL,
  `income` float NOT NULL,
  `incomePerc` float NOT NULL,
  `invoice` varchar(10) NOT NULL,
  `quantity` float NOT NULL,
  `createdBy` int(10) NOT NULL DEFAULT '0',
  `modifiedBy` int(10) NOT NULL,
  `originalCurrency` varchar(4) DEFAULT NULL,
  `saleType` varchar(12) NOT NULL,
  `s1Perc` float NOT NULL,
  `s2Perc` float NOT NULL,
  PRIMARY KEY (`blid`),
  KEY `createdBy` (`createdBy`),
  KEY `businessMgr` (`businessMgr`)
) ENGINE=MyISAM AUTO_INCREMENT=2465 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_budgets_local`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_budgets_local` (
  `bid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `year` int(4) unsigned NOT NULL,
  `description` text NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  `spid` int(10) unsigned NOT NULL,
  `isLocked` smallint(1) DEFAULT '0',
  `isFinalized` tinyint(1) DEFAULT '0',
  `finalizedBy` int(10) NOT NULL,
  `lockedBy` tinyint(1) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bid`),
  KEY `affid` (`affid`,`spid`)
) ENGINE=MyISAM AUTO_INCREMENT=276 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_commadminexps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_commadminexps` (
  `bcaeid` int(10) NOT NULL AUTO_INCREMENT,
  `bfbid` int(10) NOT NULL,
  `beciid` int(10) NOT NULL,
  `actualPrevThreeYears` float NOT NULL DEFAULT '0',
  `actualPrevTwoYears` float NOT NULL,
  `budgetPrevYear` float NOT NULL,
  `yefPrevYear` float NOT NULL,
  `budgetCurrent` float NOT NULL,
  `budYefPerc` float NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bcaeid`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_expense_categories` (
  `becid` smallint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`becid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_expense_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_expense_items` (
  `beciid` int(10) NOT NULL AUTO_INCREMENT,
  `becid` smallint(10) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  PRIMARY KEY (`beciid`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_financialbudget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_financialbudget` (
  `bfbid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `year` int(10) NOT NULL,
  `currency` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `finGenAdmExpAmtApthy` float NOT NULL DEFAULT '0',
  `finGenAdmExpAmtApty` float NOT NULL,
  `finGenAdmExpAmtBpy` float NOT NULL,
  `finGenAdmExpAmtYpy` float NOT NULL,
  `finGenAdmExpAmtCurrent` float NOT NULL,
  `netIncome` float NOT NULL DEFAULT '0',
  `isFinalized` tinyint(1) NOT NULL,
  `finalizedBy` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bfbid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_forecastbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_forecastbs` (
  `bfbsid` int(10) NOT NULL AUTO_INCREMENT,
  `bfbid` int(10) NOT NULL,
  `batid` int(10) NOT NULL,
  `amount` float NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`bfbsid`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_fxrates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_fxrates` (
  `bfxid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `year` smallint(5) NOT NULL,
  `fromCurrency` int(3) NOT NULL,
  `toCurrency` int(3) NOT NULL,
  `rate` float NOT NULL,
  `isActual` tinyint(1) NOT NULL DEFAULT '0',
  `isYef` tinyint(1) NOT NULL DEFAULT '0',
  `isBudget` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bfxid`)
) ENGINE=MyISAM AUTO_INCREMENT=226 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_headcount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_headcount` (
  `bhcid` int(10) NOT NULL AUTO_INCREMENT,
  `bfbid` int(10) NOT NULL,
  `posgid` smallint(5) NOT NULL,
  `actualPrevThreeYears` mediumint(10) NOT NULL DEFAULT '0',
  `actualPrevTwoYears` mediumint(10) NOT NULL,
  `budgetPrevYear` mediumint(10) NOT NULL,
  `yefPrevYear` mediumint(10) NOT NULL,
  `budgetCurrent` mediumint(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bhcid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_investcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_investcategory` (
  `bicid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bicid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_investexpenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_investexpenses` (
  `biid` int(10) NOT NULL AUTO_INCREMENT,
  `bfbid` int(10) NOT NULL,
  `biiid` mediumint(10) NOT NULL,
  `actualPrevThreeYears` float NOT NULL,
  `actualPrevTwoYears` float NOT NULL,
  `actualPrevYear` float NOT NULL,
  `budgetPrevYear` float NOT NULL,
  `yefPrevYear` float NOT NULL,
  `percVariation` float NOT NULL,
  `budgetCurrent` float NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`biid`),
  UNIQUE KEY `biid` (`biid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_investitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_investitems` (
  `biiid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `bicid` mediumint(10) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`biiid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_overduereceivables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_overduereceivables` (
  `boid` int(10) NOT NULL AUTO_INCREMENT,
  `bfbid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `legalAction` text COLLATE utf8_unicode_ci NOT NULL,
  `oldestUnpaidInvoiceDate` bigint(30) NOT NULL,
  `totalAmount` float NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `action` text COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `inputChecksum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`boid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_plcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_plcategory` (
  `bplcid` int(10) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`bplcid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_plexpenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_plexpenses` (
  `bpleid` int(10) NOT NULL AUTO_INCREMENT,
  `bpliid` int(10) NOT NULL,
  `bfbid` int(10) NOT NULL,
  `actualPrevThreeYears` float NOT NULL DEFAULT '0',
  `actualPrevTwoYears` float NOT NULL,
  `budgetPrevYear` float NOT NULL,
  `yefPrevYear` float NOT NULL,
  `budgetCurrent` float NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bpleid`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_plitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_plitems` (
  `bpliid` int(10) NOT NULL AUTO_INCREMENT,
  `bplcid` int(10) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bpliid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_trainingvisits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_trainingvisits` (
  `btvid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `bfbid` int(10) NOT NULL,
  `lid` int(10) DEFAULT NULL,
  `company` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date` bigint(30) NOT NULL,
  `purpose` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Costaffiliate` float NOT NULL,
  `event` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `bm` int(11) NOT NULL,
  `planCost` float NOT NULL,
  `otherCosts` float NOT NULL,
  `TotalCostAffiliate` float NOT NULL,
  `classification` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`btvid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_yearendforecast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_yearendforecast` (
  `yefid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `year` int(4) NOT NULL,
  `affid` smallint(5) NOT NULL,
  `spid` int(10) NOT NULL,
  `isLocked` tinyint(1) NOT NULL,
  `isFinalized` tinyint(1) NOT NULL,
  `finalizedBy` int(10) NOT NULL,
  `lockedBy` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `createdOn` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`yefid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_yef_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_yef_lines` (
  `yeflid` int(10) NOT NULL AUTO_INCREMENT,
  `inputCheckSum` varchar(200) NOT NULL,
  `yefid` int(10) NOT NULL,
  `blid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `altCid` varchar(50) NOT NULL,
  `prevyeflid` int(10) NOT NULL DEFAULT '0',
  `customerCountry` int(10) NOT NULL,
  `businessMgr` int(10) NOT NULL,
  `actualQty` float NOT NULL,
  `actualIncome` float NOT NULL,
  `actualAmount` float NOT NULL,
  `localIncomePercentage` float NOT NULL,
  `localIncomeAmount` float NOT NULL,
  `amount` float NOT NULL,
  `unitPrice` float NOT NULL,
  `income` float NOT NULL,
  `incomePerc` float NOT NULL,
  `invoice` varchar(10) NOT NULL,
  `invoiceAffid` int(10) NOT NULL,
  `invoicingEntityIncome` float NOT NULL,
  `interCompanyPurchase` int(10) NOT NULL,
  `quantity` float NOT NULL,
  `originalCurrency` int(11) NOT NULL,
  `saleType` varchar(12) NOT NULL,
  `october` float NOT NULL,
  `november` float NOT NULL,
  `december` float NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `commissionSplitAffid` int(11) NOT NULL,
  `purchasingEntity` varchar(50) NOT NULL,
  `purchasingEntityId` int(10) NOT NULL,
  `linkedBudgetLine` int(10) NOT NULL,
  `psid` int(5) NOT NULL,
  `fromBudget` tinyint(1) NOT NULL,
  `octoberqty` int(11) NOT NULL,
  `novemberqty` int(11) NOT NULL,
  `decemberqty` int(11) NOT NULL,
  PRIMARY KEY (`yeflid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_events` (
  `ceid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) NOT NULL,
  `alias` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fromDate` bigint(30) NOT NULL,
  `toDate` bigint(30) NOT NULL,
  `place` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `boothNum` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `type` smallint(1) NOT NULL,
  `logo` varchar(220) DEFAULT NULL,
  `refreshLogoOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `affid` smallint(5) DEFAULT NULL,
  `spid` int(10) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `isPublic` tinyint(1) NOT NULL,
  `isFeatured` tinyint(1) NOT NULL DEFAULT '0',
  `isCreatedFromCMS` tinyint(1) NOT NULL DEFAULT '0',
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `editedBy` int(10) DEFAULT NULL,
  `editedOn` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`ceid`),
  KEY `uid` (`uid`),
  KEY `createdBy` (`createdBy`,`editedBy`),
  FULLTEXT KEY `title_2` (`title`,`description`,`tags`),
  FULLTEXT KEY `tags` (`tags`)
) ENGINE=MyISAM AUTO_INCREMENT=266 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_events_invitees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_events_invitees` (
  `ceiid` int(10) NOT NULL AUTO_INCREMENT,
  `ceid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  PRIMARY KEY (`ceiid`),
  KEY `ceid` (`ceid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_events_restrictions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_events_restrictions` (
  `cerid` int(10) NOT NULL AUTO_INCREMENT,
  `ceid` int(10) NOT NULL,
  `affid` int(10) NOT NULL,
  PRIMARY KEY (`cerid`,`ceid`,`affid`),
  KEY `ceid` (`ceid`,`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_eventtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_eventtypes` (
  `cetid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`cetid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_tasks` (
  `ctid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `subject` varchar(220) NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '2',
  `dueDate` bigint(30) NOT NULL,
  `isDone` tinyint(1) NOT NULL DEFAULT '0',
  `timeDone` bigint(30) DEFAULT NULL,
  `description` text,
  `percCompleted` tinyint(3) NOT NULL,
  `reminderStart` bigint(30) DEFAULT NULL,
  `reminderInterval` int(10) DEFAULT NULL,
  `createdBy` int(10) NOT NULL,
  `pimAppId` varchar(255) DEFAULT NULL,
  `identifier` varchar(10) NOT NULL,
  PRIMARY KEY (`ctid`),
  KEY `uid` (`uid`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=994 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_tasks_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_tasks_notes` (
  `ctnid` int(10) NOT NULL AUTO_INCREMENT,
  `ctid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `note` text NOT NULL,
  `dateAdded` bigint(20) NOT NULL,
  PRIMARY KEY (`ctnid`),
  KEY `uid` (`uid`),
  KEY `ctid` (`ctid`)
) ENGINE=MyISAM AUTO_INCREMENT=1738 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_tasks_shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_tasks_shares` (
  `ctsid` int(10) NOT NULL AUTO_INCREMENT,
  `ctid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `description` int(11) NOT NULL,
  PRIMARY KEY (`ctsid`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_userpreferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_userpreferences` (
  `cpid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `excludeHolidays` int(1) NOT NULL DEFAULT '0',
  `excludeEvents` int(1) NOT NULL DEFAULT '0',
  `excludeLeaves` int(1) NOT NULL DEFAULT '0',
  `defaultView` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cpid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_userpreferences_excludedaffiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_userpreferences_excludedaffiliates` (
  `cpeaid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` int(10) NOT NULL,
  `cpid` int(10) NOT NULL,
  PRIMARY KEY (`cpeaid`,`affid`,`cpid`),
  KEY `uid` (`affid`,`cpid`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_userpreferences_excludedusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_userpreferences_excludedusers` (
  `cpeuid` int(10) NOT NULL AUTO_INCREMENT,
  `euid` int(10) NOT NULL,
  `cpid` int(10) NOT NULL,
  PRIMARY KEY (`cpeuid`,`euid`,`cpid`),
  KEY `uid` (`euid`,`cpid`)
) ENGINE=MyISAM AUTO_INCREMENT=1002 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chemfunctionchemcials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chemfunctionchemcials` (
  `cfcid` int(10) NOT NULL AUTO_INCREMENT,
  `safid` int(10) NOT NULL,
  `csid` int(10) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`cfcid`,`safid`,`csid`)
) ENGINE=MyISAM AUTO_INCREMENT=34224 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chemfunctionproducts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chemfunctionproducts` (
  `cfpid` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `safid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`cfpid`,`pid`,`safid`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chemicalfunctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chemicalfunctions` (
  `cfid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`cfid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`),
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM AUTO_INCREMENT=2051 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chemicalsubstances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chemicalsubstances` (
  `csid` int(10) NOT NULL AUTO_INCREMENT,
  `casNum` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `synonyms` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`csid`),
  FULLTEXT KEY `casNum` (`casNum`,`name`,`synonyms`),
  FULLTEXT KEY `name` (`name`,`synonyms`)
) ENGINE=MyISAM AUTO_INCREMENT=34472 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chemicalsubstances_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chemicalsubstances_import` (
  `casNum` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `cas` (`casNum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `ciid` int(10) NOT NULL AUTO_INCREMENT,
  `coid` smallint(5) NOT NULL,
  `country` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `unlocode` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `oudeloc` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `geoLocationText` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `defaultAirport` int(10) DEFAULT NULL,
  PRIMARY KEY (`ciid`),
  KEY `coid` (`coid`),
  KEY `defaultAirport` (`defaultAirport`)
) ENGINE=MyISAM AUTO_INCREMENT=87762 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms` (
  `cmssid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `optionscode` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`cmssid`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_contentcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_contentcategories` (
  `cmsccid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cmsccid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_menuitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_menuitems` (
  `cmsmiid` int(10) NOT NULL AUTO_INCREMENT,
  `cmsmid` tinyint(10) NOT NULL,
  `parent` int(10) NOT NULL,
  `sequence` smallint(3) NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `itemclasses` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isPublished` tinyint(1) NOT NULL,
  `datePublished` bigint(30) NOT NULL,
  `publishedDesc` text COLLATE utf8_unicode_ci,
  `lang` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `configurations` text COLLATE utf8_unicode_ci NOT NULL,
  `metaDesc` text COLLATE utf8_unicode_ci NOT NULL,
  `metaKeywords` text COLLATE utf8_unicode_ci NOT NULL,
  `robotsRule` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  PRIMARY KEY (`cmsmiid`),
  KEY `cmsmid` (`cmsmid`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_menus` (
  `cmsmid` tinyint(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `submenuclasses` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  PRIMARY KEY (`cmsmid`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_news` (
  `cmsnid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `version` float NOT NULL DEFAULT '1',
  `summary` text COLLATE utf8_unicode_ci NOT NULL,
  `publishDate` bigint(30) NOT NULL,
  `isPublished` tinyint(1) NOT NULL DEFAULT '0',
  `isFeatured` tinyint(1) NOT NULL DEFAULT '0',
  `lang` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `bodyText` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `metaDesc` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `metaKeywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `robotsRule` varchar(100) CHARACTER SET ucs2 COLLATE ucs2_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createDate` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifyDate` bigint(30) NOT NULL,
  `hits` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uploadedImages` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`cmsnid`),
  KEY `createDate` (`createDate`,`modifyDate`),
  FULLTEXT KEY `tags` (`tags`),
  FULLTEXT KEY `summary` (`summary`),
  FULLTEXT KEY `bodyText` (`bodyText`),
  FULLTEXT KEY `summary_2` (`summary`,`bodyText`,`tags`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_news_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_news_attachments` (
  `cmsnaid` int(10) NOT NULL AUTO_INCREMENT,
  `cmsnid` int(10) NOT NULL,
  `title` varchar(220) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `type` varchar(5) NOT NULL,
  `size` float NOT NULL,
  `dateAdded` bigint(30) NOT NULL,
  `addedBy` int(10) NOT NULL,
  PRIMARY KEY (`cmsnaid`),
  KEY `addedBy` (`addedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_news_relatedcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_news_relatedcategories` (
  `cmsnrcid` int(10) NOT NULL AUTO_INCREMENT,
  `cmsnid` int(10) NOT NULL,
  `cmsccid` smallint(5) NOT NULL,
  PRIMARY KEY (`cmsnrcid`,`cmsnid`,`cmsccid`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_pagecategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_pagecategories` (
  `cmspcid` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cmspcid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_pages` (
  `cmspid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `version` float NOT NULL,
  `publishDate` bigint(30) NOT NULL,
  `isPublished` tinyint(1) NOT NULL,
  `category` tinyint(2) unsigned NOT NULL,
  `lang` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `bodyText` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pageHeaderBkg` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `metaDesc` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `metaKeywords` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `robotsRule` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  `hits` mediumint(8) unsigned NOT NULL,
  `uploadedImages` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tags` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cmspid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`),
  KEY `category` (`category`),
  FULLTEXT KEY `bodyText` (`bodyText`,`tags`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_pages2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_pages2` (
  `cmspid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `alias` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `tags` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `version` float NOT NULL,
  `publishDate` bigint(30) NOT NULL,
  `isPublished` tinyint(1) NOT NULL,
  `category` tinyint(2) unsigned NOT NULL,
  `lang` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `bodyText` text COLLATE utf8_unicode_ci NOT NULL,
  `metaDesc` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `metaKeywords` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `robotsRule` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  `hits` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`cmspid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`),
  FULLTEXT KEY `tags` (`tags`,`bodyText`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cms_pageshighlights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_pageshighlights` (
  `cmsphid` int(10) NOT NULL AUTO_INCREMENT,
  `cmspid` int(10) NOT NULL,
  `cmshid` int(5) NOT NULL,
  PRIMARY KEY (`cmsphid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contactform_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactform_messages` (
  `cfmid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `firstName` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `coid` int(10) NOT NULL,
  `position` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `senderCategory` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `contactedOn` bigint(30) NOT NULL,
  `purpose` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cfmid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `coid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) unsigned NOT NULL,
  `name` varchar(220) NOT NULL,
  `altName` varchar(200) DEFAULT NULL,
  `acronym` varchar(10) NOT NULL,
  `capitalCity` int(10) NOT NULL,
  `mainCurrency` int(3) DEFAULT NULL,
  `defaultTimeZone` varchar(100) DEFAULT NULL,
  `phoneCode` mediumint(5) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `continent` varchar(50) DEFAULT NULL,
  `chartSpec` mediumtext,
  PRIMARY KEY (`coid`),
  KEY `affid` (`affid`),
  KEY `capitalCity` (`capitalCity`)
) ENGINE=MyISAM AUTO_INCREMENT=244 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies` (
  `numCode` int(3) NOT NULL,
  `alphaCode` varchar(4) NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`numCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `currencies_fxrates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies_fxrates` (
  `cfxid` int(10) NOT NULL AUTO_INCREMENT,
  `baseCurrency` int(3) unsigned NOT NULL,
  `currency` int(3) unsigned NOT NULL,
  `rate` float NOT NULL,
  `date` bigint(30) NOT NULL,
  PRIMARY KEY (`cfxid`),
  KEY `baseCurrency` (`baseCurrency`,`currency`)
) ENGINE=MyISAM AUTO_INCREMENT=86894 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `development_bugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `development_bugs` (
  `dbid` int(10) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `summary` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `file` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `line` smallint(10) unsigned NOT NULL,
  `module` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `moduleFile` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `stackTrace` text COLLATE utf8_unicode_ci,
  `reportedOn` int(11) NOT NULL,
  `reportedBy` int(11) NOT NULL DEFAULT '0',
  `sessionUser` int(11) DEFAULT NULL,
  `severity` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `priority` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `isFixed` tinyint(1) NOT NULL DEFAULT '0',
  `assignedTo` int(10) NOT NULL,
  `affectedVersion` float NOT NULL,
  `fixedVersion` float NOT NULL,
  `relatedRequirement` int(10) NOT NULL,
  `assignedOn` int(11) NOT NULL,
  `commitHash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `commitMsg` text COLLATE utf8_unicode_ci NOT NULL,
  `modifiedOn` int(11) NOT NULL,
  `modifiedBy` int(11) NOT NULL,
  PRIMARY KEY (`dbid`),
  KEY `assignedTo` (`assignedTo`)
) ENGINE=MyISAM AUTO_INCREMENT=415 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `development_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `development_requirements` (
  `drid` int(10) NOT NULL AUTO_INCREMENT,
  `parent` int(10) NOT NULL DEFAULT '0',
  `refWord` varchar(5) NOT NULL,
  `refKey` smallint(3) NOT NULL,
  `module` varchar(50) NOT NULL,
  `title` varchar(300) NOT NULL,
  `description` text,
  `security` text,
  `userInterface` text,
  `performance` text,
  `isApproved` tinyint(1) NOT NULL,
  `isCompleted` tinyint(1) NOT NULL DEFAULT '0',
  `requestedBy` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL DEFAULT '0',
  `modifiedOn` bigint(30) NOT NULL DEFAULT '0',
  `assignedTo` int(10) DEFAULT '0',
  PRIMARY KEY (`drid`),
  KEY `parent` (`parent`,`requestedBy`,`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=191 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `development_requirements_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `development_requirements_changes` (
  `drcid` int(10) NOT NULL AUTO_INCREMENT,
  `refKey` smallint(3) NOT NULL,
  `drid` int(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  `reasonCategory` tinyint(2) NOT NULL,
  `description` text NOT NULL,
  `impact` text NOT NULL,
  `outcomeReq` int(10) DEFAULT NULL,
  `isCompleted` tinyint(1) NOT NULL DEFAULT '0',
  `requestedBy` int(10) NOT NULL,
  `dateRequested` bigint(30) NOT NULL,
  `approvedBy` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  PRIMARY KEY (`drcid`),
  KEY `identifier` (`outcomeReq`,`requestedBy`,`approvedBy`,`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `distributors_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distributors_import` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `companyName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `postCode` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `presence` varchar(14) COLLATE utf8_unicode_ci NOT NULL,
  `productsType` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `phone1` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fax1` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `segments` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=226 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `draws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `draws` (
  `drawnum` int(10) NOT NULL,
  `date` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `year` int(4) NOT NULL,
  `b1` int(11) NOT NULL,
  `b2` int(11) NOT NULL,
  `b3` int(11) NOT NULL,
  `b4` int(11) NOT NULL,
  `b5` int(11) NOT NULL,
  `b6` int(11) NOT NULL,
  `bonus` int(11) NOT NULL,
  UNIQUE KEY `drawnum` (`drawnum`,`year`),
  UNIQUE KEY `drawnum_2` (`drawnum`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employeeseducationcert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employeeseducationcert` (
  `ecid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `year` int(4) NOT NULL,
  `schoolName` varchar(200) NOT NULL,
  PRIMARY KEY (`ecid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=825 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employeesexperience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employeesexperience` (
  `eexpid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `fromMonth` int(2) NOT NULL DEFAULT '0',
  `fromYear` int(4) NOT NULL,
  `toMonth` int(2) NOT NULL DEFAULT '0',
  `toYear` int(4) NOT NULL,
  `company` varchar(200) NOT NULL,
  `position` varchar(200) NOT NULL,
  PRIMARY KEY (`eexpid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1196 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employeessegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employeessegments` (
  `emsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `psid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`emsid`,`uid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=7177 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employeesshifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employeesshifts` (
  `esid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `wsid` smallint(10) unsigned NOT NULL,
  `fromDate` bigint(30) NOT NULL DEFAULT '0',
  `toDate` bigint(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`esid`,`uid`,`wsid`,`fromDate`,`toDate`)
) ENGINE=MyISAM AUTO_INCREMENT=1622 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `endproducttypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `endproducttypes` (
  `eptid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `psaid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `parent` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eptid`),
  UNIQUE KEY `name_2` (`name`,`psaid`,`parent`),
  KEY `createdBy` (`createdBy`,`modifiedOn`),
  KEY `psaid` (`psaid`)
) ENGINE=MyISAM AUTO_INCREMENT=1280 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `endproducttypes_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `endproducttypes_old` (
  `eptid` int(10) NOT NULL AUTO_INCREMENT,
  `parent` int(10) NOT NULL DEFAULT '0',
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `psaid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`eptid`),
  KEY `createdBy` (`createdBy`,`modifiedOn`),
  KEY `psaid` (`psaid`)
) ENGINE=MyISAM AUTO_INCREMENT=330 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities` (
  `eid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `companyName` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `companyNameShort` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `companyNameAbbr` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` varchar(220) DEFAULT NULL,
  `parent` int(10) DEFAULT NULL,
  `presence` varchar(10) NOT NULL,
  `country` int(10) unsigned NOT NULL,
  `city` varchar(100) NOT NULL,
  `addressLine1` varchar(200) NOT NULL,
  `addressLine2` varchar(150) DEFAULT NULL,
  `building` varchar(100) NOT NULL,
  `floor` int(2) DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `postCode` int(6) DEFAULT NULL,
  `poBox` int(10) DEFAULT NULL,
  `mainEmail` varchar(220) DEFAULT NULL,
  `website` varchar(220) DEFAULT NULL,
  `phone1` varchar(20) NOT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `fax1` varchar(20) DEFAULT NULL,
  `fax2` varchar(20) DEFAULT NULL,
  `companySize` varchar(50) DEFAULT NULL,
  `approved` smallint(1) unsigned NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `dateAdded` bigint(30) unsigned NOT NULL,
  `notes` text,
  `type` char(2) NOT NULL,
  `supplierType` tinyint(1) DEFAULT NULL,
  `productsType` varchar(15) DEFAULT NULL,
  `contractFirstSigDate` bigint(30) DEFAULT NULL,
  `contractExpiryDate` bigint(30) DEFAULT NULL,
  `contractIsEvergreen` tinyint(1) DEFAULT NULL,
  `contractPriorNotice` smallint(2) DEFAULT NULL,
  `isCentralPurchase` tinyint(1) DEFAULT '0',
  `centralPurchaseNote` text,
  `mainSupplyLine` varchar(200) NOT NULL,
  `supplierSince` bigint(30) NOT NULL,
  `relationMaturity` tinyint(2) DEFAULT NULL,
  `trustLevel` int(3) DEFAULT NULL,
  `noQReportReq` tinyint(1) NOT NULL DEFAULT '0',
  `reqQRSummary` tinyint(1) NOT NULL DEFAULT '0',
  `noQReportSend` tinyint(1) NOT NULL DEFAULT '0',
  `customerSince` bigint(30) NOT NULL,
  `loyalty` int(2) DEFAULT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) DEFAULT NULL,
  `modifiedOn` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`eid`),
  KEY `createBy` (`createdBy`,`modifiedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=5910 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities_contractcountries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities_contractcountries` (
  `eccid` int(10) NOT NULL AUTO_INCREMENT,
  `eid` int(10) NOT NULL,
  `coid` int(10) NOT NULL,
  `isExclusive` tinyint(1) NOT NULL,
  `exclusivity` text COLLATE utf8_unicode_ci,
  `isAgent` tinyint(1) NOT NULL DEFAULT '0',
  `isDistributor` tinyint(1) NOT NULL DEFAULT '0',
  `selectiveProducts` tinyint(1) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`eccid`,`eid`,`coid`),
  UNIQUE KEY `eccid` (`eccid`),
  KEY `eccid_2` (`eccid`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities_locations` (
  `eloid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `eid` int(10) NOT NULL,
  `locationType` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `coid` int(10) NOT NULL,
  `ciid` int(10) NOT NULL,
  `addressLine1` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine2` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buildingName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `postcode` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `poBox` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `geoLocation` point NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `isMain` tinyint(1) NOT NULL,
  PRIMARY KEY (`eloid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities_ratingcriteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities_ratingcriteria` (
  `ercid` tinyint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `weight` tinyint(1) NOT NULL,
  PRIMARY KEY (`ercid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities_ratings` (
  `erid` int(10) NOT NULL AUTO_INCREMENT,
  `ercid` tinyint(10) NOT NULL,
  `eid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `rating` tinyint(2) NOT NULL,
  `dateTime` bigint(30) NOT NULL,
  PRIMARY KEY (`erid`),
  KEY `ercid` (`ercid`,`eid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=116 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities_rmlevels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities_rmlevels` (
  `ermlid` tinyint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` tinyint(2) NOT NULL,
  `category` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`ermlid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities_rmlevels_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities_rmlevels_categories` (
  `ercid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `titleAbbr` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ercid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entitiesbrands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitiesbrands` (
  `ebid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `eid` int(10) NOT NULL,
  `isGeneral` tinyint(1) NOT NULL DEFAULT '0',
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`ebid`),
  KEY `spid` (`eid`,`createdBy`,`modifiedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entitiesbrandsproducts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitiesbrandsproducts` (
  `ebpid` int(10) NOT NULL AUTO_INCREMENT,
  `ebid` int(10) NOT NULL,
  `eptid` int(10) NOT NULL,
  `pcvid` int(5) NOT NULL DEFAULT '0',
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`ebpid`,`ebid`,`eptid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entitiesrepresentatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitiesrepresentatives` (
  `erpid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rpid` int(10) unsigned NOT NULL,
  `eid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`rpid`,`erpid`,`eid`),
  UNIQUE KEY `eid` (`eid`,`rpid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entitiessegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitiessegments` (
  `esid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eid` int(10) unsigned NOT NULL,
  `psid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`esid`,`eid`,`psid`),
  UNIQUE KEY `eid_2` (`eid`,`psid`),
  KEY `eid` (`eid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=12083 DEFAULT CHARSET=latin1;
) ENGINE=MyISAM AUTO_INCREMENT=7042 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `facilitymgmt_facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facilitymgmt_facilities` (
  `fmfid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` int(10) NOT NULL,
  `parent` int(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `numOccupants` int(11) NOT NULL,
  `dimensions` varchar(10) NOT NULL,
  `allowReservation` tinyint(1) NOT NULL,
  `description` varchar(220) NOT NULL,
  `image` text NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  `idColor` varchar(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`fmfid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `facilitymgmt_facilityfeatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facilitymgmt_facilityfeatures` (
  `fmffid` int(10) NOT NULL AUTO_INCREMENT,
  `fmftid` int(10) NOT NULL,
  `fmfid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`fmffid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `facilitymgmt_factypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facilitymgmt_factypes` (
  `fmftid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(110) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `isRoom` tinyint(1) NOT NULL,
  `isCoWorkingSpace` tinyint(1) NOT NULL,
  `isMainLocation` tinyint(1) NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`fmftid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `facilitymgmt_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facilitymgmt_features` (
  `fmftid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(110) NOT NULL,
  `title` varchar(220) NOT NULL,
  `description` varchar(220) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`fmftid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `facilitymgmt_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facilitymgmt_reservations` (
  `fmrid` int(10) NOT NULL AUTO_INCREMENT,
  `fmfid` int(10) NOT NULL,
  `fromDate` int(30) NOT NULL,
  `toDate` int(30) NOT NULL,
  `reservedBy` int(10) NOT NULL,
  `purpose` text NOT NULL,
  `mtid` int(10) NOT NULL,
  PRIMARY KEY (`fmrid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `fid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) NOT NULL,
  `category` tinyint(1) unsigned NOT NULL,
  `ffid` int(10) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `reference` varchar(5) NOT NULL,
  `referenceId` int(10) NOT NULL,
  `isShared` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fid`),
  KEY `ffid` (`ffid`)
) ENGINE=MyISAM AUTO_INCREMENT=892 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `files_viewrestriction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files_viewrestriction` (
  `faid` int(20) NOT NULL AUTO_INCREMENT,
  `fid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`faid`,`fid`,`uid`),
  KEY `fid` (`fid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=44812 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `filescategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filescategories` (
  `fcid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(250) NOT NULL,
  `isPublic` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fcid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `filesfolder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filesfolder` (
  `ffid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text,
  `parent` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL,
  `noWritePermissionsLater` tinyint(1) NOT NULL,
  `noReadPermissionsLater` tinyint(1) NOT NULL,
  PRIMARY KEY (`ffid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `filesfolder_viewrestriction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filesfolder_viewrestriction` (
  `ffaid` int(20) NOT NULL AUTO_INCREMENT,
  `ffid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `noRead` int(1) NOT NULL DEFAULT '0',
  `noWrite` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ffaid`,`ffid`,`uid`),
  KEY `ffid` (`ffid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=12798 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fileversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fileversions` (
  `fvid` int(10) NOT NULL AUTO_INCREMENT,
  `fid` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(5) NOT NULL,
  `size` float NOT NULL,
  `timeLine` bigint(30) NOT NULL,
  `uid` int(10) NOT NULL,
  `changes` text,
  PRIMARY KEY (`fvid`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=892 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `genericproducts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `genericproducts` (
  `gpid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `psid` smallint(5) unsigned NOT NULL,
  `title` varchar(220) NOT NULL,
  `description` text,
  PRIMARY KEY (`gpid`),
  KEY `psid` (`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=304 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grouppurchase_forecast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grouppurchase_forecast` (
  `gpfid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `year` int(4) unsigned NOT NULL,
  `spid` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`gpfid`)
) ENGINE=MyISAM AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grouppurchase_forecastlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grouppurchase_forecastlines` (
  `gpflid` int(10) NOT NULL AUTO_INCREMENT,
  `gpfid` int(10) NOT NULL,
  `inputChecksum` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pid` int(10) NOT NULL,
  `psid` int(10) NOT NULL,
  `saleType` smallint(10) NOT NULL,
  `businessMgr` int(10) NOT NULL,
  `month1` float(10,2) NOT NULL,
  `month2` float(10,2) NOT NULL,
  `month3` float(10,2) NOT NULL,
  `month4` float(10,2) NOT NULL,
  `month5` float(10,2) NOT NULL,
  `month6` float(10,2) NOT NULL,
  `month7` float(10,2) NOT NULL,
  `month8` float(10,2) NOT NULL,
  `month9` float(10,2) NOT NULL,
  `month10` float(10,2) NOT NULL,
  `month11` float(10,2) NOT NULL,
  `month12` float(10,2) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`gpflid`)
) ENGINE=MyISAM AUTO_INCREMENT=226 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grouppurchase_pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grouppurchase_pricing` (
  `gppid` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `setTime` bigint(30) NOT NULL,
  `setBy` int(10) NOT NULL,
  `notes` text,
  PRIMARY KEY (`gppid`),
  KEY `pid` (`pid`,`setBy`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grouppurchase_pricingdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grouppurchase_pricingdetails` (
  `gppdid` int(10) NOT NULL AUTO_INCREMENT,
  `gppid` int(10) unsigned NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  `pricingMethod` tinyint(2) NOT NULL,
  `price` decimal(10,3) NOT NULL,
  `unit` tinyint(2) NOT NULL,
  `validThrough` bigint(30) NOT NULL,
  `remark` varchar(220) DEFAULT NULL,
  PRIMARY KEY (`gppdid`,`gppid`,`affid`,`pricingMethod`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `help_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_videos` (
  `hvid` int(10) NOT NULL AUTO_INCREMENT,
  `alias` varchar(110) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `link` text COLLATE utf8_unicode_ci NOT NULL,
  `embedLink` text COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`hvid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidays` (
  `hid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `name` varchar(220) NOT NULL,
  `title` varchar(220) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) DEFAULT '0',
  `numDays` float NOT NULL,
  `isOnce` smallint(1) NOT NULL,
  `validFrom` bigint(30) DEFAULT '0',
  `validTo` bigint(30) DEFAULT '0',
  PRIMARY KEY (`hid`),
  KEY `affid` (`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=458 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `holidaysexceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidaysexceptions` (
  `heid` int(10) NOT NULL AUTO_INCREMENT,
  `hid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`heid`,`hid`,`uid`),
  KEY `hid` (`hid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=332 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `incoterms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoterms` (
  `iid` smallint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titleAbbr` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`iid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `integration_mediation_entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_entities` (
  `imspid` int(20) NOT NULL AUTO_INCREMENT,
  `localId` int(10) NOT NULL,
  `foreignSystem` tinyint(1) NOT NULL DEFAULT '1',
  `foreignId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `foreignName` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `foreignNameAbbr` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `affid` int(10) NOT NULL,
  `entityType` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 's',
  `contactPersonName` varchar(200) CHARACTER SET latin1 NOT NULL,
  `country` int(10) NOT NULL,
  `city` varchar(100) CHARACTER SET latin1 NOT NULL,
  `addressLine1` varchar(220) CHARACTER SET latin1 NOT NULL,
  `addressLine2` varchar(200) CHARACTER SET latin1 NOT NULL,
  `building` varchar(100) CHARACTER SET latin1 NOT NULL,
  `floor` int(2) NOT NULL,
  `postCode` int(6) NOT NULL,
  `poBox` int(10) NOT NULL,
  `phone1` varchar(20) CHARACTER SET latin1 NOT NULL,
  `phone2` varchar(20) CHARACTER SET latin1 NOT NULL,
  `fax` varchar(20) CHARACTER SET latin1 NOT NULL,
  `mainEmail` varchar(220) CHARACTER SET latin1 NOT NULL,
  `paymentTerms` int(3) NOT NULL,
  `foreignDate` bigint(30) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `addedBy` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`imspid`),
  UNIQUE KEY `foreignId` (`foreignId`,`foreignName`,`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=11571 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `integration_mediation_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_products` (
  `impid` int(20) NOT NULL AUTO_INCREMENT,
  `localId` int(10) NOT NULL,
  `foreignSystem` tinyint(1) NOT NULL DEFAULT '1',
  `foreignId` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `foreignName` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `foreignNameAbbr` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `foreignSupplier` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `affid` int(10) NOT NULL,
  `localDate` bigint(30) NOT NULL,
  `foreignDate` bigint(30) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `addedBy` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`impid`,`localId`,`foreignSystem`,`foreignId`),
  UNIQUE KEY `foreignId` (`foreignId`,`foreignNameAbbr`,`affid`),
  KEY `affid` (`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=11110 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `integration_mediation_purchaseorderlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_purchaseorderlines` (
  `impolid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `foreignOrderId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `pid` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `spid` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `affid` int(10) NOT NULL,
  `price` float NOT NULL,
  `quantity` float NOT NULL,
  `quantityUnit` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `psid` int(10) DEFAULT NULL,
  `salesRep` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salesRepLocalId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`impolid`),
  KEY `pid` (`pid`,`spid`)
) ENGINE=MyISAM AUTO_INCREMENT=2580687 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `integration_mediation_purchaseorders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_purchaseorders` (
  `impoid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignSystem` tinyint(1) NOT NULL,
  `foreignId` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `docNum` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` bigint(30) NOT NULL,
  `spid` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `affid` int(10) NOT NULL,
  `currency` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `usdFxrate` float DEFAULT NULL,
  `paymentTerms` int(3) DEFAULT NULL,
  `purchaseType` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`impoid`),
  KEY `cid` (`spid`,`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=6234 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `integration_mediation_salesorderlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_salesorderlines` (
  `imsolid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignId` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `foreignOrderId` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pid` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `spid` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `affid` int(10) NOT NULL,
  `price` float NOT NULL,
  `quantity` float NOT NULL,
  `quantityUnit` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cost` float NOT NULL,
  `costCurrency` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchasePrice` float NOT NULL,
  `purPriceCurrency` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`imsolid`),
  KEY `pid` (`pid`,`spid`),
  KEY `foreignOrderId` (`foreignOrderId`),
  KEY `foreignId` (`foreignId`)
) ENGINE=MyISAM AUTO_INCREMENT=10890433 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `integration_mediation_salesorders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_salesorders` (
  `imsoid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignSystem` tinyint(1) NOT NULL,
  `foreignId` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `docNum` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` bigint(30) NOT NULL,
  `cid` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `affid` int(10) NOT NULL,
  `currency` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `usdFxrate` float DEFAULT NULL,
  `paymentTerms` int(3) DEFAULT NULL,
  `salesRep` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `salesRepLocalId` int(10) NOT NULL,
  `saleType` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`imsoid`),
  KEY `cid` (`cid`,`affid`,`salesRep`),
  KEY `salesRepLocalId` (`salesRepLocalId`)
) ENGINE=MyISAM AUTO_INCREMENT=20651 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `integration_mediation_stockpurchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_stockpurchases` (
  `imspid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignSystem` tinyint(1) NOT NULL DEFAULT '1',
  `spid` varchar(32) NOT NULL,
  `affid` smallint(5) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `date` bigint(30) NOT NULL,
  `amount` float NOT NULL,
  `currency` varchar(4) NOT NULL DEFAULT 'USD',
  `usdFxrate` float NOT NULL DEFAULT '1',
  `quantity` float NOT NULL,
  `quantityUnit` varchar(10) NOT NULL,
  `saleType` varchar(10) NOT NULL,
  `orderId` varchar(32) NOT NULL,
  `orderLineId` varchar(32) NOT NULL,
  `TRansID` int(10) NOT NULL,
  PRIMARY KEY (`imspid`,`spid`,`affid`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=880 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keycustomers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keycustomers` (
  `kcid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(10) unsigned NOT NULL,
  `rid` int(10) unsigned NOT NULL,
  `rank` int(2) unsigned NOT NULL,
  `status` text,
  `changes` text,
  `risksOpportunities` text,
  PRIMARY KEY (`kcid`),
  KEY `cid` (`cid`,`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=38637 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kinships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kinships` (
  `kiid` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(120) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`kiid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves` (
  `lid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `requestKey` varchar(100) NOT NULL,
  `fromDate` bigint(30) NOT NULL,
  `toDate` bigint(30) NOT NULL,
  `numWorkingDays` float NOT NULL,
  `type` smallint(5) NOT NULL,
  `reason` text NOT NULL,
  `contactPerson` int(10) unsigned NOT NULL,
  `addressWhileAbsent` text NOT NULL,
  `phoneWhileAbsent` varchar(20) NOT NULL,
  `requestTime` bigint(30) NOT NULL,
  `limitedEmail` tinyint(1) NOT NULL DEFAULT '1',
  `affToInform` text NOT NULL,
  `affid` int(10) DEFAULT NULL,
  `spid` int(10) DEFAULT NULL,
  `cid` int(10) DEFAULT NULL,
  `coid` int(10) DEFAULT NULL,
  `ceid` int(10) DEFAULT NULL,
  `kiid` tinyint(10) unsigned DEFAULT NULL,
  `psid` smallint(5) NOT NULL,
  `ltpid` mediumint(10) NOT NULL,
  `sourceCity` int(10) DEFAULT NULL,
  `destinationCity` int(10) DEFAULT NULL,
  PRIMARY KEY (`lid`),
  KEY `uid` (`uid`),
  KEY `type` (`type`),
  KEY `contactPerson` (`contactPerson`),
  KEY `coid` (`coid`),
  KEY `kiid` (`kiid`),
  KEY `psid` (`psid`,`ltpid`)
) ENGINE=MyISAM AUTO_INCREMENT=14084 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leaves_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves_messages` (
  `lmid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `lid` int(10) DEFAULT NULL,
  `msgId` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inReplyTo` int(10) NOT NULL,
  `inReplyToMsgId` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `viewPermission` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`lmid`),
  KEY `uid` (`uid`,`inReplyTo`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leavesapproval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leavesapproval` (
  `laid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `isApproved` tinyint(1) NOT NULL,
  `timeApproved` bigint(30) NOT NULL,
  `sequence` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`laid`,`lid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=15026 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leavesstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leavesstats` (
  `lsid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `ltid` smallint(5) unsigned NOT NULL,
  `year` mediumint(4) unsigned NOT NULL,
  `periodStart` bigint(30) NOT NULL,
  `periodEnd` bigint(30) NOT NULL,
  `daysTaken` float NOT NULL,
  `canTake` float NOT NULL,
  `entitledFor` float unsigned NOT NULL,
  `remainPrevYear` float NOT NULL,
  `additionalDays` float NOT NULL,
  PRIMARY KEY (`lsid`,`uid`,`ltid`)
) ENGINE=MyISAM AUTO_INCREMENT=2026 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leavesstats2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leavesstats2` (
  `lsid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `ltid` smallint(5) unsigned NOT NULL,
  `year` mediumint(4) unsigned NOT NULL,
  `periodStart` bigint(30) NOT NULL,
  `periodEnd` bigint(30) NOT NULL,
  `daysTaken` float NOT NULL,
  `canTake` float NOT NULL,
  `entitledFor` float unsigned NOT NULL,
  `remainPrevYear` float NOT NULL,
  `additionalDays` float NOT NULL,
  PRIMARY KEY (`lsid`,`uid`,`ltid`)
) ENGINE=MyISAM AUTO_INCREMENT=1207 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leavesstats3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leavesstats3` (
  `lsid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `ltid` smallint(5) unsigned NOT NULL,
  `year` mediumint(4) unsigned NOT NULL,
  `periodStart` bigint(30) NOT NULL,
  `periodEnd` bigint(30) NOT NULL,
  `daysTaken` float NOT NULL,
  `canTake` float NOT NULL,
  `entitledFor` float unsigned NOT NULL,
  `remainPrevYear` float NOT NULL,
  `additionalDays` float NOT NULL,
  PRIMARY KEY (`lsid`,`uid`,`ltid`)
) ENGINE=MyISAM AUTO_INCREMENT=1051 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leavetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leavetypes` (
  `ltid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(220) NOT NULL,
  `symbol` varchar(10) DEFAULT NULL,
  `description` varchar(220) DEFAULT NULL,
  `isWholeDay` tinyint(1) NOT NULL DEFAULT '1',
  `isPM` tinyint(1) DEFAULT '0',
  `isAnnual` tinyint(1) NOT NULL DEFAULT '1',
  `isSick` tinyint(1) NOT NULL DEFAULT '0',
  `isBusiness` tinyint(1) NOT NULL DEFAULT '0',
  `isUnpaid` tinyint(1) NOT NULL DEFAULT '0',
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  `noNotification` tinyint(1) NOT NULL DEFAULT '0',
  `noBalance` tinyint(1) NOT NULL DEFAULT '1',
  `reasonIsRequired` tinyint(1) NOT NULL DEFAULT '0',
  `toApprove` text NOT NULL,
  `additionalFields` text,
  `countWith` smallint(5) DEFAULT '0',
  `coexistWith` text,
  `isActive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ltid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leavetypes_purposes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leavetypes_purposes` (
  `ltpid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` int(10) NOT NULL,
  PRIMARY KEY (`ltpid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `lid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `ipaddress` varchar(50) NOT NULL DEFAULT '',
  `date` bigint(30) NOT NULL DEFAULT '0',
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  PRIMARY KEY (`lid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=74825 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketintelligence_basicdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketintelligence_basicdata` (
  `mibdid` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL,
  `cfpid` int(10) NOT NULL,
  `cfcid` int(10) DEFAULT '0',
  `biid` int(10) NOT NULL,
  `ebpid` int(10) NOT NULL,
  `eptid` int(10) NOT NULL,
  `affid` smallint(5) NOT NULL,
  `vrid` int(10) DEFAULT NULL,
  `vridentifier` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `potential` float NOT NULL,
  `mktSharePerc` float NOT NULL,
  `mktShareQty` float NOT NULL,
  `unitPrice` float NOT NULL,
  `turnover` float NOT NULL,
  `comments` text COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`mibdid`),
  KEY `cid` (`cid`,`cfpid`,`ebpid`),
  KEY `affid` (`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=145 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketintelligence_competitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketintelligence_competitors` (
  `micid` int(10) NOT NULL AUTO_INCREMENT,
  `mibdid` int(10) NOT NULL,
  `trader` int(10) NOT NULL,
  `producer` int(10) NOT NULL,
  `unitPrice` float NOT NULL,
  `pid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`micid`),
  KEY `mibdid` (`mibdid`,`trader`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketreport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketreport` (
  `mrid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL,
  `psid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `markTrendCompetition` text NOT NULL,
  `quarterlyHighlights` text NOT NULL,
  `devProjectsNewOp` text NOT NULL,
  `issues` text NOT NULL,
  `actionPlan` text NOT NULL,
  `remarks` varchar(300) NOT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`mrid`),
  KEY `rid` (`rid`),
  FULLTEXT KEY `markTrendCompetition` (`markTrendCompetition`,`quarterlyHighlights`,`devProjectsNewOp`,`issues`,`actionPlan`),
  FULLTEXT KEY `markTrendCompetition_2` (`markTrendCompetition`),
  FULLTEXT KEY `quarterlyHighlights` (`quarterlyHighlights`)
) ENGINE=MyISAM AUTO_INCREMENT=8347 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketreport_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketreport_authors` (
  `mkra` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `mrid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mkra`),
  KEY `uid` (`uid`,`mrid`)
) ENGINE=MyISAM AUTO_INCREMENT=6840 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketreport_competition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketreport_competition` (
  `mrcid` int(10) NOT NULL AUTO_INCREMENT,
  `mrid` int(10) NOT NULL,
  `sid` int(10) NOT NULL,
  `coid` int(10) NOT NULL,
  `inputChecksum` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`mrcid`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketreport_competition_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketreport_competition_products` (
  `mrcpid` int(10) NOT NULL AUTO_INCREMENT,
  `mrcid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `csid` int(10) NOT NULL,
  `inputChecksum` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `howCanWeBeatThem` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`mrcpid`),
  UNIQUE KEY `mrcpid` (`mrcpid`),
  KEY `mrcid` (`mrcid`),
  KEY `csid` (`csid`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketreport_developmentpojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketreport_developmentpojects` (
  `mrdpid` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(10) NOT NULL,
  `mrid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `potentialQty` int(11) NOT NULL,
  `successPerc` int(11) NOT NULL,
  `who` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `what` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `whenn` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `inputChecksum` varchar(150) NOT NULL,
  PRIMARY KEY (`mrdpid`),
  UNIQUE KEY `mrdpid` (`mrdpid`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketreport_developmentpojects_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketreport_developmentpojects_products` (
  `mrdppid` int(10) NOT NULL AUTO_INCREMENT,
  `mrdpid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `potentialQty` int(11) NOT NULL,
  `successPerc` int(11) NOT NULL,
  `who` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `what` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `whenn` bigint(30) NOT NULL,
  `inputChecksum` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`mrdppid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings` (
  `mtid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `fromDate` bigint(30) NOT NULL,
  `toDate` bigint(30) NOT NULL,
  `location` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `hasMoM` tinyint(1) NOT NULL,
  `isPublic` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mtid`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meetings_associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings_associations` (
  `mtaid` int(10) NOT NULL AUTO_INCREMENT,
  `mtid` int(10) NOT NULL,
  `id` int(10) NOT NULL,
  `idAttr` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`mtaid`,`mtid`),
  UNIQUE KEY `mtaid` (`mtaid`),
  KEY `mtaid_2` (`mtaid`),
  KEY `mtaid_3` (`mtaid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meetings_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings_attachments` (
  `mattid` int(10) NOT NULL AUTO_INCREMENT,
  `mtid` int(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `size` float NOT NULL,
  `type` varchar(50) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`mattid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meetings_attendees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings_attendees` (
  `matid` int(10) NOT NULL AUTO_INCREMENT,
  `mtid` int(10) NOT NULL,
  `attendee` int(10) NOT NULL,
  `idAttr` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`matid`,`mtid`),
  UNIQUE KEY `matid` (`matid`),
  KEY `matid_2` (`matid`),
  KEY `matid_3` (`matid`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meetings_minsofmeeting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings_minsofmeeting` (
  `momid` int(10) NOT NULL AUTO_INCREMENT,
  `mtid` int(10) NOT NULL,
  `meetingDetails` text NOT NULL,
  `followup` text NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`momid`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meetings_mom_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings_mom_actions` (
  `momaid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date` bigint(30) NOT NULL,
  `momid` int(10) NOT NULL,
  `isTask` tinyint(1) NOT NULL DEFAULT '0',
  `what` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`momaid`),
  KEY `momid` (`momid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meetings_sharedwith`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings_sharedwith` (
  `mswid` int(10) NOT NULL AUTO_INCREMENT,
  `mtid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`mswid`,`mtid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `monthly_highlights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monthly_highlights` (
  `hid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL,
  `accomplishments` text NOT NULL,
  `actions` text NOT NULL,
  `considerations` text,
  PRIMARY KEY (`hid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=331 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `monthly_productsstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monthly_productsstatus` (
  `psid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL,
  `gpid` int(10) unsigned NOT NULL,
  `csid` int(10) NOT NULL DEFAULT '0',
  `status` text NOT NULL,
  PRIMARY KEY (`psid`,`rid`,`gpid`,`csid`),
  KEY `rid` (`rid`,`gpid`),
  KEY `csid` (`csid`)
) ENGINE=MyISAM AUTO_INCREMENT=1725 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `packaging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packaging` (
  `packid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`packid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `paymentterms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentterms` (
  `ptid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `overduePaymentDays` int(11) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nextBusinessDay` tinyint(1) NOT NULL,
  PRIMARY KEY (`ptid`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `positiongroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positiongroups` (
  `posgid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`posgid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions` (
  `posid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(220) NOT NULL,
  PRIMARY KEY (`posid`)
) ENGINE=MyISAM AUTO_INCREMENT=210 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productcharacteristics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productcharacteristics` (
  `pcid` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(110) NOT NULL,
  `title` varchar(220) NOT NULL,
  PRIMARY KEY (`pcid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productcharacteristics_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productcharacteristics_values` (
  `pcvid` int(5) NOT NULL AUTO_INCREMENT,
  `pcid` int(5) NOT NULL,
  `name` varchar(110) NOT NULL,
  `title` varchar(220) NOT NULL,
  PRIMARY KEY (`pcvid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `spid` int(10) unsigned NOT NULL,
  `gpid` int(10) unsigned NOT NULL,
  `defaultFunction` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `code` varchar(100) NOT NULL,
  `description` text,
  `defaultCurrency` varchar(10) NOT NULL,
  `taxRate` float DEFAULT NULL,
  `package` varchar(220) DEFAULT NULL,
  `itemWeight` float DEFAULT NULL,
  `standard` varchar(220) DEFAULT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) DEFAULT NULL,
  `modifiedOn` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`pid`),
  KEY `spid` (`spid`,`gpid`,`name`,`code`),
  KEY `defaultFunction` (`defaultFunction`),
  FULLTEXT KEY `name` (`name`,`description`)
) ENGINE=MyISAM AUTO_INCREMENT=2787 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products2` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `spid` int(10) unsigned NOT NULL,
  `gpid` int(10) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `code` varchar(100) NOT NULL,
  `description` text,
  `defaultCurrency` varchar(10) NOT NULL,
  `taxRate` float DEFAULT NULL,
  `package` varchar(220) DEFAULT NULL,
  `itemWeight` float DEFAULT NULL,
  `standard` varchar(220) DEFAULT NULL,
  PRIMARY KEY (`pid`),
  KEY `spid` (`spid`,`gpid`,`name`,`code`)
) ENGINE=MyISAM AUTO_INCREMENT=1219 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsactivity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsactivity` (
  `paid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `rid` int(10) unsigned NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `quantity` float unsigned NOT NULL,
  `soldQty` float unsigned NOT NULL,
  `turnOver` float unsigned NOT NULL,
  `turnOverOc` float unsigned NOT NULL,
  `originalCurrency` varchar(4) DEFAULT NULL,
  `quantityForecast` float unsigned NOT NULL,
  `salesForecast` float unsigned NOT NULL,
  `saleType` varchar(12) NOT NULL,
  PRIMARY KEY (`paid`),
  KEY `pid` (`pid`,`rid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=71474 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsactivity2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsactivity2` (
  `paid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `rid` int(10) unsigned NOT NULL,
  `quantity` float unsigned NOT NULL,
  `turnOver` float unsigned NOT NULL,
  `turnOverOc` float unsigned NOT NULL,
  `quantityForecast` float unsigned NOT NULL,
  `salesForecast` float unsigned NOT NULL,
  `saleType` varchar(12) NOT NULL,
  PRIMARY KEY (`paid`),
  KEY `pid` (`pid`,`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=42813 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsactivity3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsactivity3` (
  `paid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `rid` int(10) unsigned NOT NULL,
  `quantity` float unsigned NOT NULL,
  `turnOver` float unsigned NOT NULL,
  `turnOverOc` float unsigned NOT NULL,
  `quantityForecast` float unsigned NOT NULL,
  `salesForecast` float unsigned NOT NULL,
  `saleType` varchar(12) NOT NULL,
  PRIMARY KEY (`paid`),
  KEY `pid` (`pid`,`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productschemsubstances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productschemsubstances` (
  `pcsid` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `csid` int(10) NOT NULL,
  `createdBy` bigint(30) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`pcsid`)
) ENGINE=MyISAM AUTO_INCREMENT=179 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsegements_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsegements_applications` (
  `psaid` int(10) NOT NULL AUTO_INCREMENT,
  `psid` int(10) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`psaid`,`psid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsegmentcoordinators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsegmentcoordinators` (
  `pscid` int(10) NOT NULL AUTO_INCREMENT,
  `psid` smallint(5) NOT NULL,
  `uid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`pscid`,`psid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsegments` (
  `psid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) NOT NULL,
  `title` varchar(220) NOT NULL,
  `titleSoundex` varchar(100) NOT NULL,
  `titleAbbr` varchar(20) DEFAULT NULL,
  `description` text,
  `shortDescription` varchar(300) DEFAULT NULL,
  `category` tinyint(10) NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `smallBanner` varchar(100) DEFAULT NULL,
  `mediumBanner` varchar(100) DEFAULT NULL,
  `largeBanner` varchar(100) DEFAULT NULL,
  `slogan` varchar(100) DEFAULT NULL,
  `brandingColor` varchar(7) DEFAULT NULL,
  `displaySequence` int(3) DEFAULT NULL,
  PRIMARY KEY (`psid`),
  KEY `category` (`category`),
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsegments2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsegments2` (
  `psid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(220) NOT NULL,
  `titleAbbr` varchar(20) DEFAULT NULL,
  `description` text,
  `category` tinyint(10) NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`psid`),
  KEY `category` (`category`),
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsegments3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsegments3` (
  `psid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) NOT NULL,
  `title` varchar(220) NOT NULL,
  `titleAbbr` varchar(20) DEFAULT NULL,
  `description` text,
  `shortDescription` varchar(300) NOT NULL,
  `category` tinyint(10) NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL,
  `smallBanner` varchar(100) NOT NULL,
  `mediumBanner` varchar(100) NOT NULL,
  `largeBanner` varchar(100) NOT NULL,
  `slogan` varchar(100) NOT NULL,
  `brandingColor` varchar(30) NOT NULL,
  PRIMARY KEY (`psid`),
  KEY `category` (`category`),
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productsegments_mailinglists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsegments_mailinglists` (
  `psmid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `psid` smallint(5) NOT NULL,
  `email` varchar(220) NOT NULL,
  PRIMARY KEY (`psmid`,`affid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purchasetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasetypes` (
  `ptid` smallint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `abbreviation` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `altName` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `useLocalCurrency` tinyint(1) NOT NULL,
  `invoiceAffStid` int(10) NOT NULL,
  `sequence` int(1) NOT NULL,
  `isPurchasedByEndUser` tinyint(1) NOT NULL,
  `qtyIsNotStored` tinyint(1) NOT NULL,
  PRIMARY KEY (`ptid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reportcontributors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportcontributors` (
  `rcid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `rid` int(10) unsigned NOT NULL,
  `isDone` smallint(1) NOT NULL DEFAULT '0',
  `timeDone` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`rcid`),
  KEY `uid` (`uid`,`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=8850 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reporting_qrrecipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reporting_qrrecipients` (
  `rqrrid` int(10) NOT NULL AUTO_INCREMENT,
  `reportIdentifier` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `rpid` int(10) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `unregisteredRcpts` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `loginKey` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `sentOn` bigint(30) NOT NULL,
  `sentBy` int(10) NOT NULL,
  PRIMARY KEY (`rqrrid`),
  KEY `rpid` (`rpid`),
  KEY `sentBy` (`sentBy`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=835 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reporting_qrrecipients_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reporting_qrrecipients_views` (
  `rqrrvid` int(10) NOT NULL AUTO_INCREMENT,
  `rqrrid` int(10) NOT NULL,
  `time` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `ipAddress` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rqrrvid`,`rqrrid`)
) ENGINE=MyISAM AUTO_INCREMENT=516 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reporting_report_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reporting_report_summary` (
  `rpsid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `summary` text NOT NULL,
  PRIMARY KEY (`rpsid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `year` int(4) unsigned NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  `spid` int(10) unsigned NOT NULL,
  `initDate` bigint(30) unsigned NOT NULL,
  `uidFinish` int(10) unsigned NOT NULL,
  `finishDate` bigint(30) unsigned NOT NULL,
  `isLocked` smallint(1) DEFAULT '0',
  `isSent` smallint(1) DEFAULT '0',
  `type` varchar(1) NOT NULL DEFAULT 'q',
  `month` int(2) NOT NULL,
  `quarter` int(1) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `prActivityAvailable` smallint(1) NOT NULL DEFAULT '0',
  `keyCustAvailable` smallint(1) NOT NULL DEFAULT '0',
  `mktReportAvailable` smallint(1) NOT NULL DEFAULT '0',
  `isApproved` smallint(1) NOT NULL DEFAULT '0',
  `summary` int(10) DEFAULT NULL,
  `dataIsImported` tinyint(1) NOT NULL DEFAULT '0',
  `sentOn` bigint(30) DEFAULT NULL,
  `importedOn` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`rid`),
  KEY `affid` (`affid`,`spid`),
  KEY `summary` (`summary`)
) ENGINE=MyISAM AUTO_INCREMENT=8700 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reports2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports2` (
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year` int(4) unsigned NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  `spid` int(10) unsigned NOT NULL,
  `initDate` bigint(30) unsigned NOT NULL,
  `uidFinish` int(10) unsigned NOT NULL,
  `finishDate` bigint(30) unsigned NOT NULL,
  `isLocked` smallint(1) DEFAULT '0',
  `isSent` smallint(1) DEFAULT '0',
  `type` varchar(1) NOT NULL DEFAULT 'q',
  `month` int(2) NOT NULL,
  `quarter` int(1) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `prActivityAvailable` smallint(1) NOT NULL DEFAULT '0',
  `keyCustAvailable` smallint(1) NOT NULL DEFAULT '0',
  `mktReportAvailable` smallint(1) NOT NULL DEFAULT '0',
  `isApproved` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`),
  KEY `affid` (`affid`,`spid`)
) ENGINE=MyISAM AUTO_INCREMENT=1749 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reportssendlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportssendlog` (
  `rslid` int(10) NOT NULL AUTO_INCREMENT,
  `report` int(11) NOT NULL,
  `affid` int(10) NOT NULL,
  `date` bigint(30) NOT NULL,
  `sentBy` int(10) NOT NULL,
  `SenTo` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rslid`),
  KEY `affid` (`affid`,`sentBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `representatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representatives` (
  `rpid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `isSupportive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`rpid`)
) ENGINE=MyISAM AUTO_INCREMENT=5872 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `representativespositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representativespositions` (
  `rppid` int(10) NOT NULL AUTO_INCREMENT,
  `rpid` int(10) NOT NULL,
  `posid` smallint(5) NOT NULL,
  PRIMARY KEY (`rppid`,`rpid`,`posid`)
) ENGINE=MyISAM AUTO_INCREMENT=931 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `representativessegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representativessegments` (
  `rpsid` int(10) NOT NULL AUTO_INCREMENT,
  `rpid` int(10) NOT NULL,
  `psid` smallint(5) NOT NULL,
  PRIMARY KEY (`rpsid`,`rpid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=1655 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reputation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reputation` (
  `repid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `url` varchar(220) NOT NULL,
  `description` text,
  `timeLine` bigint(30) NOT NULL,
  `addedBy` int(10) NOT NULL,
  PRIMARY KEY (`repid`),
  KEY `addedBy` (`addedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `saletypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saletypes` (
  `stid` smallint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `abbreviation` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `altName` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `useLocalCurrency` tinyint(1) NOT NULL,
  `invoiceAffStid` smallint(10) DEFAULT NULL,
  `countLocally` tinyint(1) NOT NULL DEFAULT '1',
  `localIncomeByDefault` tinyint(1) NOT NULL DEFAULT '1',
  `isIntercompanyTrx` tinyint(1) NOT NULL DEFAULT '0',
  `sequence` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `saletypes_invoicing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saletypes_invoicing` (
  `stiid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `affid` int(5) NOT NULL,
  `stid` smallint(10) NOT NULL,
  `invoicingEntity` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `isAffiliate` tinyint(1) NOT NULL DEFAULT '0',
  `invoiceAffid` smallint(5) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`stiid`,`affid`,`stid`),
  UNIQUE KEY `affid_2` (`affid`,`stid`,`invoicingEntity`),
  KEY `affid` (`affid`,`stid`,`invoiceAffid`)
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `segapplicationfunctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `segapplicationfunctions` (
  `safid` int(10) NOT NULL AUTO_INCREMENT,
  `cfid` int(10) NOT NULL,
  `psaid` int(10) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`safid`,`cfid`,`psaid`),
  FULLTEXT KEY `description` (`description`)
) ENGINE=MyISAM AUTO_INCREMENT=2073 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `segmentapplications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `segmentapplications` (
  `psaid` int(10) NOT NULL AUTO_INCREMENT,
  `psid` smallint(5) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` tinyint(1) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '1',
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` int(30) NOT NULL,
  PRIMARY KEY (`psaid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`),
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM AUTO_INCREMENT=878 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `segmentation_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `segmentation_import` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `segment` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `application` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `function` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `chemical` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=944 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `segmentscategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `segmentscategories` (
  `scid` tinyint(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `shortDescription` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL,
  `includeInWebsiteCarousel` tinyint(1) NOT NULL DEFAULT '1',
  `smallBanner` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `mediumBanner` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `largeBanner` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `slogan` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `brandingColor` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `featuredSequence` tinyint(1) NOT NULL,
  `defaultSegment` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`scid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `sid` varchar(32) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `time` bigint(30) NOT NULL,
  `ip` varchar(40) NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `sid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `optionscode` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_chemicalrequests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_chemicalrequests` (
  `scrid` int(10) NOT NULL AUTO_INCREMENT,
  `csid` mediumint(10) DEFAULT NULL,
  `psaid` int(10) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `timeRequested` bigint(30) NOT NULL,
  `requestDescription` text COLLATE utf8_unicode_ci NOT NULL,
  `feedback` text COLLATE utf8_unicode_ci NOT NULL,
  `feedbackBy` int(10) NOT NULL,
  `feedbackTime` bigint(30) NOT NULL,
  `isClosed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`scrid`),
  KEY `csid` (`csid`,`uid`),
  KEY `psaid` (`psaid`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_chemreqs_origins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_chemreqs_origins` (
  `scroid` int(10) NOT NULL AUTO_INCREMENT,
  `scrid` int(10) NOT NULL,
  `origin` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`scroid`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers` (
  `ssid` int(10) NOT NULL AUTO_INCREMENT,
  `companyName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `companyNameAbbr` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `country` int(10) NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine1` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine2` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `building` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `floor` int(3) NOT NULL,
  `postCode` int(6) NOT NULL,
  `geoLocation` point NOT NULL,
  `poBox` int(10) NOT NULL,
  `phone1` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `phone2` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `mainEmail` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `commentsToShare` text COLLATE utf8_unicode_ci NOT NULL,
  `marketingRecords` text COLLATE utf8_unicode_ci NOT NULL,
  `coBriefing` text COLLATE utf8_unicode_ci NOT NULL,
  `historical` text COLLATE utf8_unicode_ci NOT NULL,
  `sourcingRecords` text COLLATE utf8_unicode_ci NOT NULL,
  `productFunction` text COLLATE utf8_unicode_ci NOT NULL,
  `approachedVia` tinyint(1) NOT NULL,
  `businessPotential` tinyint(1) NOT NULL,
  `relationMaturity` tinyint(1) NOT NULL DEFAULT '0',
  `isBlacklisted` tinyint(1) NOT NULL,
  `eid` int(10) DEFAULT NULL,
  PRIMARY KEY (`ssid`)
) ENGINE=MyISAM AUTO_INCREMENT=1336 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_activityareas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_activityareas` (
  `ssaid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `coid` int(10) NOT NULL,
  `availability` tinyint(1) NOT NULL,
  PRIMARY KEY (`ssaid`,`ssid`,`coid`)
) ENGINE=MyISAM AUTO_INCREMENT=110306 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_activityareas_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_activityareas_new` (
  `ssaid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `coid` int(10) NOT NULL,
  `availability` tinyint(1) NOT NULL,
  PRIMARY KEY (`ssaid`,`ssid`,`coid`)
) ENGINE=MyISAM AUTO_INCREMENT=12737 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_blhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_blhistory` (
  `ssbid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `reason` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `requestedBy` int(10) NOT NULL,
  `requestedOn` bigint(30) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `removedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`ssbid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_chemicals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_chemicals` (
  `sscid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `csid` int(10) NOT NULL,
  `supplyType` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sscid`,`ssid`,`csid`)
) ENGINE=MyISAM AUTO_INCREMENT=14431 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_chemicals_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_chemicals_new` (
  `sscid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `csid` int(10) NOT NULL,
  `supplyType` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sscid`,`ssid`,`csid`)
) ENGINE=MyISAM AUTO_INCREMENT=7647 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_contacthist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_contacthist` (
  `sschid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ssid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `affid` smallint(5) NOT NULL,
  `chemical` int(10) NOT NULL,
  `origin` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `application` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `grade` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `market` smallint(5) NOT NULL,
  `competitors` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date` bigint(30) NOT NULL,
  `paymentTerms` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `discussion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `customerDocumentDate` bigint(30) NOT NULL,
  `customerDocument` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `requestedQuantity` smallint(20) NOT NULL,
  `requestedQuantityUom` smallint(10) NOT NULL,
  `requestedDocuments` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `receivedQuantity` smallint(20) NOT NULL,
  `receivedQantityUom` smallint(10) NOT NULL,
  `receivedDocuments` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `providedQuantity` smallint(20) NOT NULL,
  `providedQuantityUom` smallint(10) NOT NULL,
  `providedDocuments` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `customerAnswer` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `receivedQuantityDate` bigint(20) NOT NULL,
  `providedDocumentsDate` bigint(20) NOT NULL,
  `customerAnswerDate` bigint(20) NOT NULL,
  `industrialQuantity` smallint(20) NOT NULL,
  `industrialQuantityUom` smallint(10) NOT NULL,
  `provisionDate` bigint(20) NOT NULL,
  `trialResult` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `offerMade` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `offerDate` bigint(20) NOT NULL,
  `customerOfferAnswer` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `offerAnswerDate` bigint(20) NOT NULL,
  `sourcingNotPossibleDesc` text COLLATE utf8_unicode_ci NOT NULL,
  `isCompleted` tinyint(1) NOT NULL,
  `isPriceApproved` tinyint(1) NOT NULL DEFAULT '0',
  `isPaymentApproved` tinyint(1) NOT NULL DEFAULT '0',
  `isCustomerdocumentApproved` tinyint(1) NOT NULL DEFAULT '0',
  `isSampleAccepted` tinyint(1) NOT NULL DEFAULT '0',
  `isCompliantSpec` tinyint(1) NOT NULL DEFAULT '0',
  `isProductApproved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sschid`),
  KEY `ssid` (`ssid`,`uid`,`affid`,`chemical`,`origin`,`market`)
) ENGINE=MyISAM AUTO_INCREMENT=776 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_contacthist_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_contacthist_new` (
  `sschid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ssid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `affid` smallint(5) NOT NULL,
  `chemical` int(10) NOT NULL,
  `origin` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `application` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `grade` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `market` smallint(5) NOT NULL,
  `competitors` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date` bigint(30) NOT NULL,
  `paymentTerms` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `discussion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `customerDocumentDate` bigint(30) NOT NULL,
  `customerDocument` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `requestedQuantity` smallint(20) NOT NULL,
  `requestedQuantityUom` smallint(10) NOT NULL,
  `requestedDocuments` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `receivedQuantity` smallint(20) NOT NULL,
  `receivedQantityUom` smallint(10) NOT NULL,
  `receivedDocuments` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `providedQuantity` smallint(20) NOT NULL,
  `providedQuantityUom` smallint(10) NOT NULL,
  `providedDocuments` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `customerAnswer` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `receivedQuantityDate` bigint(20) NOT NULL,
  `providedDocumentsDate` bigint(20) NOT NULL,
  `customerAnswerDate` bigint(20) NOT NULL,
  `industrialQuantity` smallint(20) NOT NULL,
  `industrialQuantityUom` smallint(10) NOT NULL,
  `provisionDate` bigint(20) NOT NULL,
  `trialResult` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `offerMade` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `offerDate` bigint(20) NOT NULL,
  `customerOfferAnswer` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `offerAnswerDate` bigint(20) NOT NULL,
  `sourcingNotPossibleDesc` text COLLATE utf8_unicode_ci NOT NULL,
  `isCompleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`sschid`),
  KEY `ssid` (`ssid`,`uid`,`affid`,`chemical`,`origin`,`market`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_contactpersons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_contactpersons` (
  `sscpid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `rpid` int(11) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`sscpid`,`ssid`,`rpid`)
) ENGINE=MyISAM AUTO_INCREMENT=3458 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_contactpersons_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_contactpersons_new` (
  `sscpid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `rpid` int(11) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`sscpid`,`ssid`,`rpid`)
) ENGINE=MyISAM AUTO_INCREMENT=300 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_genericprod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_genericprod` (
  `ssgpid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `gpid` int(11) NOT NULL,
  `supplyType` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ssgpid`,`ssid`,`gpid`),
  KEY `ssid` (`ssid`,`gpid`),
  KEY `ssgpid` (`ssgpid`,`ssid`,`gpid`)
) ENGINE=MyISAM AUTO_INCREMENT=717 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_new` (
  `ssid` int(10) NOT NULL AUTO_INCREMENT,
  `companyName` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `companyNameAbbr` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `country` int(10) NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine1` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine2` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `building` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `floor` int(3) NOT NULL,
  `postCode` int(6) NOT NULL,
  `geoLocation` point NOT NULL,
  `poBox` int(10) NOT NULL,
  `phone1` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `phone2` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `mainEmail` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `commentsToShare` text COLLATE utf8_unicode_ci NOT NULL,
  `marketingRecords` text COLLATE utf8_unicode_ci NOT NULL,
  `coBriefing` text COLLATE utf8_unicode_ci NOT NULL,
  `historical` text COLLATE utf8_unicode_ci NOT NULL,
  `sourcingRecords` text COLLATE utf8_unicode_ci NOT NULL,
  `productFunction` text COLLATE utf8_unicode_ci NOT NULL,
  `approachedVia` tinyint(1) NOT NULL,
  `businessPotential` tinyint(1) NOT NULL,
  `relationMaturity` tinyint(1) NOT NULL DEFAULT '0',
  `isBlacklisted` tinyint(1) NOT NULL,
  PRIMARY KEY (`ssid`)
) ENGINE=MyISAM AUTO_INCREMENT=217 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_productsegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_productsegments` (
  `sspsid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `psid` int(10) NOT NULL,
  PRIMARY KEY (`sspsid`,`ssid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=9321 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sourcing_suppliers_productsegments_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_productsegments_new` (
  `sspsid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `psid` int(10) NOT NULL,
  PRIMARY KEY (`sspsid`,`ssid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=1059 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suppliersaudits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliersaudits` (
  `said` int(10) NOT NULL AUTO_INCREMENT,
  `eid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`said`),
  KEY `eid` (`eid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=3477 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys` (
  `sid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) NOT NULL,
  `reference` varchar(20) DEFAULT NULL,
  `subject` varchar(220) NOT NULL,
  `description` text,
  `category` smallint(2) NOT NULL,
  `stid` mediumint(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `closingDate` bigint(30) DEFAULT NULL,
  `isPublicFill` tinyint(1) DEFAULT NULL,
  `isPublicResults` tinyint(1) NOT NULL DEFAULT '0',
  `anonymousFilling` tinyint(1) NOT NULL DEFAULT '0',
  `isExternal` tinyint(1) NOT NULL DEFAULT '0',
  `customInvitationSubject` varchar(300) DEFAULT NULL,
  `customInvitationBody` text,
  PRIMARY KEY (`sid`),
  KEY `stid` (`stid`,`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_associations` (
  `aaid` int(10) NOT NULL AUTO_INCREMENT,
  `sid` int(10) NOT NULL,
  `attr` varchar(10) NOT NULL,
  `id` text NOT NULL,
  PRIMARY KEY (`aaid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=142 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_categories` (
  `scid` smallint(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`scid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_invitations` (
  `siid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) NOT NULL,
  `sid` int(10) NOT NULL,
  `invitee` varchar(220) NOT NULL,
  `isDone` tinyint(1) DEFAULT NULL,
  `timeDone` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`siid`,`sid`,`invitee`)
) ENGINE=MyISAM AUTO_INCREMENT=517 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_questiontypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_questiontypes` (
  `sqtid` tinyint(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text,
  `fieldType` varchar(15) NOT NULL,
  `hasChoices` tinyint(1) NOT NULL DEFAULT '0',
  `hasMultiAnswers` tinyint(1) NOT NULL DEFAULT '0',
  `hasValidation` tinyint(1) NOT NULL DEFAULT '0',
  `isSizable` tinyint(1) NOT NULL DEFAULT '0',
  `isQuantitative` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`sqtid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_responses` (
  `srid` int(10) NOT NULL AUTO_INCREMENT,
  `sid` int(10) NOT NULL,
  `stqid` int(10) NOT NULL,
  `invitee` varchar(10) NOT NULL,
  `identifier` varchar(100) NOT NULL,
  `response` text NOT NULL,
  `comments` text,
  `time` bigint(10) NOT NULL,
  PRIMARY KEY (`srid`,`sid`,`stqid`,`invitee`)
) ENGINE=MyISAM AUTO_INCREMENT=4237 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_sharedwith`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_sharedwith` (
  `sswid` int(10) NOT NULL AUTO_INCREMENT,
  `sid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`sswid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_templates` (
  `stid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) NOT NULL,
  `category` smallint(2) NOT NULL,
  `isPublic` tinyint(1) NOT NULL,
  `forceAnonymousFilling` tinyint(1) NOT NULL DEFAULT '0',
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  PRIMARY KEY (`stid`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_templates_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_templates_questions` (
  `stqid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stsid` int(10) NOT NULL,
  `question` varchar(220) NOT NULL,
  `description` text NOT NULL,
  `sequence` tinyint(4) NOT NULL,
  `type` tinyint(2) unsigned NOT NULL,
  `isRequired` tinyint(1) NOT NULL DEFAULT '1',
  `fieldSize` tinyint(3) DEFAULT NULL,
  `hasCommentsField` tinyint(1) NOT NULL DEFAULT '0',
  `commentsFieldTitle` varchar(200) DEFAULT NULL,
  `commentsFieldType` varchar(10) DEFAULT NULL,
  `commentsFieldSize` tinyint(3) DEFAULT NULL,
  `validationType` varchar(10) DEFAULT NULL,
  `validationCriterion` tinyint(3) DEFAULT NULL,
  PRIMARY KEY (`stqid`),
  KEY `type` (`type`),
  KEY `stid` (`stsid`)
) ENGINE=MyISAM AUTO_INCREMENT=186 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_templates_questions_choices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_templates_questions_choices` (
  `stqcid` int(10) NOT NULL AUTO_INCREMENT,
  `stqid` int(10) unsigned NOT NULL,
  `choice` varchar(220) NOT NULL,
  `value` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`stqcid`),
  KEY `stqid` (`stqid`)
) ENGINE=MyISAM AUTO_INCREMENT=2317 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surveys_templates_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_templates_sections` (
  `stsid` int(10) NOT NULL AUTO_INCREMENT,
  `stid` mediumint(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`stsid`),
  KEY `stid` (`stid`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `system_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_languages` (
  `slid` tinyint(10) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(10) CHARACTER SET latin1 NOT NULL,
  `name` varchar(20) CHARACTER SET latin1 NOT NULL,
  `version` float NOT NULL,
  `rtl` tinyint(1) NOT NULL,
  `htmllang` varchar(3) CHARACTER SET latin1 NOT NULL,
  `charset` varchar(10) CHARACTER SET latin1 NOT NULL,
  `author` int(10) NOT NULL,
  PRIMARY KEY (`slid`),
  KEY `author` (`author`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `system_languages_varvalues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_languages_varvalues` (
  `slvvid` int(10) NOT NULL AUTO_INCREMENT,
  `lang` tinyint(10) NOT NULL,
  `variable` int(10) NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `timeCreated` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `timeModified` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`slvvid`),
  KEY `modifiedBy` (`modifiedBy`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=3252 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `system_langvariables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_langvariables` (
  `slvid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET latin1 NOT NULL,
  `fileName` varchar(150) CHARACTER SET latin1 NOT NULL,
  `isFrontEnd` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`slvid`)
) ENGINE=MyISAM AUTO_INCREMENT=1650 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) CHARACTER SET latin1 NOT NULL,
  `template` text CHARACTER SET latin1 NOT NULL,
  `date` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=349 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `templates_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates_old` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) CHARACTER SET latin1 NOT NULL,
  `template` text CHARACTER SET latin1 NOT NULL,
  `date` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=122 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `test` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`test`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_accomreviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_accomreviews` (
  `tmhrid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpaid` int(10) NOT NULL,
  `review` text COLLATE utf8_unicode_ci NOT NULL,
  `tmhid` int(10) NOT NULL,
  `overallRating` tinyint(1) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`tmhrid`),
  KEY `hrvid` (`tmhrid`),
  KEY `tmhrid` (`tmhrid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_accomtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_accomtypes` (
  `tmatid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `sourceDBTable` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` int(30) NOT NULL,
  PRIMARY KEY (`tmatid`),
  KEY `tmatid` (`tmatid`),
  KEY `tmatid_2` (`tmatid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_airlinerflights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_airlinerflights` (
  `aflid` int(10) NOT NULL AUTO_INCREMENT,
  `alid` int(10) NOT NULL,
  `flyingFrom` int(10) NOT NULL,
  `flyingTo` int(10) NOT NULL,
  PRIMARY KEY (`aflid`),
  KEY `alid` (`alid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_airlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_airlines` (
  `alid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(220) NOT NULL,
  `contracted` tinyint(1) NOT NULL DEFAULT '0',
  `alias` varchar(220) DEFAULT NULL,
  `iatacode` varchar(4) DEFAULT NULL,
  `icaocode` varchar(4) DEFAULT NULL,
  `callSign` varchar(100) DEFAULT NULL,
  `coid` int(10) NOT NULL,
  `country` varchar(100) NOT NULL,
  PRIMARY KEY (`alid`)
) ENGINE=MyISAM AUTO_INCREMENT=1114 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_airports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_airports` (
  `apid` int(10) NOT NULL AUTO_INCREMENT,
  `iatacode` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `ciid` int(10) NOT NULL,
  `city` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `coid` int(10) NOT NULL,
  `isCityCode` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`apid`),
  KEY `ciid` (`ciid`,`coid`)
) ENGINE=MyISAM AUTO_INCREMENT=1981 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_airports_tocorrect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_airports_tocorrect` (
  `apid` int(10) NOT NULL AUTO_INCREMENT,
  `ciid` int(10) NOT NULL,
  `iatacode` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `coid` int(10) NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`apid`),
  KEY `ciid` (`ciid`)
) ENGINE=MyISAM AUTO_INCREMENT=1980 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_citybriefings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_citybriefings` (
  `tmcbid` int(10) NOT NULL AUTO_INCREMENT,
  `ciid` int(10) NOT NULL,
  `briefing` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(1) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(1) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`tmcbid`),
  KEY `cibid` (`tmcbid`),
  KEY `ciid` (`ciid`),
  KEY `ciid_2` (`ciid`),
  KEY `destinationcid` (`ciid`),
  KEY `tmcbid` (`tmcbid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_cityreviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_cityreviews` (
  `tmcrid` int(10) NOT NULL AUTO_INCREMENT,
  `ciid` int(10) NOT NULL,
  `tmpsid` int(10) NOT NULL,
  `review` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`tmcrid`),
  KEY `cirid` (`tmcrid`),
  KEY `ciid` (`ciid`,`tmpsid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_expenses` (
  `tmeid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpsid` int(10) NOT NULL,
  `tmetid` int(10) NOT NULL,
  `expectedAmt` float NOT NULL,
  `actualAmt` float NOT NULL,
  `currency` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `usdFxrate` float NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `paidById` int(10) NOT NULL,
  `paidBy` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`tmeid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_expenses_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_expenses_types` (
  `tmetid` smallint(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`tmetid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_flightrates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_flightrates` (
  `frid` int(11) NOT NULL AUTO_INCREMENT,
  `aflid` int(11) NOT NULL,
  `rate` float NOT NULL,
  `pricingDate` bigint(30) NOT NULL,
  `validThrough` bigint(30) NOT NULL,
  `class` varchar(1) NOT NULL,
  `isOneWay` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`frid`),
  KEY `aflid` (`aflid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
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
  `kmDistanceOffice` float DEFAULT NULL,
  `mgmtReview` text COLLATE utf8_unicode_ci,
  `isContracted` tinyint(1) NOT NULL DEFAULT '0',
  `isApproved` tinyint(1) NOT NULL DEFAULT '0',
  `avgPrice` decimal(10,0) DEFAULT NULL,
  `currency` int(30) DEFAULT NULL,
  `avgPriceNotes` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`tmhid`),
  KEY `country` (`country`,`city`)
) ENGINE=MyISAM AUTO_INCREMENT=152 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan` (
  `tmpid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `lid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `isFinalized` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tmpid`),
  KEY `lid` (`lid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plan_accomodations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_accomodations` (
  `tmpaid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpsid` int(10) NOT NULL,
  `tmhid` int(10) NOT NULL,
  `inputChecksum` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `accomType` smallint(5) NOT NULL,
  `priceNight` float NOT NULL,
  `currency` int(3) NOT NULL,
  `numNights` smallint(3) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `paidBy` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `paidById` smallint(5) DEFAULT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`tmpaid`,`tmpsid`,`tmhid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plan_finance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_finance` (
  `tmpfid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpsid` int(10) NOT NULL,
  `inputChecksum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `amount` float NOT NULL,
  `currency` int(3) NOT NULL,
  PRIMARY KEY (`tmpfid`),
  KEY `tmpsid` (`tmpsid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plan_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_segments` (
  `tmpsid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpid` int(10) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` tinyint(3) NOT NULL,
  `fromDate` bigint(30) NOT NULL,
  `toDate` bigint(30) NOT NULL,
  `reason` text COLLATE utf8_unicode_ci,
  `purpose` mediumint(10) NOT NULL,
  `originCity` int(10) NOT NULL,
  `destinationCity` int(10) NOT NULL,
  `apiFlightdata` longtext COLLATE utf8_unicode_ci,
  `isNoneBusiness` tinyint(1) NOT NULL DEFAULT '0',
  `noAccomodation` tinyint(1) NOT NULL DEFAULT '0',
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(1) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(1) NOT NULL,
  PRIMARY KEY (`tmpsid`),
  KEY `tmpid` (`tmpid`,`originCity`,`destinationCity`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plan_segpurposes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_segpurposes` (
  `tmpspid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpsid` int(10) NOT NULL,
  `purpose` int(10) NOT NULL,
  UNIQUE KEY `tmpspid` (`tmpspid`),
  UNIQUE KEY `tmpspid_2` (`tmpspid`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plan_transpclass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_transpclass` (
  `tmptc` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`tmptc`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plan_transportation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_transportation` (
  `tmptid` int(10) NOT NULL,
  `tmplid` int(10) NOT NULL,
  `category` int(11) NOT NULL,
  `createdBy` int(1) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plan_transps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_transps` (
  `tmpltid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpsid` int(10) NOT NULL,
  `tmtcid` int(10) NOT NULL,
  `inputChecksum` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `isMAin` tinyint(1) NOT NULL,
  `isUserSuggested` tinyint(1) DEFAULT '0',
  `fare` float NOT NULL,
  `currency` int(3) NOT NULL,
  `flightNumber` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `flightDetails` longtext COLLATE utf8_unicode_ci,
  `vehicleNumber` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `transpDetails` longtext COLLATE utf8_unicode_ci NOT NULL,
  `transpType` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Might need removal',
  `paidBy` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `paidById` smallint(5) DEFAULT NULL,
  `seatingDescription` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `stopDescription` mediumtext COLLATE utf8_unicode_ci,
  `isRoundTrip` tinyint(1) NOT NULL DEFAULT '0',
  `class` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` tinyint(10) NOT NULL,
  `modifiedOn` bigint(30) DEFAULT NULL,
  `modifiedBy` int(10) DEFAULT NULL,
  PRIMARY KEY (`tmpltid`,`tmpsid`,`tmtcid`),
  KEY `tmpltid` (`tmpltid`),
  KEY `tmtcid` (`tmtcid`),
  KEY `tmpltid_2` (`tmpltid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_plantrip_affient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plantrip_affient` (
  `tmpsafid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(150) NOT NULL,
  `tmpsid` int(5) NOT NULL,
  `type` varchar(30) NOT NULL,
  `primaryId` int(10) NOT NULL,
  PRIMARY KEY (`tmpsafid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_transpcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_transpcategories` (
  `tmtcid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(1) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `apiVehicleTypes` text COLLATE utf8_unicode_ci NOT NULL,
  `isAerial` tinyint(1) NOT NULL DEFAULT '0',
  `isMarine` tinyint(1) NOT NULL DEFAULT '0',
  `isLand` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tmtcid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `uom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uom` (
  `uomid` smallint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `ediCode` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `precision` tinyint(2) NOT NULL,
  PRIMARY KEY (`uomid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `usergroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usergroups` (
  `gid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `defaultModule` varchar(10) NOT NULL,
  `canAccessSystem` int(1) NOT NULL DEFAULT '0',
  `canAdminCP` int(1) NOT NULL DEFAULT '0',
  `canViewAllSupp` int(1) NOT NULL DEFAULT '0',
  `canViewAllAff` int(1) NOT NULL DEFAULT '0',
  `canViewAllCust` int(1) NOT NULL DEFAULT '0',
  `canViewAllEmp` int(1) NOT NULL DEFAULT '0',
  `canUseReporting` int(1) NOT NULL DEFAULT '0',
  `canFillReports` int(1) NOT NULL DEFAULT '0',
  `reporting_canTransFillReports` int(1) NOT NULL DEFAULT '0',
  `reporting_canSendReportsEmail` int(1) NOT NULL DEFAULT '0',
  `reporting_canViewComptInfo` tinyint(1) NOT NULL DEFAULT '0',
  `canExcludeFillStages` int(1) NOT NULL DEFAULT '0',
  `reporting_canApproveReports` int(1) NOT NULL DEFAULT '0',
  `canGenerateReports` int(1) NOT NULL DEFAULT '0',
  `canCreateReports` int(1) NOT NULL DEFAULT '0',
  `canAddUsers` int(1) NOT NULL DEFAULT '0',
  `canManageUsers` int(1) NOT NULL DEFAULT '0',
  `canViewPrivateProfile` int(1) NOT NULL DEFAULT '0',
  `canPerformMaintenance` int(1) NOT NULL DEFAULT '0',
  `canReadLogs` int(1) NOT NULL DEFAULT '0',
  `canReadStats` int(1) NOT NULL DEFAULT '0',
  `canChangeSettings` int(1) NOT NULL DEFAULT '0',
  `canManageCountries` int(1) NOT NULL DEFAULT '0',
  `canManageAffiliates` int(1) NOT NULL DEFAULT '0',
  `canManageSegments` int(1) NOT NULL DEFAULT '0',
  `canManageGenericProducts` int(1) NOT NULL DEFAULT '0',
  `canLockUnlockReports` int(1) NOT NULL DEFAULT '0',
  `canManageSuppliers` int(1) NOT NULL DEFAULT '0',
  `canManageCustomers` int(1) NOT NULL DEFAULT '0',
  `admin_canManageAllCustomers` tinyint(1) NOT NULL DEFAULT '0',
  `canManageProducts` int(1) NOT NULL DEFAULT '0',
  `canUseContents` int(1) NOT NULL DEFAULT '0',
  `contents_canManageLocations` tinyint(1) NOT NULL DEFAULT '0',
  `contents_canManageWarehouses` tinyint(1) NOT NULL DEFAULT '0',
  `canAddProducts` int(1) NOT NULL DEFAULT '0',
  `canAddSuppliers` int(1) NOT NULL DEFAULT '0',
  `canAddCustomers` int(1) NOT NULL DEFAULT '0',
  `canUseCRM` int(1) NOT NULL DEFAULT '0',
  `crm_canFillVisitReports` int(1) NOT NULL DEFAULT '0',
  `crm_canViewVisitReports` int(1) NOT NULL DEFAULT '0',
  `crm_canGenerateVisitReports` int(1) NOT NULL DEFAULT '0',
  `crm_canGenerateSalesReports` tinyint(1) NOT NULL DEFAULT '0',
  `crm_canImportCustomers` int(1) NOT NULL DEFAULT '0',
  `crm_canImportSales` tinyint(1) NOT NULL DEFAULT '0',
  `canUseAttendance` int(1) NOT NULL DEFAULT '0',
  `attendance_canRequestLeave` int(1) NOT NULL DEFAULT '0',
  `attendance_canListLeaves` int(1) NOT NULL DEFAULT '0',
  `attendance_canViewAffAllLeaves` int(1) NOT NULL DEFAULT '0',
  `attendance_canViewAllLeaves` int(1) NOT NULL DEFAULT '0',
  `attenance_canApproveLeaves` int(1) NOT NULL DEFAULT '0',
  `attenance_canApproveAllLeaves` int(1) NOT NULL DEFAULT '0',
  `attendance_canListAttendance` int(1) NOT NULL DEFAULT '0',
  `attendance_canViewAllAttendance` int(1) NOT NULL DEFAULT '0',
  `attendance_canImport` int(1) NOT NULL DEFAULT '0',
  `attendance_canGenerateReport` int(1) NOT NULL DEFAULT '0',
  `attendance_canEditAttendance` int(1) NOT NULL DEFAULT '0',
  `attendance_canGenerateExpReport` tinyint(1) NOT NULL DEFAULT '0',
  `attendace_canViewAllAffExpenses` tinyint(1) NOT NULL DEFAULT '0',
  `canUseHR` int(1) NOT NULL DEFAULT '0',
  `hr_canHrAllAffiliates` int(1) NOT NULL DEFAULT '0',
  `hr_canEditEmployee` int(1) NOT NULL DEFAULT '0',
  `hr_canManageHolidays` tinyint(1) NOT NULL DEFAULT '0',
  `canUseTravelManager` int(1) NOT NULL DEFAULT '0',
  `canUseGroupPurchase` int(1) NOT NULL DEFAULT '0',
  `grouppurchase_canPrice` int(1) NOT NULL DEFAULT '0',
  `grouppurchase_canUpdateForecast` tinyint(1) NOT NULL DEFAULT '0',
  `canUseFileSharing` int(1) NOT NULL DEFAULT '0',
  `filesharing_canViewSharedfiles` int(1) NOT NULL DEFAULT '0',
  `filesharing_canUploadFile` int(1) NOT NULL DEFAULT '0',
  `filesharing_canSendAllFiles` int(1) NOT NULL DEFAULT '0',
  `profiles_canViewEntityPrivateProfile` int(1) NOT NULL DEFAULT '0',
  `profiles_canUpdateRML` tinyint(1) NOT NULL DEFAULT '0',
  `profiles_canAddMkIntlData` tinyint(1) NOT NULL DEFAULT '0',
  `calendar_canAddPublicEvents` tinyint(1) NOT NULL DEFAULT '0',
  `calendar_canPublishEvents` tinyint(1) NOT NULL DEFAULT '0',
  `canUseReputation` int(1) NOT NULL DEFAULT '0',
  `reputation_canAddLink` int(1) NOT NULL DEFAULT '0',
  `canUseSurveys` tinyint(1) NOT NULL DEFAULT '0',
  `surveys_canCreateSurvey` tinyint(1) NOT NULL DEFAULT '0',
  `canUseDevelopment` tinyint(1) NOT NULL DEFAULT '0',
  `development_canCreateReq` tinyint(1) NOT NULL DEFAULT '0',
  `admin_canModifyLangFiles` tinyint(1) NOT NULL DEFAULT '0',
  `admin_canCreateLangFiles` tinyint(1) NOT NULL DEFAULT '0',
  `canUseSourcing` tinyint(1) NOT NULL DEFAULT '0',
  `sourcing_canManageEntries` tinyint(1) NOT NULL DEFAULT '0',
  `sourcing_canListSuppliers` tinyint(1) NOT NULL DEFAULT '0',
  `sourcing_canViewKPIs` tinyint(1) NOT NULL DEFAULT '0',
  `canUseAssets` tinyint(1) NOT NULL DEFAULT '0',
  `assets_canManageAssets` tinyint(1) NOT NULL DEFAULT '0',
  `canUseBudgeting` tinyint(1) NOT NULL DEFAULT '0',
  `budgeting_canFillBudget` tinyint(1) NOT NULL DEFAULT '0',
  `budgeting_canFillComAdmExp` tinyint(1) NOT NULL DEFAULT '0',
  `budgeting_canFillFinBudgets` tinyint(1) NOT NULL DEFAULT '0',
  `budgeting_canFillLocalIncome` tinyint(1) NOT NULL DEFAULT '0',
  `budgeting_canMassUpdate` tinyint(1) NOT NULL DEFAULT '0',
  `canUseMeetings` tinyint(1) NOT NULL DEFAULT '0',
  `meetings_canViewAllMeetings` tinyint(1) NOT NULL DEFAULT '0',
  `meetings_canCreateMeeting` tinyint(1) NOT NULL DEFAULT '0',
  `cms_canAddNews` tinyint(1) NOT NULL DEFAULT '0',
  `cms_canPublishNews` tinyint(1) NOT NULL,
  `profiles_canViewContractInfo` tinyint(1) NOT NULL DEFAULT '0',
  `canUseFinance` tinyint(1) NOT NULL DEFAULT '0',
  `canUseCms` tinyint(1) NOT NULL DEFAULT '0',
  `aro_canManageApprovalPolicies` tinyint(1) NOT NULL DEFAULT '0',
  `aro_canManagePolicies` tinyint(1) NOT NULL DEFAULT '0',
  `aro_canManageWarehousePolicies` tinyint(1) NOT NULL DEFAULT '0',
  `canUseAro` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `userhrinformation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userhrinformation` (
  `uhrid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `employeeNum` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `birthDate` bigint(30) DEFAULT NULL,
  `birthPlace` varchar(200) NOT NULL,
  `nationality` int(10) unsigned NOT NULL,
  `religiousViews` tinyint(1) unsigned NOT NULL,
  `maritalStatus` tinyint(1) unsigned NOT NULL,
  `hasChildren` tinyint(1) unsigned NOT NULL,
  `passportInfo` text NOT NULL,
  `empClassification` tinyint(1) unsigned NOT NULL,
  `jobDescription` text NOT NULL,
  `joinDate` bigint(30) NOT NULL,
  `firstJobDate` bigint(30) DEFAULT NULL,
  `leaveDate` bigint(30) NOT NULL,
  `noticePeriod` tinyint(1) unsigned NOT NULL,
  `salary` varbinary(220) NOT NULL,
  `salaryKey` varchar(10) NOT NULL,
  `paymentMethod` tinyint(1) unsigned NOT NULL,
  `bankName` varchar(220) NOT NULL,
  `bankBranch` varchar(200) NOT NULL,
  `bankAccountNumber` int(20) NOT NULL,
  `iban` varchar(50) NOT NULL,
  `taxInfo` int(10) NOT NULL,
  `socialSecurityNumber` varchar(10) NOT NULL,
  `managementComments` text NOT NULL,
  PRIMARY KEY (`uhrid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=328 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(120) NOT NULL,
  `password` varchar(120) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `loginKey` varchar(40) NOT NULL,
  `lastPasswordChange` bigint(30) NOT NULL DEFAULT '0',
  `gid` smallint(5) unsigned NOT NULL DEFAULT '3',
  `lastVisit` bigint(30) unsigned NOT NULL DEFAULT '0',
  `dateAdded` bigint(30) NOT NULL,
  `failedLoginAttempts` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `lastAttemptTime` bigint(30) NOT NULL DEFAULT '0',
  `defaultModule` varchar(20) NOT NULL,
  `language` varchar(50) NOT NULL,
  `firstName` varchar(150) NOT NULL,
  `middleName` varchar(150) NOT NULL,
  `lastName` varchar(200) NOT NULL,
  `displayName` varchar(220) NOT NULL,
  `reportsTo` int(10) unsigned NOT NULL,
  `assistant` int(10) unsigned NOT NULL,
  `country` int(10) unsigned NOT NULL,
  `city` varchar(100) NOT NULL,
  `addressLine1` varchar(200) NOT NULL,
  `addressLine2` varchar(150) DEFAULT NULL,
  `postCode` int(6) NOT NULL,
  `building` varchar(100) DEFAULT NULL,
  `email` varchar(220) NOT NULL,
  `skype` varchar(200) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `telephoneExtension` smallint(4) DEFAULT NULL,
  `telephone2` varchar(20) DEFAULT NULL,
  `telephone2Extension` smallint(4) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `mobileIsPrivate` tinyint(1) NOT NULL DEFAULT '0',
  `mobile2` varchar(20) DEFAULT NULL,
  `mobile2IsPrivate` tinyint(1) NOT NULL DEFAULT '0',
  `internalExtension` smallint(4) DEFAULT NULL,
  `bbPin` varchar(8) DEFAULT NULL,
  `poBox` int(10) DEFAULT NULL,
  `profilePicture` varchar(200) DEFAULT NULL,
  `newFilesNotification` tinyint(1) NOT NULL DEFAULT '0',
  `timeZone` varchar(200) DEFAULT NULL,
  `integrationOBId` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=354 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_passwordarchive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_passwordarchive` (
  `upaid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `password` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `archiveTime` bigint(30) NOT NULL,
  PRIMARY KEY (`upaid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=990 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_usergroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_usergroups` (
  `ugid` int(10) NOT NULL AUTO_INCREMENT,
  `gid` smallint(5) NOT NULL,
  `uid` int(10) NOT NULL,
  `isMain` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ugid`,`gid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=392 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `usersemails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usersemails` (
  `ueid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `email` varchar(220) NOT NULL,
  PRIMARY KEY (`ueid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `userspositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userspositions` (
  `upid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `posid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`upid`,`uid`,`posid`),
  KEY `uid` (`uid`,`posid`)
) ENGINE=MyISAM AUTO_INCREMENT=2553 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visitreports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitreports` (
  `vrid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `location` int(10) DEFAULT NULL,
  `rpid` int(10) unsigned NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  `date` bigint(30) NOT NULL,
  `type` int(1) NOT NULL,
  `purpose` int(1) NOT NULL,
  `hasSupplier` tinyint(1) NOT NULL DEFAULT '1',
  `supplyStatus` int(1) NOT NULL,
  `availabilityIssues` int(1) NOT NULL,
  `currentMktShare` int(1) NOT NULL,
  `isLocked` int(1) NOT NULL DEFAULT '1',
  `finishDate` bigint(30) NOT NULL,
  `isDraft` tinyint(1) NOT NULL DEFAULT '0',
  `lid` int(10) DEFAULT NULL,
  PRIMARY KEY (`vrid`),
  KEY `uid` (`uid`,`cid`,`rpid`),
  KEY `affid` (`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=8012 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visitreports_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitreports_comments` (
  `vrcmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vrid` int(10) unsigned NOT NULL,
  `spid` int(10) unsigned NOT NULL,
  `competitionInfo` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `rumorsCompetitors` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ownRumors` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `productsDiscussed` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `offersMade` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `newProjCustomer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `opportunitiesCustomer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `conclusions` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `followUp` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`vrcmid`),
  KEY `vrid` (`vrid`),
  KEY `spid` (`spid`)
) ENGINE=MyISAM AUTO_INCREMENT=8118 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visitreports_competition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitreports_competition` (
  `vrcid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vrid` int(10) unsigned NOT NULL,
  `competitorName` varchar(220) NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `aggressionLevel` int(1) NOT NULL,
  `recentPrice` float NOT NULL,
  `ourRecentPrice` float NOT NULL,
  `supplyStatus` int(1) NOT NULL,
  `availabilityIssues` int(1) NOT NULL,
  PRIMARY KEY (`vrcid`),
  KEY `vrid` (`vrid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visitreports_productlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitreports_productlines` (
  `plid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vrid` int(10) unsigned NOT NULL,
  `productLine` int(10) unsigned NOT NULL,
  PRIMARY KEY (`plid`,`vrid`,`productLine`),
  KEY `vrid` (`vrid`,`productLine`)
) ENGINE=MyISAM AUTO_INCREMENT=6622 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visitreports_reportsuppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitreports_reportsuppliers` (
  `rsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vrid` int(10) unsigned NOT NULL,
  `spid` int(10) unsigned NOT NULL,
  `sprid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`rsid`,`vrid`,`spid`),
  KEY `vrid` (`vrid`,`spid`,`sprid`)
) ENGINE=MyISAM AUTO_INCREMENT=8119 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouses` (
  `wid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `name` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine1` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine2` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `postalCode` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `ciid` int(10) NOT NULL,
  `coid` int(10) NOT NULL,
  `geoLocation` point DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `integrationOBId` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`wid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `website_affiliates`;
/*!50001 DROP VIEW IF EXISTS `website_affiliates`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `website_affiliates` (
  `affid` tinyint NOT NULL,
  `brandingColor` tinyint NOT NULL,
  `alias` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `legalName` tinyint NOT NULL,
  `generalManager` tinyint NOT NULL,
  `supervisor` tinyint NOT NULL,
  `hrManager` tinyint NOT NULL,
  `finManager` tinyint NOT NULL,
  `mailingList` tinyint NOT NULL,
  `altMailingList` tinyint NOT NULL,
  `vacanciesEmail` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `country` tinyint NOT NULL,
  `city` tinyint NOT NULL,
  `postCode` tinyint NOT NULL,
  `addressLine1` tinyint NOT NULL,
  `addressLine2` tinyint NOT NULL,
  `floor` tinyint NOT NULL,
  `geoLocation` tinyint NOT NULL,
  `phone1` tinyint NOT NULL,
  `phone2` tinyint NOT NULL,
  `fax` tinyint NOT NULL,
  `poBox` tinyint NOT NULL,
  `mainEmail` tinyint NOT NULL,
  `website` tinyint NOT NULL,
  `qrAlwaysCopy` tinyint NOT NULL,
  `vrAlwaysNotify` tinyint NOT NULL,
  `defaultWorkshift` tinyint NOT NULL,
  `integrationOBOrgId` tinyint NOT NULL,
  `defaultLang` tinyint NOT NULL,
  `mainCurrency` tinyint NOT NULL,
  `publishOnWebsite` tinyint NOT NULL,
  `isIntReinvoiceAffiliate` tinyint NOT NULL,
  `isActive` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `website_calendar_events`;
/*!50001 DROP VIEW IF EXISTS `website_calendar_events`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `website_calendar_events` (
  `ceid` tinyint NOT NULL,
  `identifier` tinyint NOT NULL,
  `alias` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `fromDate` tinyint NOT NULL,
  `toDate` tinyint NOT NULL,
  `place` tinyint NOT NULL,
  `boothNum` tinyint NOT NULL,
  `tags` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `logo` tinyint NOT NULL,
  `refreshLogoOnWebsite` tinyint NOT NULL,
  `affid` tinyint NOT NULL,
  `spid` tinyint NOT NULL,
  `uid` tinyint NOT NULL,
  `isPublic` tinyint NOT NULL,
  `isFeatured` tinyint NOT NULL,
  `isCreatedFromCMS` tinyint NOT NULL,
  `publishOnWebsite` tinyint NOT NULL,
  `createdBy` tinyint NOT NULL,
  `createdOn` tinyint NOT NULL,
  `editedBy` tinyint NOT NULL,
  `editedOn` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `website_chemicalfunctions`;
/*!50001 DROP VIEW IF EXISTS `website_chemicalfunctions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `website_chemicalfunctions` (
  `cfid` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `publishOnWebsite` tinyint NOT NULL,
  `createdBy` tinyint NOT NULL,
  `createdOn` tinyint NOT NULL,
  `modifiedBy` tinyint NOT NULL,
  `modifiedOn` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `website_cms_news`;
/*!50001 DROP VIEW IF EXISTS `website_cms_news`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `website_cms_news` (
  `cmsnid` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `alias` tinyint NOT NULL,
  `version` tinyint NOT NULL,
  `summary` tinyint NOT NULL,
  `publishDate` tinyint NOT NULL,
  `isPublished` tinyint NOT NULL,
  `isFeatured` tinyint NOT NULL,
  `lang` tinyint NOT NULL,
  `bodyText` tinyint NOT NULL,
  `tags` tinyint NOT NULL,
  `metaDesc` tinyint NOT NULL,
  `metaKeywords` tinyint NOT NULL,
  `robotsRule` tinyint NOT NULL,
  `createdBy` tinyint NOT NULL,
  `createDate` tinyint NOT NULL,
  `modifiedBy` tinyint NOT NULL,
  `modifyDate` tinyint NOT NULL,
  `hits` tinyint NOT NULL,
  `uploadedImages` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `website_cms_pages`;
/*!50001 DROP VIEW IF EXISTS `website_cms_pages`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `website_cms_pages` (
  `cmspid` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `alias` tinyint NOT NULL,
  `version` tinyint NOT NULL,
  `publishDate` tinyint NOT NULL,
  `isPublished` tinyint NOT NULL,
  `category` tinyint NOT NULL,
  `lang` tinyint NOT NULL,
  `bodyText` tinyint NOT NULL,
  `pageHeaderBkg` tinyint NOT NULL,
  `metaDesc` tinyint NOT NULL,
  `metaKeywords` tinyint NOT NULL,
  `robotsRule` tinyint NOT NULL,
  `createdBy` tinyint NOT NULL,
  `dateCreated` tinyint NOT NULL,
  `modifiedBy` tinyint NOT NULL,
  `dateModified` tinyint NOT NULL,
  `hits` tinyint NOT NULL,
  `uploadedImages` tinyint NOT NULL,
  `tags` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `website_events`;
/*!50001 DROP VIEW IF EXISTS `website_events`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `website_events` (
  `ceid` tinyint NOT NULL,
  `identifier` tinyint NOT NULL,
  `alias` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `fromDate` tinyint NOT NULL,
  `toDate` tinyint NOT NULL,
  `place` tinyint NOT NULL,
  `boothNum` tinyint NOT NULL,
  `tags` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `logo` tinyint NOT NULL,
  `refreshLogoOnWebsite` tinyint NOT NULL,
  `affid` tinyint NOT NULL,
  `spid` tinyint NOT NULL,
  `uid` tinyint NOT NULL,
  `isPublic` tinyint NOT NULL,
  `isFeatured` tinyint NOT NULL,
  `publishOnWebsite` tinyint NOT NULL,
  `createdBy` tinyint NOT NULL,
  `createdOn` tinyint NOT NULL,
  `editedBy` tinyint NOT NULL,
  `editedOn` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `website_productsegments`;
/*!50001 DROP VIEW IF EXISTS `website_productsegments`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `website_productsegments` (
  `psid` tinyint NOT NULL,
  `alias` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `titleSoundex` tinyint NOT NULL,
  `titleAbbr` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `shortDescription` tinyint NOT NULL,
  `category` tinyint NOT NULL,
  `publishOnWebsite` tinyint NOT NULL,
  `smallBanner` tinyint NOT NULL,
  `mediumBanner` tinyint NOT NULL,
  `largeBanner` tinyint NOT NULL,
  `slogan` tinyint NOT NULL,
  `brandingColor` tinyint NOT NULL,
  `displaySequence` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `website_segmentapplications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_segmentapplications` (
  `psaid` int(10) DEFAULT NULL,
  `psid` smallint(5) DEFAULT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `publishOnWebsite` tinyint(1) DEFAULT NULL,
  `sequence` int(1) DEFAULT NULL,
  `createdBy` int(10) DEFAULT NULL,
  `createdOn` bigint(30) DEFAULT NULL,
  `modifiedBy` int(10) DEFAULT NULL,
  `modifiedOn` int(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `workshifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workshifts` (
  `wsid` smallint(10) NOT NULL AUTO_INCREMENT,
  `onDutyHour` int(2) unsigned NOT NULL,
  `onDutyMinutes` varchar(2) NOT NULL DEFAULT '00',
  `offDutyHour` int(2) unsigned NOT NULL,
  `offDutyMinutes` varchar(2) NOT NULL DEFAULT '00',
  `weekDays` text NOT NULL,
  PRIMARY KEY (`wsid`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP TABLE IF EXISTS `website_affiliates`*/;
/*!50001 DROP VIEW IF EXISTS `website_affiliates`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `website_affiliates` AS select `affiliates`.`affid` AS `affid`,`affiliates`.`brandingColor` AS `brandingColor`,`affiliates`.`alias` AS `alias`,`affiliates`.`name` AS `name`,`affiliates`.`legalName` AS `legalName`,`affiliates`.`generalManager` AS `generalManager`,`affiliates`.`supervisor` AS `supervisor`,`affiliates`.`hrManager` AS `hrManager`,`affiliates`.`finManager` AS `finManager`,`affiliates`.`mailingList` AS `mailingList`,`affiliates`.`altMailingList` AS `altMailingList`,`affiliates`.`vacanciesEmail` AS `vacanciesEmail`,`affiliates`.`description` AS `description`,`affiliates`.`country` AS `country`,`affiliates`.`city` AS `city`,`affiliates`.`postCode` AS `postCode`,`affiliates`.`addressLine1` AS `addressLine1`,`affiliates`.`addressLine2` AS `addressLine2`,`affiliates`.`floor` AS `floor`,`affiliates`.`geoLocation` AS `geoLocation`,`affiliates`.`phone1` AS `phone1`,`affiliates`.`phone2` AS `phone2`,`affiliates`.`fax` AS `fax`,`affiliates`.`poBox` AS `poBox`,`affiliates`.`mainEmail` AS `mainEmail`,`affiliates`.`website` AS `website`,`affiliates`.`qrAlwaysCopy` AS `qrAlwaysCopy`,`affiliates`.`vrAlwaysNotify` AS `vrAlwaysNotify`,`affiliates`.`defaultWorkshift` AS `defaultWorkshift`,`affiliates`.`integrationOBOrgId` AS `integrationOBOrgId`,`affiliates`.`defaultLang` AS `defaultLang`,`affiliates`.`mainCurrency` AS `mainCurrency`,`affiliates`.`publishOnWebsite` AS `publishOnWebsite`,`affiliates`.`isIntReinvoiceAffiliate` AS `isIntReinvoiceAffiliate`,`affiliates`.`isActive` AS `isActive` from `affiliates` where (`affiliates`.`publishOnWebsite` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `website_calendar_events`*/;
/*!50001 DROP VIEW IF EXISTS `website_calendar_events`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `website_calendar_events` AS select `calendar_events`.`ceid` AS `ceid`,`calendar_events`.`identifier` AS `identifier`,`calendar_events`.`alias` AS `alias`,`calendar_events`.`title` AS `title`,`calendar_events`.`description` AS `description`,`calendar_events`.`fromDate` AS `fromDate`,`calendar_events`.`toDate` AS `toDate`,`calendar_events`.`place` AS `place`,`calendar_events`.`boothNum` AS `boothNum`,`calendar_events`.`tags` AS `tags`,`calendar_events`.`type` AS `type`,`calendar_events`.`logo` AS `logo`,`calendar_events`.`refreshLogoOnWebsite` AS `refreshLogoOnWebsite`,`calendar_events`.`affid` AS `affid`,`calendar_events`.`spid` AS `spid`,`calendar_events`.`uid` AS `uid`,`calendar_events`.`isPublic` AS `isPublic`,`calendar_events`.`isFeatured` AS `isFeatured`,`calendar_events`.`isCreatedFromCMS` AS `isCreatedFromCMS`,`calendar_events`.`publishOnWebsite` AS `publishOnWebsite`,`calendar_events`.`createdBy` AS `createdBy`,`calendar_events`.`createdOn` AS `createdOn`,`calendar_events`.`editedBy` AS `editedBy`,`calendar_events`.`editedOn` AS `editedOn` from `calendar_events` where (`calendar_events`.`publishOnWebsite` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `website_chemicalfunctions`*/;
/*!50001 DROP VIEW IF EXISTS `website_chemicalfunctions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `website_chemicalfunctions` AS select `chemicalfunctions`.`cfid` AS `cfid`,`chemicalfunctions`.`name` AS `name`,`chemicalfunctions`.`title` AS `title`,`chemicalfunctions`.`description` AS `description`,`chemicalfunctions`.`publishOnWebsite` AS `publishOnWebsite`,`chemicalfunctions`.`createdBy` AS `createdBy`,`chemicalfunctions`.`createdOn` AS `createdOn`,`chemicalfunctions`.`modifiedBy` AS `modifiedBy`,`chemicalfunctions`.`modifiedOn` AS `modifiedOn` from `chemicalfunctions` where (`chemicalfunctions`.`publishOnWebsite` = 1) */
/*!50002 WITH LOCAL CHECK OPTION */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `website_cms_news`*/;
/*!50001 DROP VIEW IF EXISTS `website_cms_news`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `website_cms_news` AS select `cms_news`.`cmsnid` AS `cmsnid`,`cms_news`.`title` AS `title`,`cms_news`.`alias` AS `alias`,`cms_news`.`version` AS `version`,`cms_news`.`summary` AS `summary`,`cms_news`.`publishDate` AS `publishDate`,`cms_news`.`isPublished` AS `isPublished`,`cms_news`.`isFeatured` AS `isFeatured`,`cms_news`.`lang` AS `lang`,`cms_news`.`bodyText` AS `bodyText`,`cms_news`.`tags` AS `tags`,`cms_news`.`metaDesc` AS `metaDesc`,`cms_news`.`metaKeywords` AS `metaKeywords`,`cms_news`.`robotsRule` AS `robotsRule`,`cms_news`.`createdBy` AS `createdBy`,`cms_news`.`createDate` AS `createDate`,`cms_news`.`modifiedBy` AS `modifiedBy`,`cms_news`.`modifyDate` AS `modifyDate`,`cms_news`.`hits` AS `hits`,`cms_news`.`uploadedImages` AS `uploadedImages` from `cms_news` where (`cms_news`.`isPublished` = 1) */
/*!50002 WITH LOCAL CHECK OPTION */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `website_cms_pages`*/;
/*!50001 DROP VIEW IF EXISTS `website_cms_pages`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=TEMPTABLE */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `website_cms_pages` AS select `cms_pages`.`cmspid` AS `cmspid`,`cms_pages`.`title` AS `title`,`cms_pages`.`alias` AS `alias`,`cms_pages`.`version` AS `version`,`cms_pages`.`publishDate` AS `publishDate`,`cms_pages`.`isPublished` AS `isPublished`,`cms_pages`.`category` AS `category`,`cms_pages`.`lang` AS `lang`,`cms_pages`.`bodyText` AS `bodyText`,`cms_pages`.`pageHeaderBkg` AS `pageHeaderBkg`,`cms_pages`.`metaDesc` AS `metaDesc`,`cms_pages`.`metaKeywords` AS `metaKeywords`,`cms_pages`.`robotsRule` AS `robotsRule`,`cms_pages`.`createdBy` AS `createdBy`,`cms_pages`.`dateCreated` AS `dateCreated`,`cms_pages`.`modifiedBy` AS `modifiedBy`,`cms_pages`.`dateModified` AS `dateModified`,`cms_pages`.`hits` AS `hits`,`cms_pages`.`uploadedImages` AS `uploadedImages`,`cms_pages`.`tags` AS `tags` from `cms_pages` where (`cms_pages`.`isPublished` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `website_events`*/;
/*!50001 DROP VIEW IF EXISTS `website_events`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `website_events` AS select `calendar_events`.`ceid` AS `ceid`,`calendar_events`.`identifier` AS `identifier`,`calendar_events`.`alias` AS `alias`,`calendar_events`.`title` AS `title`,`calendar_events`.`description` AS `description`,`calendar_events`.`fromDate` AS `fromDate`,`calendar_events`.`toDate` AS `toDate`,`calendar_events`.`place` AS `place`,`calendar_events`.`boothNum` AS `boothNum`,`calendar_events`.`tags` AS `tags`,`calendar_events`.`type` AS `type`,`calendar_events`.`logo` AS `logo`,`calendar_events`.`refreshLogoOnWebsite` AS `refreshLogoOnWebsite`,`calendar_events`.`affid` AS `affid`,`calendar_events`.`spid` AS `spid`,`calendar_events`.`uid` AS `uid`,`calendar_events`.`isPublic` AS `isPublic`,`calendar_events`.`isFeatured` AS `isFeatured`,`calendar_events`.`publishOnWebsite` AS `publishOnWebsite`,`calendar_events`.`createdBy` AS `createdBy`,`calendar_events`.`createdOn` AS `createdOn`,`calendar_events`.`editedBy` AS `editedBy`,`calendar_events`.`editedOn` AS `editedOn` from `calendar_events` where (`calendar_events`.`publishOnWebsite` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `website_productsegments`*/;
/*!50001 DROP VIEW IF EXISTS `website_productsegments`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `website_productsegments` AS select `productsegments`.`psid` AS `psid`,`productsegments`.`alias` AS `alias`,`productsegments`.`title` AS `title`,`productsegments`.`titleSoundex` AS `titleSoundex`,`productsegments`.`titleAbbr` AS `titleAbbr`,`productsegments`.`description` AS `description`,`productsegments`.`shortDescription` AS `shortDescription`,`productsegments`.`category` AS `category`,`productsegments`.`publishOnWebsite` AS `publishOnWebsite`,`productsegments`.`smallBanner` AS `smallBanner`,`productsegments`.`mediumBanner` AS `mediumBanner`,`productsegments`.`largeBanner` AS `largeBanner`,`productsegments`.`slogan` AS `slogan`,`productsegments`.`brandingColor` AS `brandingColor`,`productsegments`.`displaySequence` AS `displaySequence` from `productsegments` where (`productsegments`.`publishOnWebsite` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

