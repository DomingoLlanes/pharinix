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

if (!class_exists("commandDelBooting")) {
    class commandDelBooting extends driverCommand {

        public static function runMe(&$params, $debug = true) {
            $params = array_merge(array(
                    "uid" => "",
                ), $params);
            $sql = "delete from `booting` where `ref` = '".$params["uid"]."'";
            dbConn::Execute($sql);
        }

        public static function getAccess($ignore = "") {
            $me = __FILE__;
            return parent::getAccess($me);
        }
        
        public static function getHelp() {
            return array(
                "package" => 'core',
                "description" => __("Delete a command from boot process."), 
                "parameters" => array(
                    "uid" => __("Reference ID of the boot command."),
                ), 
                "response" => array(),
                "type" => array(
                    "parameters" => array(
                        "uid" => "string",
                    ), 
                    "response" => array(),
                ),
                "echo" => false
            );
        }
    }
}
return new commandDelBooting();