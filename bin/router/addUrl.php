<?php

/* 
 * Pharinix Copyright (C) 2015 Pedro Pelaez <aaaaa976@gmail.com>
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

if (!class_exists("commandAddUrl")) {
    class commandAddUrl extends driverCommand {

        public static function runMe(&$params, $debug = true) {
            $params = array_merge(array(
                "url" => "",
                "cmd" => "",
                "mode" => "",
                "priority" => 0
            ), $params);
            
            $resp = array("ok" => false, "msg" => "");
            if ($params["url"] == "" || $params["cmd"] == "") {
                $resp["msg"] = __("URL or CMD is empty.");
            } else {
                $sql = "SELECT `id` FROM `url_rewrite` where `url` = '{$params["url"]}'";
                $q = dbConn::Execute($sql);
                if (!$q->EOF) {
                    $resp["msg"] = __("URL just exists.");
                } else {
                    $sql = "insert into `url_rewrite` set `url` = '{$params["url"]}', `rewriteto` = '{$params["cmd"]}'";
                    
                    // Check 'mode' from parameters
                    if ($params['mode'] === "r" || $params['mode'] === "m") {
                        $sql .= ", `type` = '{$params['mode']}'";
                    }
                    // Check 'priority' from parameters
                    if (is_numeric($params['priority']) && $params['priority'] > 0 && $params['priority'] <= 100) {
                        $sql .= ", `priority` = '{$params['priority']}'";
                    }
                    
                    dbConn::Execute($sql);
                    $resp["ok"] = true;
                }
            }
            
            return $resp;
        }

        public static function getAccess($ignore = "") {
            $me = __FILE__;
            return parent::getAccess($me);
        }
        
        public static function getHelp() {
            return array(
                "package" => 'core',
                "description" => __("Add a new URL to the rewrite list."), 
                "parameters" => array(
                    "url" => __("The new URL, relative at root. Ex. home to http://127.0.0.1/home"), 
                    "cmd" => __("POST's encoded string with command and parameters. Ex. command=pageToHTML&page=home"),
                    "mode" => __("Rewrite mode. 'r' => simple rewrite, without params interpreter, 'm' => params interpreter. Optional, 'r' by default."),
                    "priority" => __("Priority. Order of the rewrite rule, number between 0 and 100. Optional, 0 by default. 0 is the highest.")
                ), 
                "response" => array(
                    "ok" => __("TRUE if new URL added."), 
                    "msg" => __("If FALSE contain the error message.")
                    ),
                "type" => array(
                    "parameters" => array(
                        "url" => "string", 
                        "cmd" => "string",
                        "mode" => "string",
                        "priority" => "integer"
                    ), 
                    "response" => array(
                        "ok" => "boolean", 
                        "msg" => "string"
                        ),
                ),
                "echo" => false
            );
        }
    }
}
return new commandAddUrl();
