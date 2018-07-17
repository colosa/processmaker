# Database soapinterop running on localhost

# phpMyAdmin MySQL-Dump
# version 2.2.5
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# Host: localhost
# Generation Time: Aug 31, 2002 at 06:36 PM
# Server version: 3.23.49
# PHP Version: 4.2.1
# Database : `soapinterop`
# --------------------------------------------------------

#
# Table structure for table `clientinfo`
#

CREATE TABLE clientinfo (
  id char(40) NOT NULL default '',
  name char(100) NOT NULL default '',
  version char(20) NOT NULL default '',
  resultsURL char(255) NOT NULL default ''
) TYPE=InnoDB;
# --------------------------------------------------------

#
# Table structure for table `results`
#

CREATE TABLE results (
  id int(11) NOT NULL auto_increment,
  client varchar(100) NOT NULL default '0',
  endpoint int(11) NOT NULL default '0',
  stamp int(11) NOT NULL default '0',
  class varchar(50) NOT NULL default '',
  type varchar(10) default NULL,
  wsdl int(11) NOT NULL default '0',
  function varchar(255) NOT NULL default '',
  result varchar(25) NOT NULL default '',
  error text,
  wire text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=InnoDB;
# --------------------------------------------------------

#
# Table structure for table `serverinfo`
#

CREATE TABLE serverinfo (
  id int(11) NOT NULL auto_increment,
  service_id char(40) NOT NULL default '',
  name char(100) NOT NULL default '',
  version char(20) NOT NULL default '',
  endpointURL char(255) NOT NULL default '',
  wsdlURL char(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=InnoDB;
# --------------------------------------------------------

#
# Table structure for table `services`
#

CREATE TABLE services (
  id char(40) NOT NULL default '',
  name char(50) NOT NULL default '',
  description char(255) NOT NULL default '',
  wsdlURL char(255) NOT NULL default '',
  websiteURL char(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=InnoDB;

    

