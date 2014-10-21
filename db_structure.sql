-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 28, 2011 at 02:44 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `newtube`
--

-- --------------------------------------------------------

--
-- Table structure for table `encode_jobs`
--

CREATE TABLE IF NOT EXISTS `encode_jobs` (
  `jid` int(11) NOT NULL auto_increment,
  `show` varchar(255) NOT NULL,
  `snum` int(11) NOT NULL,
  `filepath` varchar(1024) NOT NULL,
  `tmp_description` varchar(1024) NOT NULL,
  `curfile` varchar(1024) NOT NULL,
  `completed` int(2) NOT NULL,
  PRIMARY KEY  (`jid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=572 ;

-- --------------------------------------------------------

--
-- Table structure for table `episodes`
--

CREATE TABLE IF NOT EXISTS `episodes` (
  `eid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `edescription` varchar(1024) NOT NULL,
  `sid` int(11) NOT NULL,
  `seasonid` int(11) NOT NULL,
  `filename` varchar(1024) NOT NULL,
  PRIMARY KEY  (`eid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6172 ;

-- --------------------------------------------------------

--
-- Table structure for table `iplog`
--

CREATE TABLE IF NOT EXISTS `iplog` (
  `logid` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `fail` tinyint(4) NOT NULL,
  PRIMARY KEY  (`logid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8334 ;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
  `mid` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `filename` varchar(1024) NOT NULL,
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE IF NOT EXISTS `playlist` (
  `peid` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `vidid` int(11) NOT NULL,
  PRIMARY KEY  (`peid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2436 ;

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE IF NOT EXISTS `seasons` (
  `seasonid` int(11) NOT NULL auto_increment,
  `sid` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `sdescription` varchar(255) NOT NULL,
  PRIMARY KEY  (`seasonid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=425 ;

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE IF NOT EXISTS `shows` (
  `sid` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY  (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=132 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscribedShows`
--

CREATE TABLE IF NOT EXISTS `subscribedShows` (
  `subShowID` int(11) NOT NULL auto_increment,
  `subShowTitleID` int(11) NOT NULL,
  `subShowSeason` tinyint(2) unsigned zerofill NOT NULL,
  PRIMARY KEY  (`subShowID`),
  KEY `subShowTitleID` (`subShowTitleID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `torrents`
--

CREATE TABLE IF NOT EXISTS `torrents` (
  `torrentID` int(11) NOT NULL auto_increment,
  `subShowID` int(11) NOT NULL,
  `torrentTitle` varchar(255) NOT NULL,
  `torrentFile` varchar(255) NOT NULL,
  `torrentStage` tinyint(2) NOT NULL,
  `torrentInfoHash` varchar(40) NOT NULL,
  `torrentEncodeID` int(11) NOT NULL,
  PRIMARY KEY  (`torrentID`),
  KEY `subShowID` (`subShowID`),
  KEY `torrentEncodeID` (`torrentEncodeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL auto_increment,
  `username` varchar(32) NOT NULL,
  `password` char(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `perms` tinyint(1) NOT NULL,
  `homepath` varchar(255) NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=114 ;

-- --------------------------------------------------------

--
-- Table structure for table `vidlog`
--

CREATE TABLE IF NOT EXISTS `vidlog` (
  `vidlogid` bigint(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `vidid` int(11) NOT NULL,
  `quality` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`vidlogid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11085 ;

