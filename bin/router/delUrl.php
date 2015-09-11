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

if (!class_exists("commandDelUrl")) {
    class commandDelUrl extends driverCommand {

        public static function runMe(&$params, $debug = true) {
            if ($params["url"] == "") {
                return;
            } else {
                $sql = "SELECT `id` FROM `url_rewrite` where `url` = '{$params["url"]}'";
                $q = dbConn::Execute($sql);
                if (!$q->EOF) {
                    $sql = "delete FROM `url_rewrite` where `id` = '{$q->fields["id"]}'";
                    dbConn::Execute($sql);
                }
            }
        }
        
        public static function getAccess($ignore = "") {
            $me = __FILE__;
            return parent::getAccess($me);
        }

        public static function getHelp() {
            return array(
                "package" => 'core',
                "description" => __("Del URL to the rewrite list."), 
                "parameters" => array(
                    "url" => __("The URL to erase, relative at root. Ex. home to http://127.0.0.1/home"),
                ),
                "response" => array(),
                "type" => array(
                    "parameters" => array(
                        "url" => "string",
                    ), 
                    "response" => array(),
                ),
                "echo" => false
            );
        }
    }
}
return new commandDelUrl();