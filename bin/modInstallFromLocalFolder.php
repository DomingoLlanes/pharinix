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

if (!class_exists("commandModInstallFromLocalFolder")) {
    class commandModInstallFromLocalFolder extends driverCommand {

        public static function runMe(&$params, $debug = true) {
            $params = array_merge(array(
                "path" => "usr/",
                "folder" => "",
                'inplace' => false,
            ), $params);
            
            if (!is_dir($params['path'])) {
                return array("ok" => false, "msg" => __('Install path not found.'));
            }
            if (!driverTools::str_end('/', $params['path'])) {
                $params['path'] .= '/';
            }
            $params['folder'] = str_replace('\\','/', $params['folder']);
            if (!driverTools::str_end('/', $params['folder'])) {
                $params['folder'] .= '/';
            }
//            $folder = driverTools::pathInfo($params['folder']);
//            if (is_dir($params['path'].$folder['filename'])) {
//                return array("ok" => false, "msg" => __('Install path is in use.'));
//            }
            $tmpFolder = $params['folder'];
            // Long process monitor
            $lp = driverLPMonitor::start(9, __('Installing module'));
            $lpStep = 0;
            // Get meta data
            if (is_file($tmpFolder.'meta.json')) {
                driverLPMonitor::update($lp->id, ++$lpStep, __('Unpacking the module'));
                $jsonMeta = file_get_contents($tmpFolder.'meta.json');
                $meta = json_decode($jsonMeta);
                if (json_last_error() != 0) {
                    self::closeMonitor($lp);
                    return array("ok" => false, "msg" => __('JSON error:').' '.json_last_error_msg());
                }
                // Verify if install folder is in use
                $installPath = $params['path'].$meta->meta->slugname.'/';
                $folder = driverTools::pathInfo($installPath);
                if (!$params['inplace'] && $folder['exists']) {
                    self::closeMonitor($lp);
                    return array("ok" => false, "msg" => __('Install path is in use.'));
                }
                // Module installed?
                $mods = driverCommand::run('getNodes', array(
                    'nodetype' => 'modules',
                    'where' => "`title` = '{$meta->meta->slugname}'",
                ));
                if (count($mods) > 0) {
                    $ids = array_keys($mods);
                    self::closeMonitor($lp);
                    return array(
                        "ok" => false, 
                        "msg" => sprintf(__("Module '%s' is installed with version '%s'."), $meta->meta->slugname, $mods[$ids[0]]['version'])
                        );
                }
                // Verify requirements
                driverLPMonitor::update($lp->id, ++$lpStep, __('Verify requirements'));
                foreach($meta->requirements as $need => $version) {
                    if ($need == 'pharinix') {
                        $ver = driverCommand::run('getVersion');
                        $ver = $ver['version'];
                        if(!driverTools::versionIsGreaterOrEqual($version, $ver)) {
                            self::closeMonitor($lp);
                            return array(
                                "ok" => false, 
                                "msg" => sprintf(__("This module requires '%s' version '%s' and you have version '%s'."), $need, $version, $ver)
                                    );
                        }
                    } else {
                        $ver = driverCommand::run('getNodes', array(
                            'nodetype' => 'modules',
                            'where' => "`title` = '$need'",
                        ));
                        if (count($ver) == 0) {
                            self::closeMonitor($lp);
                            return array(
                                "ok" => false, 
                                "msg" => sprintf(__("This module requires '%s' version '%s' and you don't have it."), $need, $version)
                                    );
                        } else {
                            $ids = array_keys($ver);
                            if(!driverTools::versionIsGreaterOrEqual($version, $ver[$ids[0]]['version'])) {
                                self::closeMonitor($lp);
                                return array(
                                    "ok" => false, 
                                    "msg" => sprintf(__("This module requires '%s' version '%s' and you have version '%s'."), $need, $version, $ver)
                                        );
                            }
                        }
                    }
                }
                // Copy module to final path
                driverLPMonitor::update($lp->id, ++$lpStep, __('Copy module to final path'));
                if (!$params['inplace']) {
                    mkdir($installPath);
                    $resul = driverTools::pathCopy($tmpFolder, $installPath);
                    if ($resul !== true) {
                        $resp = array(
                            'ok' => false,
                            'msg' => __(sprintf('Can\'t copy files from \'%s\' to \'%s\'.', $params['folder'], $params['path'])),
                            );
                        if ($resul instanceof Exception) {
                            $resp['msg'] .= ' '.$resul->getMessage();
                        }
                        self::closeMonitor($lp);
                        return $resp;
                    }
                }
                // Apply configuration
                if (isset($meta->configuration)) {
                    driverLPMonitor::update($lp->id, ++$lpStep, __('Apply configuration'));
                    foreach($meta->configuration as $group => $values) {
                        switch ($group) { // System configuration can't be changed by modules meta
                            case '[core]':
                            case '[mysql]':
                            case '[safe_mode]':
                                break;
                            default:
                                driverConfig::getCFG()->addSection($group);
                                foreach($values as $key => $value) {
                                    driverConfig::getCFG()->getSection($group)->set($key, $value);
                                }
                                break;
                        }
                    }
                    driverConfig::getCFG()->save();
                } else {
                    driverLPMonitor::update($lp->id, ++$lpStep, __(''));
                }
                // Install command's paths
                if (isset($meta->bin_paths)) {
                    driverLPMonitor::update($lp->id, ++$lpStep, __("Install command's paths"));
                    foreach($meta->bin_paths as $cpath) {
                        driverCommand::run('cfgAddPath', array(
                            'path' => $installPath.$cpath
                        ));
                    }
                    driverCommand::refreshPaths();
                } else {
                    driverLPMonitor::update($lp->id, ++$lpStep, __(''));
                }
                // Install booting
                if (isset($meta->booting)) {
                    driverLPMonitor::update($lp->id, ++$lpStep, __('Install booting commands'));
                    $narr = array();
                    foreach($meta->booting as $bootObj) {
                        $cmd = '';
                        $pars = array();
                        $priority = 0;
                        foreach($bootObj as $key => $value) {
                            switch ($key) {
                                case 'priority':
                                    $priority = $value;
                                    break;
                                default: // Is a command
                                    $cmd = $key;
                                    foreach($value as $par => $val) {
                                        $pars[] = $par."=".$val;
                                    }
                                    break;
                            }
                        }
                        $boot = driverCommand::run('addBooting', array(
                            'cmd' => $cmd,
                            'parameters' => implode("&", $pars),
                            'priority' => $priority,
                        ));
                        $bootObj->id = $boot['uid'];
                        $narr[] = $bootObj;
                    }
                    $meta->booting = $narr;
                } else {
                    driverLPMonitor::update($lp->id, ++$lpStep, __(''));
                }
                // Install meta to modules table
                driverCommand::run('addNode', array(
                    'nodetype' => 'modules',
                    'title' => $meta->meta->slugname,
                    'path' => $installPath,
                    'meta' => json_encode($meta),
                    'version' => $meta->meta->version,
                ));
                // Install node types
                if (isset($meta->nodetypes)) {
                    driverLPMonitor::update($lp->id, ++$lpStep, __('Install node types'));
                    foreach($meta->nodetypes as $nodetype => $def) {
                        $nodetype = strtolower($nodetype);
                        // Find especial field names like __removetitle
                        $haveTitle = true;
                        $labelField = '';
                        foreach($def as $name => $fieldDef) {
                            switch ($name) {
                                case '__removetitle':
                                    $haveTitle = !$fieldDef;
                                    break;
                                case '__labelfield':
                                    $labelField = $fieldDef;
                                    break;
                            }
                        }
                        // Create node types
                        $conf = array( 'name' => $nodetype );
                        if ($labelField != '') {
                            $conf['label_field'] = $labelField;
                        }
                        driverCommand::run('addNodeType', $conf);
                        if (!$haveTitle) {
                            driverCommand::run('delNodeField', array(
                                'nodetype' => $nodetype,
                                'name' => 'title'
                            ));
                        }
                        // Create fields
                        foreach($def as $name => $fieldDef) {
                            switch ($name) {
                                case '__removetitle':
                                case '__labelfield':
                                    break;
                                default:
                                    $field = array(
                                        'node_type' => $nodetype,
                                        'name' => $name
                                    );
                                    if (isset($fieldDef->type)) $field['type'] = $fieldDef->type;
                                    if (isset($fieldDef->iskey)) $field['iskey'] = $fieldDef->iskey;
                                    if (isset($fieldDef->len)) $field['len'] = $fieldDef->len;
                                    if (isset($fieldDef->required)) $field['required'] = $fieldDef->required;
                                    if (isset($fieldDef->readonly)) $field['readonly'] = $fieldDef->readonly;
                                    if (isset($fieldDef->locked)) $field['locked'] = $fieldDef->locked;
                                    if (isset($fieldDef->multi)) $field['multi'] = $fieldDef->multi;
                                    if (isset($fieldDef->default)) $field['default'] = $fieldDef->default;
                                    if (isset($fieldDef->label)) $field['label'] = $fieldDef->label;
                                    if (isset($fieldDef->help)) $field['help'] = $fieldDef->help;

                                    driverCommand::run('addNodeField', $field);
                                    break;
                            }
                        }
                    }
                } else {
                    driverLPMonitor::update($lp->id, ++$lpStep, __(''));
                }
                // Run SQL queries
                if (isset($meta->sql)) {
                    driverLPMonitor::update($lp->id, ++$lpStep, __('Runing SQL queries'));
                    if (isset($meta->sql->install)) {
                        foreach ($meta->sql->install as $sql) {
                            dbConn::Execute($sql);
                        }
                    }
                } else {
                    driverLPMonitor::update($lp->id, ++$lpStep, __(''));
                }
                // Execute install commands
                if (isset($meta->install)) {
                    driverLPMonitor::update($lp->id, ++$lpStep, __('Executing install commands'));
                    foreach($meta->install as $bootObj) {
                        $pars = array();
                        foreach($bootObj as $key => $value) {
                            $cmd = $key;
                            foreach($value as $par => $val) {
                                $pars[$par] = $val;
                            }
                        }
                        driverCommand::run($cmd, $pars);
                    }
                } else {
                    driverLPMonitor::update($lp->id, ++$lpStep, __(''));
                }

                self::closeMonitor($lp);
                return array("ok" => true, "path" => $installPath);
            } else {
                self::closeMonitor($lp);
                return array(
                    "ok" => false, 
                    "msg" => sprintf(__("Meta file not found at '%s'. Have the package the correct structure?."), $tmpFolder.'meta.json')
                    );
            }
        }

        public static function closeMonitor($lp) {
            // Closing progressbar
            driverLPMonitor::close($lp->id);
        }
        
        public static function getHelp() {
            return array(
                "package" => 'core',
                "description" => __("Install a module from a local folder."), 
                "parameters" => array(
                    "path" => __("Optional path where install the module, relative to Pharinix root path. If not defined the default path is 'usr/'"),
                    "folder" => __("Path to the folder with the new module."),
                    'inplace' => __('If TRUE install module in some folder where is. Default FALSE.'),
                ), 
                "response" => array(
                        "ok" => __("TRUE if the installation is OK."),
                        "msg" => __("If install error this contains the error message."),
                        "path" => __("If install ok contains the install path."),
                    ),
                "type" => array(
                    "parameters" => array(
                        "path" => "string",
                        "folder" => "string",
                        'inplace' => 'boolean',
                    ), 
                    "response" => array(
                        "ok" => "booelan",
                        "msg" => "string",
                        "path" => "string",
                    ),
                ),
                "echo" => false
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
return new commandModInstallFromLocalFolder();