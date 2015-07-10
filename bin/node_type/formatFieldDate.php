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

if (!class_exists("commandFormatFieldDate")) {
    class commandFormatFieldDate extends driverCommand {
        protected static $firstHtmltextWrite = true;
        
        public static function runMe(&$params, $debug = true) {
            $p = array_merge(array(
                    "fieldname" => "",
                    "toread" => false,
                    "towrite" => false,
                    "value" => "",
                    "length" => 0,
                    "required" => false,
                    "readonly" => false,
                    "system" => false,
                    "multivalued" => false,
                    "default" => "",
                    "label" => "",
                    "help" => "",
                ), $params);
            
            if ($p["toread"] == $p["towrite"]) {
                echo self::getAlert("Object of call must be read or write.");
            } else {
                if ($p["multivalued"]) {
                    // Basic types dont have multivalue.
                } else {
                    if ($p["toread"] || $p["readonly"]) { // to read
                        echo '<!-- Field "'.$p["fieldname"].'" -->';
                        echo '<div class="col-md-12 col-sm-12 col-xs-12">';
                        echo '<div class="form-group">';
                        echo '<label class="control-label" for="'.$p["fieldname"].'">';
                        echo $p["label"];
                        echo '</label>';
                        echo '<div id="'.$p["fieldname"].'">'.$p["value"].'</div>';
                        echo '</div>';
                        echo '</div>';
                    } else { // to write
                        if ($p["value"] == "") {
                            $p["value"] = $p["default"];
                        }
                        if (self::$firstHtmltextWrite) {
                            //TODO: Quit this and send by other way...
                            echo '<script src="'.CMS_DEFAULT_URL_BASE.'/usr/datepicker/js/bootstrap-datepicker.js"></script>';
                            echo '<link rel="stylesheet" href="'.CMS_DEFAULT_URL_BASE.'/usr/datepicker/css/datepicker.css" />';
                            self::$firstHtmltextWrite = false;
                        }
                        echo '<!-- Field "'.$p["fieldname"].'" -->';
                        echo '<div class="col-md-12 col-sm-12 col-xs-12">';
                        echo '<div class="form-group">';
                        echo '<label class="control-label" for="'.$p["fieldname"].'">';
                        echo $p["label"];
                        if ($p["required"]) {
                            echo '&nbsp;<span class="glyphicon glyphicon-asterisk text-danger" aria-hidden="true"></span>';
                        }
                        echo '</label>';
                        echo '<input id="'.$p["fieldname"].'" name="'.$p["fieldname"].
                                '" type="text" placeholder="'.$p["default"].'" '.
                                'class="form-control " '.($p["required"]?"required":"").
                                ' value="'.$p["value"].'"/>';
                        echo "<div class=\"help help-block\">".$p["help"]."</div>";
                        echo '</div>';
                        echo '</div>';
                        $js = <<<EOT
$('#{$p["fieldname"]}').datepicker({
        format: 'dd-mm-yyyy'
    });
EOT;
                        $reg = &self::getRegister("customscripts");
                        $reg .= $js;
                    }
                }
                
            }
        }

        public static function getHelp() {
            return array(
                "description" => "Format datetime field to read or write how date.", 
                "parameters" => array(
                    "fieldname" => "Field name to the form control.",
                    "toread" => "Caller need a read form.",
                    "towrite" => "Caller need a write form.",
                    "value" => "Field value.",
                    "length" => "Field max length.",
                    "required" => "Is a required field.",
                    "readonly" => "Is a read only field.",
                    "system" => "Is a system field, it isn't allow write.",
                    "multivalued" => "Is a multi valued field.",
                    "default" => "Default value.",
                    "label" => "Label.",
                    "help" => "Help to write forms.",
                ), 
                "response" => array(),
                "type" => array(
                    "parameters" => array(
                        "fieldname" => "string",
                        "toread" => "boolean",
                        "towrite" => "boolean",
                        "value" => "string",
                        "length" => "integer",
                        "required" => "boolean",
                        "readonly" => "boolean",
                        "system" => "boolean",
                        "multivalued" => "boolean",
                        "default" => "string",
                        "label" => "string",
                        "help" => "string",
                    ), 
                    "response" => array(),
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
return new commandFormatFieldDate();