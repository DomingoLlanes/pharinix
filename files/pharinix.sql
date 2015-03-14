-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.6.20


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema miana
--

CREATE DATABASE IF NOT EXISTS miana;
USE miana;
CREATE TABLE `bin-path` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
INSERT INTO `bin-path` VALUES  (1,'bin/'),
 (2,'bin/html/'),
 (3,'usr/bootstrap-form-builder/bin/'),
 (4,'bin/node_type/');
CREATE TABLE `node_algo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pass` varchar(250) DEFAULT NULL,
  `modifier` int(10) unsigned DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `creator` int(10) unsigned DEFAULT '0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
INSERT INTO `node_algo` VALUES  (1,'aaa',0,'2015-03-13 00:00:00',0,'2015-03-13 00:00:00');
CREATE TABLE `node_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `created` datetime NOT NULL,
  `creator_node_user` int(10) unsigned NOT NULL COMMENT '''User ID''',
  `modified` datetime NOT NULL,
  `modifier_node_user` int(10) unsigned NOT NULL COMMENT '''User ID''',
  `locked` varchar(1) DEFAULT '0' COMMENT 'System node type',
  `label_field` varchar(250) NOT NULL COMMENT 'Field that I can use to list',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1752 DEFAULT CHARSET=latin1;
INSERT INTO `node_type` VALUES  (977,'algo','2015-03-01 18:56:30',0,'2015-03-13 19:01:41',0,'1','pass');
CREATE TABLE `node_type_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `type` varchar(250) NOT NULL,
  `len` int(10) unsigned NOT NULL,
  `required` varchar(1) NOT NULL DEFAULT '0' COMMENT 'Field required',
  `readonly` varchar(1) NOT NULL DEFAULT '0' COMMENT 'Not writeble field',
  `locked` varchar(1) NOT NULL DEFAULT '0' COMMENT 'The cant be erased of the type',
  `node_type` int(10) unsigned NOT NULL,
  `default` longtext NOT NULL COMMENT 'Default value',
  `label` varchar(250) NOT NULL,
  `help` longtext NOT NULL,
  `multi` varchar(1) NOT NULL DEFAULT '0' COMMENT 'Multivalue',
  `iskey` varchar(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8180 DEFAULT CHARSET=latin1;
INSERT INTO `node_type_field` VALUES  (5078,'pass','password',250,'0','0','0',977,'','Password','Secutiry ID','0','0'),
 (4462,'created','datetime',0,'0','0','1',977,'','Creation date','','0','0'),
 (4463,'creator','user',0,'0','0','1',977,'0','User creator','','0','0'),
 (4464,'modified','datetime',0,'0','0','1',977,'','Modified date','','0','0'),
 (4465,'modifier','user',0,'0','0','1',977,'0','Modifier user','','0','0');
CREATE TABLE `page-blocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idpage` int(10) unsigned NOT NULL,
  `idcol` varchar(250) NOT NULL,
  `command` varchar(250) NOT NULL,
  `parameters` longtext NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=800 DEFAULT CHARSET=latin1;
INSERT INTO `page-blocks` VALUES  (20,9,'content','getNodeTypeDefHtml','nodetype=algo',0),
 (9,1,'colRight','executeCommandOnline','',0),
 (10,0,'footCopy','echoHTML','html=%3Ch3%3EPharinix+Copyright+%C2%A9+%3C%3Fphp+echo+date%28%22Y%22%29%3B+%3F%3E+Pedro+Pelaez%3C%2Fh3%3E%0D%0A%3Cdiv%3EThis+program+is+free+software%3B+you+can+redistribute+it+and%2For+modify+it+under+the+terms+of+the+GNU+General+Public+License+as+published+by+the+Free+Software+Foundation%3B+either+version+2+of+the+License%2C+or+%28at+your+option%29+any+later+version.%3Cbr%2F%3E%0D%0A%3Cbr%2F%3E%0D%0AThis+program+is+distributed+in+the+hope+that+it+will+be+useful%2C+but+WITHOUT+ANY+WARRANTY%3B+without+even+the+implied+warranty+of+MERCHANTABILITY+or+FITNESS+FOR+A+PARTICULAR+PURPOSE.++See+the+GNU+General+Public+License+for+more+details.%3Cbr%2F%3E%0D%0A%3Cbr%2F%3E%0D%0AYou+should+have+received+a+copy+of+the+GNU+General+Public+License+along+with+this+program%3B+if+not%2C+write+to+the+Free+Software+Foundation%2C+Inc.%2C+59+Temple+Place+-+Suite+330%2C+Boston%2C+MA++02111-1307%2C+USA.%0D%0A%3C%2Fdiv%3E',0),
 (12,3,'content','echoHTML','html=<h1>Page not found: <?php echo \"\'{$_GET[\"rewrite\"]}\'\";?></h1>',0),
 (14,1,'colLeft','textUrlEncoder','',0),
 (13,4,'content','commandHelp',' ',0),
 (21,10,'content','templateEditor','',0),
 (22,11,'content','iconsList','',0),
 (764,744,'content','getNodeHtml','nodetype=testtype&node=1',0),
 (797,744,'content','getNodeHtml','nodetype=testtype&node=1',0),
 (428,1,'colLeft','manHelpOnline','',1),
 (762,744,'content','getNodeHtml','nodetype=testtype&node=1',0),
 (799,744,'content','getNodeHtml','nodetype=testtype&node=1',0);
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `template` varchar(250) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` longtext NOT NULL,
  `keys` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=779 DEFAULT CHARSET=latin1;
INSERT INTO `pages` VALUES  (1,'home','etc/templates/pages/two_column.xml','Home','',''),
 (4,'help/command','etc/templates/pages/default.xml','Command\'s help','',''),
 (3,'404','etc/templates/pages/default.xml','Page not found','',''),
 (9,'node_type_algo','etc/templates/pages/default.xml','algo node type','',''),
 (10,'template/editor','etc/templates/pages/default.xml','Template editor','',''),
 (11,'help/icons','etc/templates/pages/default.xml','Icons list','',''),
 (744,'node_type_testtype_1','etc/templates/pages/default.xml','Node 1','','');
CREATE TABLE `url_rewrite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` longtext,
  `rewriteto` longtext,
  PRIMARY KEY (`id`),
  KEY `url` (`url`(250))
) ENGINE=MyISAM AUTO_INCREMENT=793 DEFAULT CHARSET=latin1;
INSERT INTO `url_rewrite` VALUES  (1,'home','command=pageToHTML&page=home'),
 (12,'node/type/algo','command=pageToHTML&page=node_type_algo'),
 (3,'help/command','command=pageToHTML&page=help/command'),
 (13,'template/editor','command=pageToHTML&page=template/editor'),
 (14,'help/icons','command=pageToHTML&page=help/icons'),
 (757,'node/type/testtype/1','command=pageToHTML&page=node_type_testtype_1');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
