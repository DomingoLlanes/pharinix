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
if (!defined("CMS_VERSION")) { header("HTTP/1.0 404 Not Found"); die(""); }

if (!class_exists("commandChownNode")) {
    class commandChownNode extends driverCommand {

        public static function runMe(&$params, $debug = true) {
            $params = array_merge(array(
                "nodetype" => "",
                "nid" => null,
                "owner" => null, // To detect defaults
                "group" => null,
            ), $params);
            $nodetype = null;
            $owner = null;
            $group = null;
            if ($params["owner"] == 0) {
                $owner = 0;
            }
            if ($params["group"] == 0) {
                $group = 0;
            }
            // Detect wrong values
            if ($params["nodetype"] != null) {
                if (!is_int($params["nodetype"])) {
                    // Node type is a name
                    $resp = driverCommand::run("getNodeTypeId", array(
                        "name" => $params["nodetype"],
                    ));
                    if ($resp === false) {
                        return array("ok" => false, "msg" => "Bad node type id.");
                    } else {
                        $nodetype = $params["nodetype"];
                    }
                } else {
                    // Node type is a ID
                    $sql = "SELECT * FROM `node_type` where `id` = ".$params["nodetype"];
                    $q = dbConn::Execute($sql);
                    if ($q->EOF) {
                        return array("ok" => false, "msg" => "Bad node type id.");
                    }
                    $nodetype = $q->fields["name"];
                }
            } else {
                return array("ok" => false, "msg" => "Node type is required.");
            }
            if ($params["owner"] != null && $params["owner"] != "0") {
                if (!is_int($params["owner"])) {
                    // Owner is a mail
                    $resp = driverCommand::run("getNodes", array(
                        "nodetype" => "user",
                        "fields" => "id",
                        "where" => "`mail` = '".$params["owner"]."'",
                    ));
                    if (count($resp) == 0) {
                        return array("ok" => false, "msg" => "Bad user id.");
                    } else {
                        $ids = array_keys($resp);
                        $owner = $ids[0];
                    }
                } else {
                    // Owner is a ID
                    $resp = driverCommand::run("getNode", array(
                        "nodetype" => "user",
                        "node" => $params["owner"],
                    ));
                    if (count($resp) == 0) {
                        return array("ok" => false, "msg" => "Bad user id.");
                    }
                    $owner = $params["owner"];
                }
            }
            if ($params["group"] != null && $params["group"] != "0") {
                if (!is_int($params["group"])) {
                    // group is a title
                    $resp = driverCommand::run("getNodes", array(
                        "nodetype" => "group",
                        "fields" => "id",
                        "where" => "`title` = '".$params["group"]."'",
                    ));
                    if (count($resp) == 0) {
                        return array("ok" => false, "msg" => "Bad group id.");
                    } else {
                        $ids = array_keys($resp);
                        $group = $ids[0];
                    }
                } else {
                    // group is a ID
                    $resp = driverCommand::run("getNode", array(
                        "nodetype" => "group",
                        "node" => $params["group"],
                    ));
                    if (count($resp) == 0) {
                        return array("ok" => false, "msg" => "Bad group id.");
                    }
                    $group = $params["group"];
                }
            }
            //Change
            if ($params["nid"] == null) {
                // Change node type
                $can = driverCommand::run("getNodeTypeDef", array(
                    "nodetype" => $nodetype,
                ));
                if (driverUser::getID() == 0 || driverUser::getID() == $accessData["user_owner"]) {
                    // Calculate the new ownership
                    if ($params["owner"] != null) {
                        $can["user_owner"] = $owner;
                    }
                    if ($params["group"] != null) {
                        $can["group_owner"] = $group;
                    }
                    // Change ownership
                    $sql = "update `node_type` set ";
                    $sql .= "`user_owner` = {$can["user_owner"]}, ";
                    $sql .= "`group_owner` = {$can["group_owner"]}, ";
                    $sql .= "`access` = {$can["access"]} ";
                    $sql .= "where `name` = '".$params["nodetype"]."'";
                    dbConn::Execute($sql);

                    $resp = array("ok" => true);
                } else {
                    $resp = array("ok" => false, "msg" => "You need ownership.");
                }
            } else {
                // Change node
                $can = driverCommand::run("getNodes", array(
                    "nodetype" => $nodetype,
                    "fields" => "group_owner,user_owner,access",
                    "where" => "`id` = ".$params["nid"],
                ));
                if (count($can) > 0) {
                    $can = $can[$params["nid"]];
                    $ids = array_keys($can);
                    // Calculate the new ownership
                    if ($params["owner"] != null) {
                        $can["user_owner"] = $owner;
                    }
                    if ($params["group"] != null) {
                        $can["group_owner"] = $group;
                    }
                    // Change ownership
                    $resp = driverCommand::run("updateNode", array(
                        "nodetype" => $nodetype,
                        "user_owner" => $can["user_owner"],
                        "group_owner" => $can["group_owner"],
                        "nid" => $params["nid"],
                    ));
                    if (isset($resp["ok"]) && $resp["ok"] === false) {
                        return $resp;
                    }
                    $resp = array("ok" => true);
                } else {
                    $resp = array("ok" => false, "msg" => "Unknow node or you can't read.");
                }
            }
            return $resp;
        }

        public static function getHelp() {
            return array(
                "description" => "To change owner, and/or group, of the node types or nodes.", 
                "parameters" => array(
                    "nodetype" => "Node type that you need change ownership or type of the node to change.",
                    "nid" => "Node ID of the node that you need change. Optional, if it's set try change a node, else try change a node type.",
                    "owner" => "Mail of the user or ID of the new owner. If it's null, or it is not set, command don't change it, to set to root you must value how 0, zero.",
                    "group" => "Title or ID of the group. If it's null, or it is not set, command don't change it, to set to root you must value how 0, zero.",
                ), 
                "response" => array(
                    "ok" => "TRUE if changed."
                )
            );
        }
        
        public static function getAccess($ignore = "") {
            $me = __FILE__;
            return parent::getAccess($me);
        }
        
        public static function getAccessFlags() {
            return driverUser::PERMISSION_FILE_ALL_EXECUTE;
        }
    }
}
return new commandChownNode();