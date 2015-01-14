
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
) ENGINE=MyISAM AUTO_INCREMENT=4077 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=16612 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliates` (
  `affid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `isIntReinvoiceAffiliate` tinyint(1) NOT NULL DEFAULT '0',
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
  `integrationOBOrgId` varchar(32) NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL,
  `defaultLang` varchar(180) NOT NULL,
  `alias` varchar(150) NOT NULL,
  `vacanciesEmail` varchar(220) NOT NULL,
  `mainCurrency` int(3) DEFAULT NULL,
  PRIMARY KEY (`affid`),
  KEY `name` (`name`),
  KEY `generalManager` (`generalManager`,`supervisor`,`hrManager`),
  KEY `country` (`country`),
  KEY `defaultWorkshift` (`defaultWorkshift`),
  KEY `geoLocation` (`geoLocation`(25)),
  KEY `finManager` (`finManager`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=62846 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=22986 DEFAULT CHARSET=latin1;
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
  `approvedOn` bigint(30) DEFAULT NULL,
  `requestedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`adid`),
  KEY `uid` (`uid`),
  KEY `addedBy` (`addedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=273 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance_leaveexptypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_leaveexptypes` (
  `aletid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=811 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=242 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_budgets_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_budgets_lines` (
  `blid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `bid` int(10) unsigned NOT NULL,
  `cid` int(10) NOT NULL,
  `altCid` varchar(50) NOT NULL,
  `prevblid` int(10) DEFAULT NULL,
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
  `interCompanypurchase` int(10) NOT NULL,
  `quantity` float NOT NULL,
  `createdBy` int(10) NOT NULL DEFAULT '0',
  `modifiedBy` int(10) NOT NULL,
  `originalCurrency` int(11) DEFAULT NULL,
  `saleType` varchar(12) NOT NULL,
  `s1Perc` float NOT NULL,
  `s2Perc` float NOT NULL,
  `inputChecksum` varchar(200) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`blid`),
  KEY `createdBy` (`createdBy`),
  KEY `businessMgr` (`businessMgr`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
CREATE TABLE `budgeting_fxrates` (
  `bfxid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `year` smallint(5) NOT NULL,
  `fromCurrency` int(3) NOT NULL,
  `toCurrency` int(3) NOT NULL,
  `rate` float NOT NULL,
  `isActual` tinyint(1) NOT NULL,
  `isYef` tinyint(1) NOT NULL,
  `isBudget` tinyint(1) NOT NULL,
  PRIMARY KEY (`bfxid`),
  KEY `affid` (`affid`,`fromCurrency`,`toCurrency`)
/*!40101 SET character_set_client = @saved_cs_client */;
CREATE TABLE `budgeting_investcategory` (
  `bicid` int(7) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(20) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(20) NOT NULL,
  PRIMARY KEY (`bicid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_investexpenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_investexpenses` (
  `biid` int(7) NOT NULL AUTO_INCREMENT,
  `bfbid` int(10) NOT NULL,
  `biiid` int(7) NOT NULL,
  `budgetPrevYear` decimal(10,0) NOT NULL,
  `yefPrevYear` decimal(10,0) NOT NULL,
  `percVariation` decimal(10,0) NOT NULL,
  `budgetCurrent` decimal(10,0) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(10) NOT NULL,
  `modifiedOn` bigint(7) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`biid`),
  UNIQUE KEY `biid` (`biid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_investitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_investitems` (
  `biiid` int(7) NOT NULL AUTO_INCREMENT,
  `bicid` int(7) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(7) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(7) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`biiid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgeting_financialbudget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_financialbudget` (
  `bfbid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `year` int(10) NOT NULL,
  `currency` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `finGenAdmExpAmtApty` float NOT NULL,
  `finGenAdmExpAmtBpy` float NOT NULL,
  `finGenAdmExpAmtYpy` float NOT NULL,
  `finGenAdmExpAmtCurrent` float NOT NULL,
  `isFinalized` tinyint(1) NOT NULL,
  `finalizedBy` int(10) NOT NULL,
CREATE TABLE `budgeting_plcategory` (
  `bplcid` int(10) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(20) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(20) NOT NULL,
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
  `actualPrevThreeYears` float NOT NULL,
  `actualPrevTwoYears` float NOT NULL,
  `budgetPrevYear` float NOT NULL,
  `yefPrevYear` float NOT NULL,
  `budgetCurrent` float NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`bpleid`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_fxrates` (
  `bfxid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `year` smallint(5) NOT NULL,
  `fromCurrency` int(3) NOT NULL,
  `toCurrency` int(3) NOT NULL,
  `rate` float NOT NULL,
  `isActual` tinyint(1) NOT NULL,
  PRIMARY KEY (`bfxid`),
  KEY `affid` (`affid`,`fromCurrency`,`toCurrency`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS `budgeting_trainingvisits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_trainingvisits` (
  `btvid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `bfbid` int(10) NOT NULL,
  `lid` int(10) NOT NULL,
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
DROP TABLE IF EXISTS `calendar_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_events` (
  `ceid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) NOT NULL,
  `title` varchar(220) NOT NULL,
  `description` text NOT NULL,
  `fromDate` bigint(30) NOT NULL,
  `toDate` bigint(30) NOT NULL,
  `place` varchar(300) DEFAULT NULL,
  `type` smallint(1) NOT NULL,
  `affid` smallint(5) DEFAULT NULL,
  `spid` int(10) DEFAULT NULL,
  `uid` int(10) NOT NULL,
  `isPublic` tinyint(1) NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `editedBy` int(10) DEFAULT NULL,
  `editedOn` bigint(30) DEFAULT NULL,
  `logo` varchar(220) NOT NULL,
  PRIMARY KEY (`ceid`),
  KEY `uid` (`uid`),
  KEY `createdBy` (`createdBy`,`editedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=129 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;
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
  `timeStarted` bigint(30) DEFAULT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=1734 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`cfcid`,`safid`,`csid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chemicalfunctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chemicalfunctions` (
  `cfid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`cfid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chemicalsubstances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chemicalsubstances` (
  `csid` int(10) NOT NULL AUTO_INCREMENT,
  `casNum` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `synonyms` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`csid`)
) ENGINE=MyISAM AUTO_INCREMENT=6063 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `ciid` int(10) NOT NULL AUTO_INCREMENT,
  `coid` smallint(5) NOT NULL,
  `country` varchar(10) NOT NULL,
  `unlocode` varchar(4) NOT NULL,
  `oudeloc` varchar(5) DEFAULT NULL,
  `name` varchar(220) NOT NULL,
  `geoLocationText` varchar(50) DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `defaultAirport` int(10) DEFAULT NULL,
  PRIMARY KEY (`ciid`),
  KEY `coid` (`coid`),
  KEY `defaultAirport` (`defaultAirport`)
) ENGINE=MyISAM AUTO_INCREMENT=87762 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `coid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) unsigned NOT NULL,
  `name` varchar(220) NOT NULL,
  `acronym` varchar(10) NOT NULL,
  `mainCurrency` int(3) DEFAULT NULL,
  PRIMARY KEY (`coid`),
  KEY `affid` (`affid`)
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
) ENGINE=MyISAM AUTO_INCREMENT=86724 DEFAULT CHARSET=latin1;
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
  `isCompleted` tinyint(1) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `requestedBy` int(10) NOT NULL,
  `dateRequested` bigint(30) NOT NULL,
  `approvedBy` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  PRIMARY KEY (`drcid`),
  KEY `identifier` (`outcomeReq`,`requestedBy`,`approvedBy`,`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=6912 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employeesshifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employeesshifts` (
  `esid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `wsid` smallint(10) unsigned NOT NULL,
  `fromDate` bigint(30) DEFAULT NULL,
  `toDate` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`esid`,`uid`,`wsid`)
) ENGINE=MyISAM AUTO_INCREMENT=1386 DEFAULT CHARSET=latin1;
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
  PRIMARY KEY (`eptid`),
  KEY `createdBy` (`createdBy`,`modifiedOn`),
  KEY `psaid` (`psaid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities` (
  `eid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `companyName` varchar(250) DEFAULT NULL,
  `companyNameShort` varchar(20) DEFAULT NULL,
  `supplierType` varchar(5) NOT NULL,
  `companyNameAbbr` varchar(10) DEFAULT NULL,
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
  `approved` smallint(1) unsigned NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `dateAdded` bigint(30) unsigned NOT NULL,
  `notes` text,
  `type` char(2) NOT NULL,
  `contractFirstSigDate` bigint(30) DEFAULT NULL,
  `contractExpiryDate` bigint(30) DEFAULT NULL,
  `contractIsEvergreen` tinyint(1) DEFAULT NULL,
  `contractPriorNotice` smallint(2) DEFAULT NULL,
  `mainSupplyLine` varchar(200) NOT NULL,
  `supplierSince` bigint(30) NOT NULL,
  `relationMaturity` tinyint(2) DEFAULT NULL,
  `trustLevel` int(3) DEFAULT NULL,
  `noQReportReq` tinyint(1) NOT NULL DEFAULT '0',
  `reqQRSummary` tinyint(1) NOT NULL DEFAULT '0',
  `noQReportSend` tinyint(1) NOT NULL DEFAULT '0',
  `customerSince` bigint(30) NOT NULL,
  `loyalty` int(2) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL,
  `isCentralPurchase` tinyint(1) NOT NULL,
  `CentralPurchaseNote` varchar(100) NOT NULL,
  `companySize` varchar(200) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) DEFAULT NULL,
  `modifiedOn` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`eid`),
  KEY `createBy` (`createdBy`,`modifiedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=4938 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `entities_contractcountries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities_contractcountries` (
  `eccid` int(10) NOT NULL AUTO_INCREMENT,
  `eid` int(10) NOT NULL,
  `coid` int(10) NOT NULL,
  `isExclusive` tinyint(1) NOT NULL,
  `selectiveProducts` tinyint(1) NOT NULL,
  `isAgent` tinyint(1) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `isDistributor` tinyint(1) NOT NULL,
  `Exclusivity` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`eccid`),
  UNIQUE KEY `eccid` (`eccid`),
  KEY `eccid_2` (`eccid`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entities_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities_locations` (
  `eloid` int(10) NOT NULL AUTO_INCREMENT,
  `inputChecksum` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `eid` int(10) NOT NULL,
  `location` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `coid` int(10) NOT NULL,
  `ciid` int(10) NOT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `buildingName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `postcode` int(50) NOT NULL,
  `geoLocation` point NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` int(11) NOT NULL,
  `isMain` tinyint(1) NOT NULL,
  PRIMARY KEY (`eloid`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entitiesbrandsproducts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitiesbrandsproducts` (
  `ebpid` int(10) NOT NULL AUTO_INCREMENT,
  `ebid` int(10) NOT NULL,
  `eptid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`ebpid`,`ebid`,`eptid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  KEY `eid` (`eid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=6782 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=891 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=12757 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=891 DEFAULT CHARSET=latin1;
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
CREATE TABLE `goruppurchase_forecast` (
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
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grouppurchase_forecastlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grouppurchase_forecastlines` (
  `gpflid` int(10) NOT NULL AUTO_INCREMENT,
  `gpfid` int(10) NOT NULL,
  `inputChecksum` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pid` int(10) NOT NULL,
  `psid` int(10) NOT NULL,
  `saleType` smallint(10) NOT NULL,
  `businessMgr` int(10) NOT NULL,
  `month1` float NOT NULL,
  `month2` float NOT NULL,
  `month3` float NOT NULL,
  `month4` float NOT NULL,
  `month5` float NOT NULL,
  `month6` float NOT NULL,
  `month7` float NOT NULL,
  `month8` float NOT NULL,
  `month9` float NOT NULL,
  `month10` float NOT NULL,
  `month11` float NOT NULL,
  `month12` float NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`gpflid`)
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=457 DEFAULT CHARSET=latin1;
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
DROP TABLE IF EXISTS `integration_mediation_allproducts`;
/*!50001 DROP VIEW IF EXISTS `integration_mediation_allproducts`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `integration_mediation_allproducts` (
  `pid` tinyint NOT NULL,
  `name` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `integration_mediation_allsuppliers`;
/*!50001 DROP VIEW IF EXISTS `integration_mediation_allsuppliers`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `integration_mediation_allsuppliers` (
  `eid` tinyint NOT NULL,
  `companyName` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `incoterms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoterms` (
  `iid` int(10) NOT NULL AUTO_INCREMENT,
  `titleAbbr` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`iid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  KEY `pid` (`pid`,`spid`)
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
) ENGINE=MyISAM AUTO_INCREMENT=38605 DEFAULT CHARSET=utf8;
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
  PRIMARY KEY (`lid`),
  KEY `uid` (`uid`),
  KEY `type` (`type`),
  KEY `contactPerson` (`contactPerson`),
  KEY `coid` (`coid`),
  KEY `kiid` (`kiid`)
) ENGINE=MyISAM AUTO_INCREMENT=11555 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=11805 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=975 DEFAULT CHARSET=latin1;
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
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  `noNotification` tinyint(1) NOT NULL DEFAULT '0',
  `noBalance` tinyint(1) NOT NULL DEFAULT '1',
  `reasonIsRequired` tinyint(1) NOT NULL DEFAULT '0',
  `toApprove` text NOT NULL,
  `additionalFields` text,
  `countWith` smallint(5) DEFAULT '0',
  `coexistWith` text,
  PRIMARY KEY (`ltid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketintelligence_basicdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketintelligence_basicdata` (
  `mibdid` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL,
  `cfpid` int(10) NOT NULL,
  `cfcid` int(10) DEFAULT '0',
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
  PRIMARY KEY (`mrid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=8180 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=6710 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
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
DROP TABLE IF EXISTS `packaging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packaging` (
  `packid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`packid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `positiongroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positiongroups` (
  `posgid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
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
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2768 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=71455 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=utf8;
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
  `title` varchar(220) NOT NULL,
  `titleAbbr` varchar(20) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qr_missingforecast`;
/*!50001 DROP VIEW IF EXISTS `qr_missingforecast`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `qr_missingforecast` (
  `rid` tinyint NOT NULL,
  `identifier` tinyint NOT NULL,
  `year` tinyint NOT NULL,
  `affid` tinyint NOT NULL,
  `spid` tinyint NOT NULL,
  `initDate` tinyint NOT NULL,
  `uidFinish` tinyint NOT NULL,
  `finishDate` tinyint NOT NULL,
  `isLocked` tinyint NOT NULL,
  `isSent` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `month` tinyint NOT NULL,
  `quarter` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `prActivityAvailable` tinyint NOT NULL,
  `keyCustAvailable` tinyint NOT NULL,
  `mktReportAvailable` tinyint NOT NULL,
  `isApproved` tinyint NOT NULL,
  `summary` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `qr_redablereports`;
/*!50001 DROP VIEW IF EXISTS `qr_redablereports`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `qr_redablereports` (
  `rid` tinyint NOT NULL,
  `Supplier` tinyint NOT NULL,
  `Affiliate` tinyint NOT NULL,
  `quarter` tinyint NOT NULL,
  `year` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
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
) ENGINE=MyISAM AUTO_INCREMENT=8835 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=834 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`rid`),
  KEY `affid` (`affid`,`spid`),
  KEY `summary` (`summary`)
) ENGINE=MyISAM AUTO_INCREMENT=8695 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=5866 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=1209 DEFAULT CHARSET=latin1;
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
  `countLocally` int(1) NOT NULL,
  `invoiceAffStid` int(10) NOT NULL,
  `sequence` int(1) NOT NULL,
  `localIncomeByDefault` float NOT NULL,
  PRIMARY KEY (`stid`)
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
  `invoiceAffStid` int(10) NOT NULL,
  PRIMARY KEY (`stiid`,`affid`,`stid`),
  KEY `affid` (`affid`,`stid`,`invoiceAffid`)
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `segapplicationfunctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `segapplicationfunctions` (
  `safid` int(10) NOT NULL AUTO_INCREMENT,
  `cfid` int(10) NOT NULL,
  `psaid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`safid`,`cfid`,`psaid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `segmentapplications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `segmentapplications` (
  `psaid` int(10) NOT NULL AUTO_INCREMENT,
  `psid` smallint(5) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` int(30) NOT NULL,
  PRIMARY KEY (`psaid`,`psid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=1335 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=110171 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=14413 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=3455 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=714 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=8110 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=3450 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=3200 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=1624 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=348 DEFAULT CHARSET=utf8;
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
  PRIMARY KEY (`alid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `travelmanager_airports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_airports` (
  `apid` int(10) NOT NULL AUTO_INCREMENT,
  `iatacode` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `ciid` int(10) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
DROP TABLE IF EXISTS `unspecifiedsegment_marketreport`;
/*!50001 DROP VIEW IF EXISTS `unspecifiedsegment_marketreport`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `unspecifiedsegment_marketreport` (
  `displayName` tinyint NOT NULL,
  `quarter` tinyint NOT NULL,
  `year` tinyint NOT NULL,
  `companyName` tinyint NOT NULL,
  `affid` tinyint NOT NULL,
  `mrid` tinyint NOT NULL,
  `rid` tinyint NOT NULL,
  `psid` tinyint NOT NULL,
  `markTrendCompetition` tinyint NOT NULL,
  `quarterlyHighlights` tinyint NOT NULL,
  `devProjectsNewOp` tinyint NOT NULL,
  `issues` tinyint NOT NULL,
  `actionPlan` tinyint NOT NULL,
  `remarks` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `travelmanager_plan_accomodations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_accomodations` (
  `tmpaid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpsid` int(10) NOT NULL,
  `tmhid` int(10) NOT NULL,
  `accomType` smallint(5) NOT NULL,
  `priceNight` float NOT NULL,
  `numNights` smallint(3) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `paidById` int(10) NOT NULL,
  `paidBy` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tmpaid`,`tmpsid`,`tmhid`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS `travelmanager_plan_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_segments` (
  `tmpsid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpid` int(10) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `fromDate` bigint(30) NOT NULL,
  `toDate` bigint(30) NOT NULL,
  `reason` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(1) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(1) NOT NULL,
  `originCity` int(10) NOT NULL,
  `destinationCity` int(10) NOT NULL,
  `sequence` tinyint(3) NOT NULL,
  `apiFlightdata` longtext COLLATE utf8_unicode_ci NOT NULL,
  `purpose` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tmpsid`),
  KEY `tmpid` (`tmpid`,`originCity`,`destinationCity`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS `travelmanager_plan_transps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_plan_transps` (
  `tmpltid` int(10) NOT NULL AUTO_INCREMENT,
  `tmpsid` int(10) NOT NULL,
  `tmtcid` int(10) NOT NULL,
  `isMAin` tinyint(1) NOT NULL,
  `fare` float(10,0) NOT NULL,
  `vehicleNumber` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `flightNumber` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `flightDetails` text COLLATE utf8_unicode_ci NOT NULL,
  `agencyName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `numDays` decimal(10,0) NOT NULL,
  `transpType` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `paidById` int(10) NOT NULL,
  `paidBy` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tmpltid`,`tmpsid`,`tmtcid`),
  KEY `tmpltid` (`tmpltid`),
  KEY `tmtcid` (`tmtcid`),
  KEY `tmpltid_2` (`tmpltid`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `canManageProducts` int(1) NOT NULL DEFAULT '0',
  `canUseContents` int(1) NOT NULL DEFAULT '0',
  `contents_canManageLocations` tinyint(1) NOT NULL DEFAULT '0',
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
  `canUseMeetings` tinyint(1) NOT NULL DEFAULT '0',
  `meetings_canViewAllMeetings` tinyint(1) NOT NULL DEFAULT '0',
  `meetings_canCreateMeeting` tinyint(1) NOT NULL DEFAULT '0',
  `profiles_canViewContractInfo` int(1) NOT NULL,
  `cms_canAddNews` tinyint(1) NOT NULL,
  `cms_canPublishNews` tinyint(1) NOT NULL,
  `budgeting_canFillInvests` tinyint(1) NOT NULL,
  `budgeting_canFillComAdmExp` tinyint(1) NOT NULL,
  `budgeting_canFillFinBudgets` tinyint(1) NOT NULL,
  `Budget_canFillLocalincome` tinyint(1) NOT NULL,
  `canUseFinance` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=325 DEFAULT CHARSET=latin1;
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
  `defaultModule` varchar(10) NOT NULL,
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
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=353 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=986 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=389 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=MyISAM AUTO_INCREMENT=2535 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visitreports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitreports` (
  `vrid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=8001 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=8115 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=6617 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=8116 DEFAULT CHARSET=latin1;
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
/*!50001 DROP TABLE IF EXISTS `integration_mediation_allproducts`*/;
/*!50001 DROP VIEW IF EXISTS `integration_mediation_allproducts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `integration_mediation_allproducts` AS select `products`.`pid` AS `pid`,`products`.`name` AS `name` from `products` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `integration_mediation_allsuppliers`*/;
/*!50001 DROP VIEW IF EXISTS `integration_mediation_allsuppliers`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `integration_mediation_allsuppliers` AS select `entities`.`eid` AS `eid`,`entities`.`companyName` AS `companyName` from `entities` where (`entities`.`type` = _utf8's') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `qr_missingforecast`*/;
/*!50001 DROP VIEW IF EXISTS `qr_missingforecast`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `qr_missingforecast` AS select `reports`.`rid` AS `rid`,`reports`.`identifier` AS `identifier`,`reports`.`year` AS `year`,`reports`.`affid` AS `affid`,`reports`.`spid` AS `spid`,`reports`.`initDate` AS `initDate`,`reports`.`uidFinish` AS `uidFinish`,`reports`.`finishDate` AS `finishDate`,`reports`.`isLocked` AS `isLocked`,`reports`.`isSent` AS `isSent`,`reports`.`type` AS `type`,`reports`.`month` AS `month`,`reports`.`quarter` AS `quarter`,`reports`.`status` AS `status`,`reports`.`prActivityAvailable` AS `prActivityAvailable`,`reports`.`keyCustAvailable` AS `keyCustAvailable`,`reports`.`mktReportAvailable` AS `mktReportAvailable`,`reports`.`isApproved` AS `isApproved`,`reports`.`summary` AS `summary` from `reports` where ((`reports`.`quarter` = 3) and (`reports`.`year` = 2013) and (`reports`.`status` = 1) and `reports`.`rid` in (select `productsactivity`.`rid` AS `rid` from `productsactivity` where (((`productsactivity`.`quantityForecast` = 0) or (`productsactivity`.`salesForecast` = 0)) and ((`productsactivity`.`quantity` <> 0) or (`productsactivity`.`turnOver` <> 0)) and `productsactivity`.`rid` in (select `reports`.`rid` AS `rid` from `reports` where ((`reports`.`quarter` = 3) and (`reports`.`year` = 2013) and (`reports`.`status` = 1)))))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `qr_redablereports`*/;
/*!50001 DROP VIEW IF EXISTS `qr_redablereports`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `qr_redablereports` AS select `r`.`rid` AS `rid`,`s`.`companyName` AS `Supplier`,`a`.`name` AS `Affiliate`,`r`.`quarter` AS `quarter`,`r`.`year` AS `year` from ((`reports` `r` join `affiliates` `a` on((`a`.`affid` = `r`.`affid`))) join `entities` `s` on((`s`.`eid` = `r`.`spid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `unspecifiedsegment_marketreport`*/;
/*!50001 DROP VIEW IF EXISTS `unspecifiedsegment_marketreport`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `unspecifiedsegment_marketreport` AS select `u`.`displayName` AS `displayName`,`r`.`quarter` AS `quarter`,`r`.`year` AS `year`,`e`.`companyName` AS `companyName`,`r`.`affid` AS `affid`,`mkr`.`mrid` AS `mrid`,`mkr`.`rid` AS `rid`,`mkr`.`psid` AS `psid`,`mkr`.`markTrendCompetition` AS `markTrendCompetition`,`mkr`.`quarterlyHighlights` AS `quarterlyHighlights`,`mkr`.`devProjectsNewOp` AS `devProjectsNewOp`,`mkr`.`issues` AS `issues`,`mkr`.`actionPlan` AS `actionPlan`,`mkr`.`remarks` AS `remarks` from ((((`marketreport` `mkr` join `marketreport_authors` `mka` on((`mkr`.`mrid` = `mka`.`mrid`))) join `users` `u` on((`u`.`uid` = `mka`.`uid`))) join `reports` `r` on((`r`.`rid` = `mkr`.`rid`))) join `entities` `e` on((`r`.`spid` = `e`.`eid`))) where (`mkr`.`psid` < 1) order by `mkr`.`mrid` desc */;
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

