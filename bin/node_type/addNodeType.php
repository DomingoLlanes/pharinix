<?php

/*
 * Copyright (C) 2015 Pedro Pelaez <aaaaa976@gmail.com>
 * Sources https://github.com/PSF1/pharinix
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
if (!defined("CMS_VERSION")) {
    header("HTTP/1.0 404 Not Found");
    die("");
}

//TODO: Securizate access
/*
 * Add a new node type
 * Parameters:
 * name = Node type name
 * 
 * CREATE TABLE `node_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `created` datetime NOT NULL,
  `creator_node_user` int(10) unsigned NOT NULL COMMENT '''User ID''',
  `modified` datetime NOT NULL,
  `modifier_node_user` int(10) unsigned NOT NULL COMMENT '''User ID''',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
 */

$resp = array("ok" => false, "nid" => 0, "msg" => "");

// Default values
$params = array_merge(array(
            "name" => "",
          ), $params);
if ($params["name"] == "") {
    $resp["msg"] = "Node type name is required. ";
}
if ($resp["msg"] != "") return $resp;

// We dont like various types with some name
$sql = "select id from `node_type` where name = '{$params["name"]}'";
$q = dbConn::get()->Execute($sql);

if ($q->EOF) {
    // Insert the new type
    $sql = "insert into `node_type` set name = '{$params["name"]}', created = NOW(), modified = NOW()";
    dbConn::get()->Execute($sql);
    $id = dbConn::lastID();
    // Add default fields
    $nField = array(
        "name" => "created",
        "type" => "datetime",
        "len" => 0,
        "required" => false,
        "readonly" => false,
        "locked" => true,
        "node_type" => $id,
        "default" => "",
        );
    driverCommand::run("addNodeField", $nField);
    $nField = array(
        "name" => "creator",
        "type" => "node_user",
        "len" => 0,
        "required" => false,
        "readonly" => false,
        "locked" => true,
        "node_type" => $id,
        "default" => "",
        );
    driverCommand::run("addNodeField", $nField);
    $nField = array(
        "name" => "modified",
        "type" => "datetime",
        "len" => 0,
        "required" => false,
        "readonly" => false,
        "locked" => true,
        "node_type" => $id,
        "default" => "",
        );
    driverCommand::run("addNodeField", $nField);
    $nField = array(
        "name" => "modifier",
        "type" => "node_user",
        "len" => 0,
        "required" => false,
        "readonly" => false,
        "locked" => true,
        "node_type" => $id,
        "default" => "",
        );
    driverCommand::run("addNodeField", $nField);
    
    $resp["ok"] = true;
    $resp["nid"] = $id;
} else {
    $resp["ok"] = false;
    $resp["msg"] = "Node type '{$params["name"]}' already exist.";
}

return $resp;