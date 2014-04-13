-- MySQL dump 10.13  Distrib 5.5.27, for Win32 (x86)
--
-- Host: localhost    Database: ocos
-- ------------------------------------------------------
-- Server version	5.5.27-log

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
-- Table structure for table `affiliatedemployees`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=888 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `affiliatedentities`
--

DROP TABLE IF EXISTS `affiliatedentities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliatedentities` (
  `aeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) unsigned NOT NULL,
  `eid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`aeid`,`affid`,`eid`),
  KEY `affid` (`affid`,`eid`)
) ENGINE=MyISAM AUTO_INCREMENT=3723 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `affiliates`
--

DROP TABLE IF EXISTS `affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliates` (
  `affid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(220) NOT NULL,
  `legalName` varchar(220) NOT NULL,
  `generalManager` int(10) unsigned NOT NULL,
  `supervisor` int(10) unsigned NOT NULL,
  `hrManager` int(10) unsigned NOT NULL,
  `finManager` int(10) NOT NULL,
  `mailingList` varchar(200) NOT NULL,
  `altMailingList` varchar(200) NOT NULL,
  `description` text,
  `country` int(10) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postCode` int(6) DEFAULT NULL,
  `addressLine1` varchar(200) DEFAULT NULL,
  `addressLine2` varchar(100) DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `poBox` int(10) DEFAULT NULL,
  `mainEmail` varchar(220) NOT NULL,
  `qrAlwaysCopy` text NOT NULL,
  `vrAlwaysNotify` text,
  `defaultWorkshift` smallint(10) NOT NULL,
  `systemLang` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`affid`),
  KEY `name` (`name`),
  KEY `generalManager` (`generalManager`,`supervisor`),
  KEY `hrManager` (`hrManager`),
  KEY `country` (`country`),
  KEY `defaultWorkshift` (`defaultWorkshift`),
  KEY `geoLocation` (`geoLocation`(25))
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `affiliates_accountingtree`
--

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

--
-- Table structure for table `affiliatesleavespolicies`
--

DROP TABLE IF EXISTS `affiliatesleavespolicies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliatesleavespolicies` (
  `alpid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` int(10) unsigned NOT NULL,
  `ltid` smallint(5) unsigned NOT NULL,
  `promotionPolicy` text,
  `basicEntitlement` smallint(2) NOT NULL,
  `canAccumulateFor` tinyint(2) NOT NULL,
  `maxAccumulateDays` int(2) NOT NULL DEFAULT '0',
  `entitleAfter` tinyint(2) NOT NULL,
  `oneTimeBonusDays` tinyint(2) DEFAULT NULL,
  `oneTimeBonusAfter` tinyint(2) DEFAULT NULL,
  `halfDayMargin` float NOT NULL DEFAULT '0',
  `useFirstJobDate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`alpid`,`affid`,`ltid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets_locations`
--

DROP TABLE IF EXISTS `assets_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets_locations` (
  `alid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asid` int(10) unsigned NOT NULL,
  `location` point NOT NULL,
  `timeLine` bigint(30) NOT NULL,
  `deviceId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`alid`),
  KEY `asid` (`asid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets_trackingdevices`
--

DROP TABLE IF EXISTS `assets_trackingdevices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets_trackingdevices` (
  `atdid` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `deviceId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `asid` int(10) NOT NULL,
  `fromDate` bigint(30) NOT NULL,
  `toDate` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`atdid`,`deviceId`,`asid`),
  KEY `deviceId` (`deviceId`,`asid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets_types`
--

DROP TABLE IF EXISTS `assets_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets_types` (
  `astid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`astid`),
  UNIQUE KEY `astid` (`astid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets_users`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assignedemployees`
--

DROP TABLE IF EXISTS `assignedemployees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignedemployees` (
  `aseid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `affid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`aseid`,`eid`,`uid`,`affid`),
  KEY `eid` (`eid`,`uid`),
  KEY `affid` (`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=4714 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attendance`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=17154 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attendance_additionalleaves`
--

DROP TABLE IF EXISTS `attendance_additionalleaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_additionalleaves` (
  `adid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(100) NOT NULL,
  `uid` int(10) NOT NULL,
  `numDays` float NOT NULL,
  `date` bigint(30) NOT NULL,
  `correspondToDate` tinyint(1) NOT NULL DEFAULT '1',
  `remark` text NOT NULL,
  `addedBy` int(10) NOT NULL,
  `isApproved` tinyint(1) NOT NULL DEFAULT '0',
  `approvedOn` bigint(30) DEFAULT NULL,
  `requestedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`adid`),
  KEY `uid` (`uid`),
  KEY `addedBy` (`addedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attendance_leaveexptypes`
--

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

--
-- Table structure for table `attendance_leaves_expenses`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attendance_leavetypes_expenses`
--

DROP TABLE IF EXISTS `attendance_leavetypes_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_leavetypes_expenses` (
  `alteid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `ltid` smallint(5) NOT NULL,
  `aletid` smallint(10) NOT NULL,
  `titleOverwrite` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `budgeting_budgets`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `budgeting_budgets_lines`
--

DROP TABLE IF EXISTS `budgeting_budgets_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgeting_budgets_lines` (
  `blid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `bid` int(10) unsigned NOT NULL,
  `cid` int(10) NOT NULL,
  `prevblid` int(10) NOT NULL,
  `altCid` varchar(50) NOT NULL,
  `customerCountry` int(10) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=731 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_events`
--

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
  `affid` smallint(5) NOT NULL,
  `spid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `isPublic` tinyint(1) NOT NULL,
  `publishOnWebsite` tinyint(1) NOT NULL DEFAULT '0',
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `editedBy` int(10) NOT NULL,
  `editedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`ceid`),
  KEY `uid` (`uid`),
  KEY `affid` (`affid`),
  KEY `spid` (`spid`),
  KEY `createdBy` (`createdBy`,`editedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=157 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_events_invitees`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_events_restrictions`
--

DROP TABLE IF EXISTS `calendar_events_restrictions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_events_restrictions` (
  `cerid` int(10) NOT NULL AUTO_INCREMENT,
  `ceid` int(10) NOT NULL,
  `affid` int(10) NOT NULL,
  PRIMARY KEY (`cerid`,`ceid`,`affid`),
  KEY `ceid` (`ceid`,`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_eventtypes`
--

DROP TABLE IF EXISTS `calendar_eventtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_eventtypes` (
  `cetid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`cetid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_tasks`
--

DROP TABLE IF EXISTS `calendar_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_tasks` (
  `ctid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `subject` varchar(220) NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT '1',
  `dueDate` bigint(30) NOT NULL,
  `isDone` tinyint(1) NOT NULL DEFAULT '0',
  `timeDone` bigint(30) DEFAULT NULL,
  `description` text,
  `percCompleted` tinyint(3) NOT NULL DEFAULT '0',
  `reminderStart` bigint(30) DEFAULT NULL,
  `reminderInterval` int(10) DEFAULT NULL,
  `createdBy` int(10) NOT NULL,
  `pimAppId` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ctid`),
  KEY `uid` (`uid`),
  KEY `createdBy` (`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=320 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_tasks_notes`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_userpreferences`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_userpreferences_excludedaffiliates`
--

DROP TABLE IF EXISTS `calendar_userpreferences_excludedaffiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_userpreferences_excludedaffiliates` (
  `cpeaid` int(10) NOT NULL AUTO_INCREMENT,
  `affid` int(10) NOT NULL,
  `cpid` int(10) NOT NULL,
  PRIMARY KEY (`cpeaid`,`affid`,`cpid`),
  KEY `uid` (`affid`,`cpid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_userpreferences_excludedusers`
--

DROP TABLE IF EXISTS `calendar_userpreferences_excludedusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_userpreferences_excludedusers` (
  `cpeuid` int(10) NOT NULL AUTO_INCREMENT,
  `euid` int(10) NOT NULL,
  `cpid` int(10) NOT NULL,
  PRIMARY KEY (`cpeuid`,`euid`,`cpid`),
  KEY `uid` (`euid`,`cpid`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chemfunctionchemcials`
--

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

--
-- Table structure for table `chemfunctionproducts`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chemicalfunctions`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chemicalsubstances`
--

DROP TABLE IF EXISTS `chemicalsubstances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chemicalsubstances` (
  `csid` int(10) NOT NULL AUTO_INCREMENT,
  `casNum` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `synonyms` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`csid`)
) ENGINE=MyISAM AUTO_INCREMENT=2088 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `ciid` int(10) NOT NULL AUTO_INCREMENT,
  `coid` smallint(5) NOT NULL,
  `name` varchar(220) NOT NULL,
  PRIMARY KEY (`ciid`),
  KEY `coid` (`coid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_contentcategories`
--

DROP TABLE IF EXISTS `cms_contentcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_contentcategories` (
  `cmsccid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cmsccid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_menuitems`
--

DROP TABLE IF EXISTS `cms_menuitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_menuitems` (
  `cmsmiid` int(10) NOT NULL AUTO_INCREMENT,
  `cmsmid` tinyint(10) NOT NULL,
  `parent` int(10) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_menus`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_news`
--

DROP TABLE IF EXISTS `cms_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_news` (
  `cmsnid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) CHARACTER SET latin1 NOT NULL,
  `alias` varchar(200) CHARACTER SET latin1 NOT NULL,
  `version` float NOT NULL DEFAULT '1',
  `summary` text CHARACTER SET latin1 NOT NULL,
  `publishDate` bigint(30) NOT NULL,
  `isPublished` tinyint(1) NOT NULL DEFAULT '0',
  `isFeatured` tinyint(1) NOT NULL DEFAULT '0',
  `lang` varchar(2) CHARACTER SET latin1 NOT NULL,
  `bodyText` text CHARACTER SET latin1 NOT NULL,
  `metaDesc` text CHARACTER SET latin1 NOT NULL,
  `metaKeywords` text CHARACTER SET latin1 NOT NULL,
  `robotsRule` varchar(100) CHARACTER SET latin1 NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createDate` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifyDate` bigint(30) NOT NULL,
  `hits` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uploadedImages` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`cmsnid`),
  KEY `createDate` (`createDate`,`modifyDate`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_news_attachments`
--

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

--
-- Table structure for table `cms_news_relatedcategories`
--

DROP TABLE IF EXISTS `cms_news_relatedcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_news_relatedcategories` (
  `cmsnrcid` int(10) NOT NULL AUTO_INCREMENT,
  `cmsnid` int(10) NOT NULL,
  `cmsccid` smallint(5) NOT NULL,
  PRIMARY KEY (`cmsnrcid`,`cmsnid`,`cmsccid`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_pagecategories`
--

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

--
-- Table structure for table `cms_pages`
--

DROP TABLE IF EXISTS `cms_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_pages` (
  `cmspid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `alias` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `version` float NOT NULL,
  `publishDate` bigint(30) NOT NULL,
  `isPublished` tinyint(1) NOT NULL,
  `category` tinyint(2) unsigned NOT NULL,
  `lang` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `bodyText` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `metaDesc` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `metaKeywords` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `robotsRule` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  `hits` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`cmspid`),
  KEY `createdBy` (`createdBy`,`modifiedBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `countries`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=240 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currencies`
--

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

--
-- Table structure for table `currencies_fxrates`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=70112 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `development_requirements`
--

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
  `security` text NOT NULL,
  `userInterface` text NOT NULL,
  `performance` text NOT NULL,
  `isApproved` tinyint(1) NOT NULL,
  `isCompleted` tinyint(1) NOT NULL DEFAULT '0',
  `requestedBy` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `assignedTo` int(10) DEFAULT NULL,
  PRIMARY KEY (`drid`),
  KEY `parent` (`parent`,`requestedBy`,`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `development_requirements_changes`
--

DROP TABLE IF EXISTS `development_requirements_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `development_requirements_changes` (
  `drcid` int(10) NOT NULL AUTO_INCREMENT,
  `refKey` smallint(5) NOT NULL,
  `drid` int(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  `reasonCategory` tinyint(2) NOT NULL,
  `description` text NOT NULL,
  `impact` text NOT NULL,
  `outcomeReq` int(10) NOT NULL,
  `requestedBy` int(10) NOT NULL,
  `dateRequested` bigint(30) NOT NULL,
  `approvedBy` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  PRIMARY KEY (`drcid`),
  KEY `identifier` (`outcomeReq`,`requestedBy`,`approvedBy`,`createdBy`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employeeseducationcert`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employeesexperience`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employeessegments`
--

DROP TABLE IF EXISTS `employeessegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employeessegments` (
  `emsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `psid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`emsid`,`uid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=190 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employeesshifts`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=168 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `endproducttypes`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entities`
--

DROP TABLE IF EXISTS `entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities` (
  `eid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) NOT NULL DEFAULT '0',
  `presence` varchar(10) NOT NULL,
  `companyName` varchar(250) DEFAULT NULL,
  `companyNameShort` varchar(20) DEFAULT NULL,
  `companyNameAbbr` varchar(10) DEFAULT NULL,
  `logo` varchar(220) DEFAULT NULL,
  `country` int(10) unsigned NOT NULL,
  `city` varchar(100) NOT NULL,
  `addressLine1` varchar(200) NOT NULL,
  `addressLine2` varchar(150) DEFAULT NULL,
  `building` varchar(100) NOT NULL,
  `floor` int(2) DEFAULT NULL,
  `geoLocation` point DEFAULT NULL,
  `mainEmail` varchar(220) DEFAULT NULL,
  `postCode` int(6) DEFAULT NULL,
  `poBox` int(10) DEFAULT NULL,
  `phone1` varchar(20) NOT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `fax1` varchar(20) DEFAULT NULL,
  `fax2` varchar(20) DEFAULT NULL,
  `website` varchar(220) DEFAULT NULL,
  `approved` smallint(1) unsigned NOT NULL DEFAULT '0',
  `dateAdded` bigint(30) unsigned NOT NULL,
  `paymentTerms` int(3) NOT NULL,
  `notes` text,
  `isPotential` tinyint(1) NOT NULL,
  `type` char(1) NOT NULL,
  `supplierType` varchar(10) NOT NULL,
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
  PRIMARY KEY (`eid`),
  KEY `parent` (`parent`),
  KEY `geoLocation` (`geoLocation`(25))
) ENGINE=MyISAM AUTO_INCREMENT=872 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entities_ratingcriteria`
--

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

--
-- Table structure for table `entities_ratings`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entities_rmlevels`
--

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

--
-- Table structure for table `entities_rmlevels_categories`
--

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

--
-- Table structure for table `entitiesbrands`
--

DROP TABLE IF EXISTS `entitiesbrands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitiesbrands` (
  `ebid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `eid` int(10) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`ebid`),
  KEY `spid` (`eid`,`createdBy`,`modifiedBy`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entitiesbrandsproducts`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entitiesrepresentatives`
--

DROP TABLE IF EXISTS `entitiesrepresentatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitiesrepresentatives` (
  `erpid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rpid` int(10) unsigned NOT NULL,
  `eid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`erpid`,`rpid`,`eid`),
  KEY `eid` (`eid`)
) ENGINE=MyISAM AUTO_INCREMENT=1342 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entitiessegments`
--

DROP TABLE IF EXISTS `entitiessegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitiessegments` (
  `esid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eid` int(10) unsigned NOT NULL,
  `psid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`esid`,`eid`,`psid`),
  KEY `eid` (`eid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=2742 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files_viewrestriction`
--

DROP TABLE IF EXISTS `files_viewrestriction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files_viewrestriction` (
  `faid` int(10) NOT NULL AUTO_INCREMENT,
  `fid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`faid`),
  KEY `fid` (`fid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filescategories`
--

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

--
-- Table structure for table `filesfolder`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filesfolder_viewrestriction`
--

DROP TABLE IF EXISTS `filesfolder_viewrestriction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filesfolder_viewrestriction` (
  `ffaid` int(10) NOT NULL AUTO_INCREMENT,
  `ffid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `noRead` int(1) NOT NULL DEFAULT '0',
  `noWrite` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ffaid`),
  KEY `ffid` (`ffid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=838 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fileversions`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `genericproducts`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grouppurchase_pricing`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grouppurchase_pricingdetails`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `helpdocuments`
--

DROP TABLE IF EXISTS `helpdocuments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `helpdocuments` (
  `hdid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) NOT NULL,
  `description` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `module` varchar(200) NOT NULL,
  `relatesTo` text NOT NULL,
  `language` varchar(3) NOT NULL,
  `dateCreated` int(11) NOT NULL,
  `dateUpdated` int(11) NOT NULL,
  `author` int(10) NOT NULL,
  `isEnables` tinyint(1) NOT NULL,
  `dispOrder` tinyint(1) NOT NULL,
  PRIMARY KEY (`hdid`),
  KEY `isEnables` (`isEnables`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `holidays`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `holidaysexceptions`
--

DROP TABLE IF EXISTS `holidaysexceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidaysexceptions` (
  `heid` int(10) NOT NULL AUTO_INCREMENT,
  `hid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`heid`,`hid`,`uid`),
  KEY `hid` (`hid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_industries`
--

DROP TABLE IF EXISTS `hr_industries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_industries` (
  `hriid` mediumint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`hriid`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_userspermissions`
--

DROP TABLE IF EXISTS `hr_userspermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_userspermissions` (
  `hrupid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `affid` smallint(5) NOT NULL,
  PRIMARY KEY (`hrupid`,`uid`,`affid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_vacancies`
--

DROP TABLE IF EXISTS `hr_vacancies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_vacancies` (
  `hrvid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `reference` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `affid` smallint(5) NOT NULL,
  `workLocation` int(10) NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `employmentType` tinyint(1) NOT NULL,
  `shortDesc` text COLLATE utf8_unicode_ci NOT NULL,
  `responsibilities` text COLLATE utf8_unicode_ci NOT NULL,
  `managesOthers` tinyint(1) NOT NULL,
  `salary` float NOT NULL,
  `approxJoinDate` bigint(30) NOT NULL,
  `gender` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `nationality` int(10) NOT NULL,
  `residence` int(10) NOT NULL,
  `careerLevel` tinyint(1) NOT NULL,
  `minEducation` tinyint(1) NOT NULL,
  `minYearsExp` tinyint(2) NOT NULL,
  `maxYearsExp` tinyint(2) NOT NULL,
  `minQualifications` text COLLATE utf8_unicode_ci NOT NULL,
  `prefQualifications` text COLLATE utf8_unicode_ci NOT NULL,
  `drivingLicReq` tinyint(1) NOT NULL,
  `publishOn` bigint(30) NOT NULL,
  `unpublishOn` bigint(30) NOT NULL,
  `publishingTimeZone` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `allowSocialSharing` tinyint(1) NOT NULL,
  `autoFilterType` tinyint(1) NOT NULL,
  `filterMinYearsExp` int(11) NOT NULL,
  `filterMaxYearsExp` int(11) NOT NULL,
  `filterMinEducation` int(11) NOT NULL,
  `filterGender` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `filterMinAge` tinyint(2) NOT NULL,
  `filterMaxAge` tinyint(2) NOT NULL,
  `filterMinCareerLevel` tinyint(1) NOT NULL,
  `reqPortraitPhoto` tinyint(1) NOT NULL DEFAULT '0',
  `reqIdNumber` tinyint(1) NOT NULL DEFAULT '0',
  `reqMaritalStatus` tinyint(1) NOT NULL DEFAULT '0',
  `reqMilitaryStatus` tinyint(1) DEFAULT '0',
  `reqDiseasesInfo` tinyint(1) DEFAULT '0',
  `reqEducationDetails` tinyint(1) DEFAULT '1',
  `reqTrainingDetails` tinyint(1) DEFAULT '0',
  `reqPrevExperience` tinyint(1) DEFAULT '1',
  `hasOnlineInterview` tinyint(1) NOT NULL,
  `dateCreated` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `dateModified` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `isCanceled` tinyint(1) NOT NULL,
  `views` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hrvid`),
  KEY `affid` (`affid`,`workLocation`,`nationality`,`residence`,`createdBy`,`modifiedBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_vacancies_applicants`
--

DROP TABLE IF EXISTS `hr_vacancies_applicants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_vacancies_applicants` (
  `hrvaid` int(10) NOT NULL AUTO_INCREMENT,
  `hrvid` int(10) NOT NULL,
  `identifier` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `firstName` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `gender` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `birtDate` bigint(30) NOT NULL,
  `birthPlace` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(220) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` int(10) NOT NULL,
  `nationality` int(10) NOT NULL,
  `maritalStatus` tinyint(1) DEFAULT NULL,
  `hasChildren` tinyint(1) DEFAULT NULL,
  `militaryStatus` tinyint(1) DEFAULT NULL,
  `relativesWorkHere` tinyint(1) DEFAULT NULL,
  `hasDiseases` tinyint(1) DEFAULT NULL,
  `diseasesDesc` text COLLATE utf8_unicode_ci,
  `hasEmploymentRestrictions` tinyint(1) DEFAULT NULL,
  `employmentRestrictionsDesc` text COLLATE utf8_unicode_ci,
  `lastSalary` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `yearsExperience` tinyint(2) NOT NULL,
  `educationLevel` tinyint(1) NOT NULL,
  `hasDrivingLicense` tinyint(1) DEFAULT NULL,
  `specialSkills` text COLLATE utf8_unicode_ci,
  `dateSubmitted` bigint(30) NOT NULL,
  `ipAddress` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `isFlagged` tinyint(1) NOT NULL,
  PRIMARY KEY (`hrvaid`),
  KEY `hrvid` (`hrvid`),
  KEY `residence` (`city`,`nationality`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_vacancies_filteredresidences`
--

DROP TABLE IF EXISTS `hr_vacancies_filteredresidences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_vacancies_filteredresidences` (
  `hrvfrid` int(10) NOT NULL AUTO_INCREMENT,
  `hrvid` int(10) NOT NULL,
  `ciid` int(10) NOT NULL,
  `filterType` tinyint(1) NOT NULL,
  PRIMARY KEY (`hrvfrid`),
  KEY `hrvid` (`hrvid`,`ciid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_vacancies_interviewquestions`
--

DROP TABLE IF EXISTS `hr_vacancies_interviewquestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_vacancies_interviewquestions` (
  `hrviqid` int(10) NOT NULL AUTO_INCREMENT,
  `hrvid` int(10) NOT NULL,
  `question` text COLLATE utf8_unicode_ci NOT NULL,
  `readingTime` smallint(4) unsigned NOT NULL,
  `answeringTime` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`hrviqid`),
  KEY `hrvid` (`hrvid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_vacancies_interviews`
--

DROP TABLE IF EXISTS `hr_vacancies_interviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_vacancies_interviews` (
  `hrviid` int(10) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `hrvaid` int(10) NOT NULL,
  `interviewVideo` longblob NOT NULL,
  `interviewDate` bigint(30) NOT NULL,
  `interviewer` int(10) NOT NULL,
  `feedback` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`hrviid`),
  KEY `hrvaid` (`hrvaid`,`interviewer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_vacancies_reqlangs`
--

DROP TABLE IF EXISTS `hr_vacancies_reqlangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_vacancies_reqlangs` (
  `hrvrid` int(10) NOT NULL AUTO_INCREMENT,
  `hrvid` int(10) NOT NULL,
  `lang` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`hrvrid`),
  KEY `hrvid` (`hrvid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `importtemp`
--

DROP TABLE IF EXISTS `importtemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `importtemp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyName` varchar(50) DEFAULT NULL,
  `country` varchar(50) NOT NULL,
  `ProducerTrader` varchar(50) DEFAULT NULL,
  `Contactperson` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `cell` varchar(50) DEFAULT NULL,
  `website` varchar(50) DEFAULT NULL,
  `MainApplicationsCovered` text,
  `Mainproducts1` varchar(100) DEFAULT NULL,
  `Mainproducts2` varchar(100) DEFAULT NULL,
  `Mainproducts3` varchar(100) DEFAULT NULL,
  `Mainproducts4` varchar(100) DEFAULT NULL,
  `Mainproducts5` varchar(100) DEFAULT NULL,
  `Mainproducts6` varchar(100) DEFAULT NULL,
  `Mainproducts7` varchar(100) DEFAULT NULL,
  `Mainproducts8` varchar(100) DEFAULT NULL,
  `Mainproducts9` varchar(100) DEFAULT NULL,
  `Mainproducts10` varchar(100) DEFAULT NULL,
  `Mainproducts11` varchar(100) DEFAULT NULL,
  `Mainproducts12` varchar(100) DEFAULT NULL,
  `Mainproducts13` varchar(100) DEFAULT NULL,
  `Mainproducts14` varchar(100) DEFAULT NULL,
  `Mainproducts15` varchar(100) DEFAULT NULL,
  `Mainproducts16` varchar(100) DEFAULT NULL,
  `Mainproducts17` varchar(100) DEFAULT NULL,
  `Mainproducts18` varchar(100) DEFAULT NULL,
  `Mainproducts19` varchar(100) DEFAULT NULL,
  `Mainproducts20` varchar(100) DEFAULT NULL,
  `Mainproducts21` varchar(100) DEFAULT NULL,
  `Mainproducts22` varchar(100) DEFAULT NULL,
  `Mainproducts23` varchar(100) DEFAULT NULL,
  `Mainproducts24` varchar(100) DEFAULT NULL,
  `Mainproducts25` varchar(100) DEFAULT NULL,
  `Mainproducts26` varchar(100) DEFAULT NULL,
  `Mainproducts27` varchar(100) DEFAULT NULL,
  `Mainproducts28` varchar(100) DEFAULT NULL,
  `Mainproducts29` varchar(100) DEFAULT NULL,
  `Mainproducts30` varchar(100) DEFAULT NULL,
  `Briefing` text,
  `Historical` text,
  `Approachvia` varchar(100) DEFAULT NULL,
  `SourcingAction` text,
  `market` varchar(100) DEFAULT NULL,
  `DestinationCountry` varchar(100) DEFAULT NULL,
  `BMname` varchar(100) DEFAULT NULL,
  `BMemail` varchar(20) NOT NULL,
  `BMPhone` varchar(100) DEFAULT NULL,
  `Product` varchar(100) DEFAULT NULL,
  `ClassGrade` varchar(100) DEFAULT NULL,
  `Origin` varchar(100) DEFAULT NULL,
  `Application` varchar(100) DEFAULT NULL,
  `Marketcompetitors` varchar(100) DEFAULT NULL,
  `Generalcomments` text,
  `Commentstoshare` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila1` varchar(100) DEFAULT NULL,
  `AO1` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila2` varchar(100) DEFAULT NULL,
  `AO2` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila3` varchar(100) DEFAULT NULL,
  `AO3` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila4` varchar(100) DEFAULT NULL,
  `AO4` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila5` varchar(100) DEFAULT NULL,
  `AO5` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila6` varchar(100) DEFAULT NULL,
  `AO6` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila7` varchar(100) DEFAULT NULL,
  `AO7` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila8` varchar(100) DEFAULT NULL,
  `AO8` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila9` varchar(100) DEFAULT NULL,
  `AO9` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila10` varchar(100) DEFAULT NULL,
  `AO10` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila11` varchar(100) DEFAULT NULL,
  `AO11` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila12` varchar(100) DEFAULT NULL,
  `AO12` varchar(100) DEFAULT NULL,
  `ActivitywithOrkila13` varchar(100) DEFAULT NULL,
  `Text32683` text,
  `Text32682` text,
  `Text32678` text,
  `Combo32670` varchar(100) DEFAULT NULL,
  `AO13` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5045 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_entities`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=4183 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_entities2`
--

DROP TABLE IF EXISTS `integration_mediation_entities2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_entities2` (
  `imspid` int(20) NOT NULL AUTO_INCREMENT,
  `localId` int(10) NOT NULL,
  `foreignSystem` tinyint(1) NOT NULL DEFAULT '1',
  `foreignId` varchar(32) NOT NULL,
  `foreignName` varchar(220) NOT NULL,
  `foreignNameAbbr` varchar(40) DEFAULT NULL,
  `affid` int(10) NOT NULL,
  `entityType` varchar(1) NOT NULL DEFAULT 's',
  `contactPersonName` varchar(200) NOT NULL,
  `country` int(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `addressLine1` varchar(220) NOT NULL,
  `addressLine2` varchar(200) NOT NULL,
  `building` varchar(100) NOT NULL,
  `floor` int(2) NOT NULL,
  `postCode` int(6) NOT NULL,
  `poBox` int(10) NOT NULL,
  `phone1` varchar(20) NOT NULL,
  `phone2` varchar(20) NOT NULL,
  `fax` varchar(20) NOT NULL,
  `mainEmail` varchar(220) NOT NULL,
  `paymentTerms` int(3) NOT NULL,
  `foreignDate` bigint(30) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `addedBy` varchar(50) NOT NULL,
  PRIMARY KEY (`imspid`),
  UNIQUE KEY `foreignId` (`foreignId`,`foreignName`,`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=182 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_products`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=9869 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_products2`
--

DROP TABLE IF EXISTS `integration_mediation_products2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_products2` (
  `impid` int(20) NOT NULL AUTO_INCREMENT,
  `localId` int(10) NOT NULL,
  `foreignSystem` tinyint(1) NOT NULL DEFAULT '1',
  `foreignId` varchar(32) NOT NULL,
  `foreignName` varchar(220) NOT NULL,
  `foreignNameAbbr` varchar(40) NOT NULL,
  `foreignSupplier` varchar(32) NOT NULL,
  `affid` int(10) NOT NULL,
  `localDate` bigint(30) NOT NULL,
  `foreignDate` bigint(30) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `addedBy` varchar(200) NOT NULL,
  PRIMARY KEY (`impid`),
  KEY `affid` (`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=110 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_purchaseorderlines`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=903989 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_purchaseorderlines2`
--

DROP TABLE IF EXISTS `integration_mediation_purchaseorderlines2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_purchaseorderlines2` (
  `impolid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignId` varchar(32) NOT NULL,
  `foreignOrderId` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `spid` varchar(32) NOT NULL,
  `affid` int(10) NOT NULL,
  `price` float NOT NULL,
  `quantity` float NOT NULL,
  `quantityUnit` varchar(10) NOT NULL,
  PRIMARY KEY (`impolid`),
  KEY `pid` (`pid`,`spid`)
) ENGINE=MyISAM AUTO_INCREMENT=463 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_purchaseorders`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=2459 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_purchaseorders2`
--

DROP TABLE IF EXISTS `integration_mediation_purchaseorders2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_purchaseorders2` (
  `impoid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignSystem` tinyint(1) NOT NULL,
  `foreignId` varchar(32) NOT NULL,
  `docNum` varchar(30) NOT NULL,
  `date` bigint(30) NOT NULL,
  `spid` varchar(32) NOT NULL,
  `affid` int(10) NOT NULL,
  `currency` varchar(4) NOT NULL,
  `usdFxrate` float DEFAULT NULL,
  `paymentTerms` int(3) DEFAULT NULL,
  `purchaseType` varchar(5) NOT NULL,
  PRIMARY KEY (`impoid`),
  KEY `cid` (`spid`,`affid`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_salesorderlines`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=3282018 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_salesorderlines2`
--

DROP TABLE IF EXISTS `integration_mediation_salesorderlines2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_salesorderlines2` (
  `imsolid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignId` varchar(32) NOT NULL,
  `foreignOrderId` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `spid` varchar(32) NOT NULL,
  `affid` int(10) NOT NULL,
  `price` float NOT NULL,
  `quantity` float NOT NULL,
  `quantityUnit` varchar(10) NOT NULL,
  `cost` float NOT NULL,
  `costCurrency` varchar(4) DEFAULT NULL,
  `purchasePrice` float NOT NULL,
  `purPriceCurrency` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`imsolid`),
  KEY `pid` (`pid`,`spid`)
) ENGINE=MyISAM AUTO_INCREMENT=585 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_salesorders`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=10464 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_salesorders2`
--

DROP TABLE IF EXISTS `integration_mediation_salesorders2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_mediation_salesorders2` (
  `imsoid` int(10) NOT NULL AUTO_INCREMENT,
  `foreignSystem` tinyint(1) NOT NULL,
  `foreignId` varchar(32) NOT NULL,
  `docNum` varchar(30) NOT NULL,
  `date` bigint(30) NOT NULL,
  `cid` varchar(32) NOT NULL,
  `affid` int(10) NOT NULL,
  `currency` varchar(4) NOT NULL,
  `usdFxrate` float DEFAULT NULL,
  `paymentTerms` int(3) DEFAULT NULL,
  `salesRep` varchar(220) NOT NULL,
  `salesRepLocalId` int(10) NOT NULL,
  `saleType` varchar(5) NOT NULL,
  PRIMARY KEY (`imsoid`),
  KEY `cid` (`cid`,`affid`,`salesRep`),
  KEY `salesRepLocalId` (`salesRepLocalId`)
) ENGINE=MyISAM AUTO_INCREMENT=142 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `integration_mediation_stockpurchases`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keycustomers`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=32729 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kinships`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leaves`
--

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
  `affid` int(10) unsigned NOT NULL,
  `spid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `coid` int(10) unsigned NOT NULL,
  `ceid` int(10) NOT NULL,
  `kiid` tinyint(10) unsigned NOT NULL,
  PRIMARY KEY (`lid`),
  KEY `uid` (`uid`),
  KEY `type` (`type`),
  KEY `contactPerson` (`contactPerson`),
  KEY `affid` (`affid`,`spid`,`cid`),
  KEY `coid` (`coid`),
  KEY `kiid` (`kiid`)
) ENGINE=MyISAM AUTO_INCREMENT=556 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leaves_messages`
--

DROP TABLE IF EXISTS `leaves_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves_messages` (
  `lmid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `inReplyTo` int(10) NOT NULL,
  `inReplyToMsgId` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `permission` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`lmid`),
  KEY `uid` (`uid`,`inReplyTo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leavesapproval`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=581 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leavesstats`
--

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
  `canTake` float unsigned NOT NULL,
  `entitledFor` float unsigned NOT NULL,
  `remainPrevYear` float NOT NULL,
  `validAccum` float NOT NULL,
  `additionalDays` float NOT NULL,
  PRIMARY KEY (`lsid`,`uid`,`ltid`)
) ENGINE=MyISAM AUTO_INCREMENT=500 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leavetypes`
--

DROP TABLE IF EXISTS `leavetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leavetypes` (
  `ltid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(220) NOT NULL,
  `symbol` varchar(10) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `isWholeDay` tinyint(1) NOT NULL DEFAULT '1',
  `isPM` tinyint(1) DEFAULT '0',
  `isAnnual` tinyint(1) NOT NULL DEFAULT '1',
  `isSick` tinyint(1) NOT NULL DEFAULT '0',
  `isBusiness` tinyint(1) NOT NULL DEFAULT '0',
  `reasonIsRequired` tinyint(1) NOT NULL DEFAULT '0',
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  `noNotification` tinyint(1) NOT NULL DEFAULT '0',
  `noBalance` tinyint(1) NOT NULL DEFAULT '1',
  `toApprove` text NOT NULL,
  `additionalFields` text NOT NULL,
  `countWith` smallint(5) unsigned DEFAULT '0',
  `coexistWith` text,
  PRIMARY KEY (`ltid`),
  KEY `countWith` (`countWith`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=8162 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketintelligence_basicdata`
--

DROP TABLE IF EXISTS `marketintelligence_basicdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketintelligence_basicdata` (
  `mibdid` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL,
  `cfpid` int(10) NOT NULL,
  `ebpid` int(10) NOT NULL,
  `potential` float NOT NULL,
  `mktSharePerc` float NOT NULL,
  `mktShareQty` float NOT NULL,
  `unitPrice` float NOT NULL,
  `comments` text COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`mibdid`),
  KEY `cid` (`cid`,`cfpid`,`ebpid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketintelligence_competitors`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketreport`
--

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
  KEY `rid` (`rid`),
  KEY `psid` (`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=6086 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketreport_authors`
--

DROP TABLE IF EXISTS `marketreport_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketreport_authors` (
  `mkra` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `mrid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mkra`),
  KEY `uid` (`uid`,`mrid`)
) ENGINE=MyISAM AUTO_INCREMENT=4406 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetings`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=146 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetings_associations`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetings_attachments`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetings_attendees`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetings_minsofmeeting`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetings_sharedwith`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `monthly_highlights`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `monthly_productsstatus`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `packingtypes`
--

DROP TABLE IF EXISTS `packingtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packingtypes` (
  `ptid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`ptid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions` (
  `posid` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(220) NOT NULL,
  PRIMARY KEY (`posid`)
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productpacking`
--

DROP TABLE IF EXISTS `productpacking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productpacking` (
  `ppid` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `packingType` int(10) NOT NULL,
  `packingWeight` float NOT NULL,
  `fcl` float NOT NULL,
  PRIMARY KEY (`ppid`,`pid`,`packingType`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

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
  `package` varchar(220) NOT NULL,
  `standard` varchar(220) DEFAULT NULL,
  `shelfLife` int(4) NOT NULL,
  `itemWeight` float NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `spid` (`spid`,`gpid`,`name`,`code`),
  KEY `defaultFunction` (`defaultFunction`)
) ENGINE=MyISAM AUTO_INCREMENT=2068 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productsactivity`
--

DROP TABLE IF EXISTS `productsactivity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsactivity` (
  `paid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `rid` int(10) unsigned NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `quantity` float unsigned NOT NULL,
  `soldQty` float unsigned DEFAULT NULL,
  `turnOver` float unsigned NOT NULL,
  `turnOverOc` float unsigned NOT NULL,
  `originalCurrency` varchar(4) DEFAULT NULL,
  `quantityForecast` float unsigned NOT NULL,
  `salesForecast` float unsigned NOT NULL,
  `saleType` varchar(12) NOT NULL,
  PRIMARY KEY (`paid`),
  KEY `pid` (`pid`,`rid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=66174 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productschemsubstances`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productsegements_applications`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productsegmentcoordinators`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productsegments`
--

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

--
-- Table structure for table `reportcontributors`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=6490 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reporting_qrmarketingmatls`
--

DROP TABLE IF EXISTS `reporting_qrmarketingmatls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reporting_qrmarketingmatls` (
  `rmmid` int(11) NOT NULL AUTO_INCREMENT,
  `affid` smallint(5) NOT NULL,
  `year` tinyint(4) NOT NULL,
  `quarter` tinyint(1) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rmmid`),
  KEY `affid` (`affid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reporting_qrrecipients`
--

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
  KEY `sentBy` (`sentBy`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reporting_qrrecipients_views`
--

DROP TABLE IF EXISTS `reporting_qrrecipients_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reporting_qrrecipients_views` (
  `rqrrvid` int(10) NOT NULL AUTO_INCREMENT,
  `rqrrid` int(10) NOT NULL,
  `time` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `ipAddress` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rqrrvid`,`rqrrid`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reporting_report_summary`
--

DROP TABLE IF EXISTS `reporting_report_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reporting_report_summary` (
  `rpsid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `summary` text NOT NULL,
  PRIMARY KEY (`rpsid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(32) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=6866 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `representatives`
--

DROP TABLE IF EXISTS `representatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representatives` (
  `rpid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `email` varchar(220) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`rpid`)
) ENGINE=MyISAM AUTO_INCREMENT=173 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `representativespositions`
--

DROP TABLE IF EXISTS `representativespositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representativespositions` (
  `rppid` int(10) NOT NULL AUTO_INCREMENT,
  `rpid` int(10) NOT NULL,
  `posid` smallint(5) NOT NULL,
  PRIMARY KEY (`rppid`,`rpid`,`posid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `representativessegments`
--

DROP TABLE IF EXISTS `representativessegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representativessegments` (
  `rpsid` int(10) NOT NULL AUTO_INCREMENT,
  `rpid` int(10) NOT NULL,
  `psid` smallint(5) NOT NULL,
  PRIMARY KEY (`rpsid`,`rpid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reputation`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `saletypes`
--

DROP TABLE IF EXISTS `saletypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saletypes` (
  `stid` smallint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(220) COLLATE utf8_unicode_ci NOT NULL,
  `abbreviation` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `useLocalCurrency` tinyint(1) NOT NULL,
  PRIMARY KEY (`stid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `saletypes_invoicing`
--

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
  KEY `affid` (`affid`,`stid`,`invoiceAffid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `segapplicationfunctions`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `segmentapplications`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

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

--
-- Table structure for table `settings`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sourcing_chemicalrequests`
--

DROP TABLE IF EXISTS `sourcing_chemicalrequests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_chemicalrequests` (
  `scrid` int(10) NOT NULL AUTO_INCREMENT,
  `csid` mediumint(10) NOT NULL,
  `psaid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `timeRequested` bigint(30) NOT NULL,
  `origin` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
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

--
-- Table structure for table `sourcing_chemreqs_origins`
--

DROP TABLE IF EXISTS `sourcing_chemreqs_origins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_chemreqs_origins` (
  `scroid` int(10) NOT NULL AUTO_INCREMENT,
  `scrid` int(10) NOT NULL,
  `origin` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`scroid`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sourcing_suppliers`
--

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
  PRIMARY KEY (`ssid`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sourcing_suppliers_activityareas`
--

DROP TABLE IF EXISTS `sourcing_suppliers_activityareas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_activityareas` (
  `ssaid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `coid` int(10) NOT NULL,
  `availability` tinyint(1) NOT NULL,
  PRIMARY KEY (`ssaid`,`ssid`,`coid`)
) ENGINE=MyISAM AUTO_INCREMENT=506 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sourcing_suppliers_chemicals`
--

DROP TABLE IF EXISTS `sourcing_suppliers_chemicals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_chemicals` (
  `sscid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `csid` int(10) NOT NULL,
  `supplyType` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sscid`,`ssid`,`csid`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sourcing_suppliers_contacthist`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sourcing_suppliers_contactpersons`
--

DROP TABLE IF EXISTS `sourcing_suppliers_contactpersons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_contactpersons` (
  `sscpid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `rpid` int(11) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`sscpid`,`ssid`,`rpid`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sourcing_suppliers_genericprod`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sourcing_suppliers_productsegments`
--

DROP TABLE IF EXISTS `sourcing_suppliers_productsegments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sourcing_suppliers_productsegments` (
  `sspsid` int(10) NOT NULL AUTO_INCREMENT,
  `ssid` int(10) NOT NULL,
  `psid` int(10) NOT NULL,
  PRIMARY KEY (`sspsid`,`ssid`,`psid`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockorder`
--

DROP TABLE IF EXISTS `stockorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockorder` (
  `soid` int(10) NOT NULL AUTO_INCREMENT,
  `type` int(10) NOT NULL,
  `orderNumber` int(10) NOT NULL,
  `timeLine` bigint(30) NOT NULL,
  `affid` smallint(5) NOT NULL,
  `spid` int(10) NOT NULL,
  `currency` int(10) NOT NULL,
  `fxUSD` float NOT NULL,
  `warehouseUnit` tinyint(1) NOT NULL,
  `warehouseUnitSize` float NOT NULL,
  `incoTerms` float NOT NULL,
  `incoTermsLocation` int(10) NOT NULL DEFAULT '0',
  `paymentTermsDays` bigint(30) NOT NULL,
  `paymentTermsFrom` bigint(30) NOT NULL,
  `expectedShippingDate` bigint(30) NOT NULL,
  `daysToDeliver` int(40) NOT NULL,
  `customerPaymentDate` bigint(30) NOT NULL,
  `supplierPaymentDate` bigint(30) NOT NULL,
  `financeManager` int(10) NOT NULL,
  `generalManager` int(10) NOT NULL,
  `regionalManager` int(10) NOT NULL,
  `submittedBy` int(10) NOT NULL,
  `preparedBy` int(10) NOT NULL,
  PRIMARY KEY (`soid`),
  KEY `affid` (`affid`,`spid`,`preparedBy`),
  KEY `incoTermsLocation` (`incoTermsLocation`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockorder_customers`
--

DROP TABLE IF EXISTS `stockorder_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockorder_customers` (
  `socid` int(10) NOT NULL AUTO_INCREMENT,
  `soid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  `paymentTermsDays` bigint(30) NOT NULL DEFAULT '0',
  `paymentTermsFrom` bigint(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`socid`,`soid`,`cid`),
  KEY `eid` (`cid`),
  KEY `soid` (`soid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockorder_customers_products`
--

DROP TABLE IF EXISTS `stockorder_customers_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockorder_customers_products` (
  `scpid` int(10) NOT NULL AUTO_INCREMENT,
  `socid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `firstOrderQty` float NOT NULL,
  `firstOrderDate` bigint(30) NOT NULL,
  `numOrders` tinyint(2) NOT NULL,
  `quantityPerNextOrder` float NOT NULL,
  `nextOrdersInterval` tinyint(3) NOT NULL,
  PRIMARY KEY (`scpid`,`socid`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockorder_products`
--

DROP TABLE IF EXISTS `stockorder_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockorder_products` (
  `sopid` int(10) NOT NULL AUTO_INCREMENT,
  `soid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `packing` int(10) NOT NULL,
  `packingWeight` float NOT NULL,
  `quantity` float NOT NULL,
  `daysInStock` int(30) NOT NULL,
  `clearingFees` float NOT NULL,
  `lcFees` float NOT NULL,
  `purchasePrice` float NOT NULL,
  `sellingPrice` float NOT NULL,
  PRIMARY KEY (`sopid`,`soid`,`pid`),
  KEY `pid` (`pid`),
  KEY `soid` (`soid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliersaudits`
--

DROP TABLE IF EXISTS `suppliersaudits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliersaudits` (
  `said` int(10) NOT NULL AUTO_INCREMENT,
  `eid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`said`),
  KEY `eid` (`eid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys_associations`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys_categories`
--

DROP TABLE IF EXISTS `surveys_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_categories` (
  `scid` smallint(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`scid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys_invitations`
--

DROP TABLE IF EXISTS `surveys_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_invitations` (
  `siid` int(10) NOT NULL AUTO_INCREMENT,
  `sid` int(10) NOT NULL,
  `invitee` varchar(220) NOT NULL,
  `identifier` varchar(20) NOT NULL,
  `isDone` tinyint(1) DEFAULT NULL,
  `timeDone` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`siid`,`sid`,`invitee`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys_questiontypes`
--

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

--
-- Table structure for table `surveys_responses`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys_templates`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys_templates_questions`
--

DROP TABLE IF EXISTS `surveys_templates_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_templates_questions` (
  `stqid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stsid` int(10) NOT NULL,
  `question` varchar(220) NOT NULL,
  `description` text,
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
) ENGINE=MyISAM AUTO_INCREMENT=121 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys_templates_questions_choices`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=2111 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveys_templates_sections`
--

DROP TABLE IF EXISTS `surveys_templates_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveys_templates_sections` (
  `stsid` int(10) NOT NULL AUTO_INCREMENT,
  `stid` mediumint(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`stsid`),
  KEY `stid` (`stid`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_languages`
--

DROP TABLE IF EXISTS `system_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_languages` (
  `slid` tinyint(10) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `version` float NOT NULL,
  `rtl` tinyint(1) NOT NULL,
  `htmllang` varchar(3) NOT NULL,
  `charset` varchar(10) NOT NULL,
  `author` int(10) NOT NULL,
  PRIMARY KEY (`slid`),
  KEY `author` (`author`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_languages_varvalues`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=1835 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_langvariables`
--

DROP TABLE IF EXISTS `system_langvariables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_langvariables` (
  `slvid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `fileName` varchar(150) NOT NULL,
  `isFrontEnd` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`slvid`)
) ENGINE=MyISAM AUTO_INCREMENT=1303 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) CHARACTER SET latin1 NOT NULL,
  `template` text CHARACTER SET latin1 NOT NULL,
  `date` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=290 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `travelmanager_airlinerflights`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `travelmanager_airlines`
--

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

--
-- Table structure for table `travelmanager_airports`
--

DROP TABLE IF EXISTS `travelmanager_airports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travelmanager_airports` (
  `apid` int(10) NOT NULL AUTO_INCREMENT,
  `ciid` int(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`apid`),
  KEY `ciid` (`ciid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `travelmanager_flightrates`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uom`
--

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

--
-- Table structure for table `usergroups`
--

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
  `canExcludeFillStages` int(1) NOT NULL DEFAULT '0',
  `reporting_canApproveReports` int(1) NOT NULL DEFAULT '0',
  `reporting_canViewComptInfo` tinyint(1) NOT NULL DEFAULT '0',
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
  `calendar_canAddPublicEvents` tinyint(1) NOT NULL DEFAULT '0',
  `calendar_canPublishEvents` tinyint(1) NOT NULL DEFAULT '0',
  `canUseStock` tinyint(1) NOT NULL DEFAULT '0',
  `stock_canOrderStock` tinyint(1) NOT NULL DEFAULT '0',
  `canUseReputation` int(1) NOT NULL DEFAULT '0',
  `reputation_canAddLink` int(1) NOT NULL DEFAULT '0',
  `canUseSurveys` tinyint(1) NOT NULL DEFAULT '0',
  `surveys_canCreateSurvey` tinyint(1) NOT NULL DEFAULT '0',
  `canUseDevelopment` tinyint(4) NOT NULL DEFAULT '0',
  `admin_canModifyLangFiles` tinyint(1) NOT NULL DEFAULT '0',
  `admin_canCreateLangFiles` tinyint(1) NOT NULL DEFAULT '0',
  `cms_canAddNews` tinyint(1) NOT NULL DEFAULT '0',
  `canUseSourcing` tinyint(1) NOT NULL DEFAULT '0',
  `sourcing_canManageEntries` tinyint(1) NOT NULL DEFAULT '0',
  `sourcing_canListSuppliers` tinyint(1) NOT NULL DEFAULT '0',
  `sourcing_canViewKPIs` tinyint(1) NOT NULL DEFAULT '0',
  `canUseAssets` tinyint(1) NOT NULL DEFAULT '0',
  `assets_canTrackAssets` tinyint(1) NOT NULL DEFAULT '0',
  `assets_canManageAssets` tinyint(1) NOT NULL DEFAULT '0',
  `canUseBudgeting` tinyint(1) NOT NULL DEFAULT '0',
  `budgeting_canFillBudget` tinyint(1) NOT NULL DEFAULT '0',
  `canUseMeetings` tinyint(1) NOT NULL DEFAULT '0',
  `meetings_canCreateMeeting` tinyint(1) NOT NULL DEFAULT '0',
  `profiles_canAddMkIntlData` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userhrinformation`
--

DROP TABLE IF EXISTS `userhrinformation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userhrinformation` (
  `uhrid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `employeeNum` varchar(6) NOT NULL,
  `legalAffid` varchar(220) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `birthDate` bigint(30) NOT NULL,
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
  `bankAccountNumber` varchar(20) NOT NULL,
  `iban` varchar(50) NOT NULL,
  `taxInfo` int(10) NOT NULL,
  `socialSecurityNumber` varchar(10) NOT NULL,
  `managementComments` text NOT NULL,
  PRIMARY KEY (`uhrid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=146 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

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
  `email2` varchar(220) DEFAULT NULL,
  `skype` varchar(200) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `telephoneExtension` smallint(4) DEFAULT NULL,
  `telephone2` varchar(20) DEFAULT NULL,
  `telephone2Extension` smallint(4) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `mobileIsPrivate` tinyint(1) NOT NULL DEFAULT '0',
  `mobile2` varchar(20) NOT NULL,
  `mobile2IsPrivate` tinyint(1) NOT NULL DEFAULT '0',
  `internalExtension` smallint(6) DEFAULT NULL,
  `bbPin` varchar(8) NOT NULL,
  `poBox` int(10) DEFAULT NULL,
  `profilePicture` varchar(200) DEFAULT NULL,
  `newFilesNotification` tinyint(1) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `assistant` (`assistant`)
) ENGINE=MyISAM AUTO_INCREMENT=316 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_passwordarchive`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_usergroups`
--

DROP TABLE IF EXISTS `users_usergroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_usergroups` (
  `ugid` int(10) NOT NULL AUTO_INCREMENT,
  `gid` smallint(5) NOT NULL,
  `uid` int(10) NOT NULL,
  `isMain` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ugid`,`gid`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=343 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usersemails`
--

DROP TABLE IF EXISTS `usersemails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usersemails` (
  `ueid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `email` varchar(220) NOT NULL,
  PRIMARY KEY (`ueid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userspositions`
--

DROP TABLE IF EXISTS `userspositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userspositions` (
  `upid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `posid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`upid`,`uid`,`posid`),
  KEY `uid` (`uid`,`posid`)
) ENGINE=MyISAM AUTO_INCREMENT=261 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitreports`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=233 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitreports_comments`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitreports_competition`
--

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

--
-- Table structure for table `visitreports_productlines`
--

DROP TABLE IF EXISTS `visitreports_productlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitreports_productlines` (
  `plid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vrid` int(10) unsigned NOT NULL,
  `productLine` int(10) unsigned NOT NULL,
  PRIMARY KEY (`plid`,`vrid`,`productLine`),
  KEY `vrid` (`vrid`,`productLine`)
) ENGINE=MyISAM AUTO_INCREMENT=145 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitreports_reportsuppliers`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workshifts`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-14  1:10:52
