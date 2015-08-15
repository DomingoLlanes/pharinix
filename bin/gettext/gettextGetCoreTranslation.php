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

if (!class_exists("commandGettextGetCoreTranslation")) {
    class commandGettextGetCoreTranslation extends driverCommand {

        public static function runMe(&$params, $debug = true) {
            $params = array_merge(array(
                'languaje' => '',
            ), $params);
            
            if ($params['languaje'] == '') {
                return array('ok' => false, 'msg' => __('Languaje is required.'));
            }
            
            $fInfo = driverTools::pathInfo('etc/i18n/'.$params['languaje'].'.po');
            if ($fInfo['writable']) {
                $po = Gettext\Extractors\Po::fromFile('etc/i18n/'.$params['languaje'].'.po');
                $resp = array('items' => array());
//                $item = new Gettext\Translation();
                foreach($po as $item) {
                   $t = new stdClass();
                   $t->original = $item->getOriginal();
                   $t->translation = $item->getTranslation();
                   $t->references = $item->getReferences();
                   $resp['items'][] = $t;
                }
                return $resp;
            } else {
                return array('ok' => false, 'msg' => __('PO file is not writable.'));
            }
        }
        
        public static function getHelp() {
            return array(
                "description" => __("Return all Pharinix translation items."), 
                "parameters" => array(
                    'languaje' => __('Language code.'),
                ), 
                "response" => array(
                    'items' => __('List of items translated.')
                ),
                "type" => array(
                    "parameters" => array(
                        'languaje' => 'string',
                    ), 
                    "response" => array(
                        'items' => 'string'
                    ),
                )
            );
        }
        
        public static function getAccess($ignore = "") {
            $me = __FILE__;
            return parent::getAccess($me);
        }
        
//        public static function getAccessFlags() {
//            return driverUser::PERMISSION_FILE_ALL_EXECUTE;
//        }
    }
}
return new commandGettextGetCoreTranslation();