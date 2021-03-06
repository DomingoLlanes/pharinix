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
//if (!defined("CMS_VERSION")) { header("HTTP/1.0 404 Not Found"); die(""); }

 class driverTools {

     /**
      * Get information about a path, name and extension of the given file.<br>
     * Extraer informacion de una ruta, nombre y extension de un archivo dado.<br/>
     * <a href="http://www.propiedadprivada.com/funcion-php-extraer-ruta-nombre-y-extension-de-un-archivo/746/">Source/Fuente</a>
     * @param string $path Path to explore. Ruta a analizar
     * @return array Extension sin nombre: .htaccess
     * <ul>
     * <li>Array (7)</li>
     * <li>(</li>
     * <li>|    ["exists"] = Boolean(1) TRUE</li>
     * <li>|    ["isfile"] = Boolean(1) TRUE</li>
     * <li>|    ["isdir"] = Boolean(1) FALSE</li>
     * <li>|    ["writable"] = Boolean(0) FALSE</li>
     * <li>|    ["chmod"] = String(4) " 0644 "</li>
     * <li>|    ["ext"] = String(8) " htaccess "</li>
     * <li>|    ["path"] = Boolean(0) FALSE</li>
     * <li>|    ["name"] = Boolean(0) FALSE</li>
     * <li>|    ["filename"] = String(9) " .htaccess "</li>
     * <li>)
     * </ul><br>
     * Posibilidades de uso (o mal uso) de una funcion de este tipo:<br>
     * <ul>
     * <li>Extension sin nombre: .htaccess</li>
     * <li>Nombre sin extension: name</li>
     * <li>Nombre simplon: name.jpeg</li>
     * <li>Nombre complejo: name.surname.gif</li>
     * <li>Ruta absoluta: /path/to/name.surname.tar.gz</li>
     * <li>Ruta relativa: ../../path/to/name.surname.tar.gz</li>
     * <li>BONUS: Cadena vacia para romper la funcion Comillas vacias "</li>
     * <li>BONUS 2: Cadena malformada para romper la funcion "/\/.path///file/.gif"</li>
     * <li>BONUS 3: Ruta sin archivo "/path/to/folder/"</li>
     * </ul>
     */
    public static function pathInfo($path) {
        $path = str_replace("\\", "/", $path);
        // Vaciamos la cachÃ© de lectura de disco
        clearstatcache();
        // Comprobamos si el fichero existe
        $data["exists"] = is_file($path) || is_dir($path);
        $data["isfile"] = $data["exists"] && is_file($path);
        $data["isdir"] = $data["exists"] && is_dir($path);
        // Comprobamos si el fichero es escribible
        $data["writable"] = is_writable($path);
        // Leemos los permisos del fichero
        $data["chmod"] = ($data["exists"] ? substr(sprintf("%o", fileperms($path)), -4) : FALSE);
        // Extraemos la extension, un solo paso
        if (!$data["isdir"]) {
            $data["ext"] = substr(strrchr($path, "."), 1);
        } else {
            $data["ext"] = false;
        }
        // Primer paso de lectura de ruta
        $pt = explode("." . $data["ext"], $path);
        $data["path"] = array_shift($pt);
        // Primer paso de lectura de nombre
        if (!$data["isdir"]) {
            $pt = explode("/", $data["path"]);
            $data["name"] = array_pop($pt);
            // Ajustamos nombre a FALSE si esta vacio
            $data["name"] = ($data["name"] ? $data["name"] : "");
            // Ajustamos el nombre a FALSE si esta vacio o a su valor en caso contrario
            $data["filename"] = (($data["name"] OR $data["ext"]) ? $data["name"] . ($data["ext"] ? "." : "") . $data["ext"] : FALSE);
        } else {
            $data["name"] = "";
            $data["filename"] = basename($path);
        }
        // Ajustamos la ruta a FALSE si esta vacia
        $p1 = @explode($data["name"], $data["path"]);
        if ($p1 === false) $p1 = array();
        $p2 = @explode($data["ext"], $data["path"]);
        if ($p2 === false) $p2 = array();
        $p3 = @explode($data["name"], $data["path"]);
        if ($p3 === false) $p3 = array();
        $p4 = @explode($data["ext"], $data["path"]);
        if ($p4 === false) $p4 = array();
        $data["path"] = ($data["exists"] ?
                ($data["name"] ?
                    realpath(array_shift($p1)) :
                    realpath(array_shift($p2))) :
                ($data["name"] ?
                    array_shift($p3) :
                    ($data["ext"] ?
                            array_shift($p4) :
                            rtrim($data["path"], "/"))));
        // Devolvemos los resultados
        return $data;
    }

     /**
     * http://www.programacionweb.net/articulos/articulo/listar-archivos-de-un-directorio/
     * @param string $path Folder path to explore. Must include "/" at the end.
     * @return array ("folders" => array(string, ...), "files" => array(string, ...))
     */
    public static function lsDir($path, $pattern = "*.*") {
        $resp = array("files" => array(), "folders" => array());
        $directorio = opendir($path);
        while ($archivo = readdir($directorio)) {
            if ($archivo != '.' && $archivo != '..') {
                if (fnmatch($pattern, $archivo)) {
                    if (is_dir("$path/$archivo")) {
                        $resp["folders"][] = $path.$archivo;
                    } else {
                        $resp["files"][] = $path.$archivo;
                    }
                }
            }
        }
        closedir($directorio);
        return $resp;
    }

    /**
     +-------------------------------------------------------------------------+
     | Revive Adserver                                                         |
     | http://www.revive-adserver.com                                          |
     |                                                                         |
     | Copyright: See the COPYRIGHT.txt file.                                  |
     | License: GPLv2 or later, see the LICENSE.txt file.                      |
     +-------------------------------------------------------------------------+
     * Attempts to remove the file indicated by the $sFilename path from the
     * filesystem. If the $filename indicates non-empty directory the function
     * will remove it along with all its content.
     *
     * @param string $sFilename
     * @return boolean True if the operation is successful, Exception if there
     * was a failure.
    */
    public static function fileRemove($sFilename) {
        if (file_exists($sFilename)) {
            if (is_dir($sFilename)) {
                $directory = opendir($sFilename);
                if (false === $directory) {
                    $error = new Exception(sprintf(__("Can't open the directory: '%s'."), $sFilename));
                    return $error;
                }
                while (($sChild = readdir($directory)) !== false) {
                    if ($sChild == '.' or $sChild == '..') {
                        continue;
                    }
                    $result = self::fileRemove($sFilename . '/' . $sChild);
                    if ($result instanceof Exception) {
                        return $result;
                    }
                }
                closedir($directory);
                $result = rmdir($sFilename);
                if ($result === false) {
                    $error = new Exception(sprintf(__("Can't delete the directory: '%s'."), $sFilename));
                    return $error;
                }
            } else {
                if(!unlink($sFilename)) {
                    return new Exception(sprintf(__("Can't remove the file: '%s'."), $sFilename));
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Copy a file or a entire folder from $source to $final.
     * @param string $source Path to the source file.
     * @param string $final <p>
     * The destination path.
     * </p>
     * <p>
     * If the destination file already exists, it will be overwritten.
     * </p>
     * @param integer $mod <p>
     * The mode is 0777 by default, which means the widest possible
     * access. For more information on modes, read the details
     * on the <b>chmod</b> page.
     * </p>
     * <p>
     * <i>mode</i> is ignored on Windows.
     * </p>
     * <p>
     * Note that you probably want to specify the mode as an octal number,
     * which means it should have a leading zero. The mode is also modified
     * by the current umask, which you can change using
     * <b>umask</b>.
     * </p>
     * @return boolean|\Exception
     */
    public static function pathCopy($source, $final, $mod = 0777) {
        $sourceInfo = driverTools::pathInfo($source);
        if ($sourceInfo['exists']) {
            if ($sourceInfo['isdir']) {
                $directory = opendir($source);
                if (false === $directory) {
                    $error = new Exception(sprintf(__("Can't open the directory: '%s'."), $source));
                    return $error;
                }
                while (($sChild = readdir($directory)) !== false) {
                    if ($sChild == '.' or $sChild == '..') {
                        continue;
                    }
                    if (is_dir($source.'/'.$sChild) && !is_dir($final.'/'.$sChild)) {
                        if (!mkdir($final.'/'.$sChild, $mod)) {
                            return new Exception(sprintf(__("Can't make folder: '%s'."), $final.'/'.$sChild));
                        }
                    }
                    $result = self::pathCopy($source.'/'.$sChild, $final.'/'.$sChild, $mod);
                    if ($result instanceof Exception) {
                        return $result;
                    }
                }
                closedir($directory);
            } else {
                if(!copy($source, $final)) {
                    return new Exception(sprintf(__("Can't copy the file: '%s'."), $source));
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Compare two versions strings. Version numbers must have de format <mayor>.<minor>.<revision>, and all parts must be numbers. Ex. '1.2.3' . In $need, minor or revision can be 'x' to allow any value.
     * @param string $need Version number
     * @param string $have Version number
     * @return boolean TRUE if $have is greater or equal to $need
     */
    public static function versionIsGreaterOrEqual($need, $have) {
        $need = explode(".", $need);
        $have = explode(".", $have);
        for($i = 0; $i < 3; $i++) {
            if (!isset($need[$i])) {
                $need[$i] = 0;
            } else {
                $need[$i] = intval($need[$i]);
            }
            if (!isset($have[$i])) {
                $have[$i] = 0;
            } else {
                $have[$i] = intval($have[$i]);
            }
            if (!($have[$i] >= $need[$i])) return false;
            if ($have[$i] > $need[$i]) break;
        }
        return true;
    }

    public static function formatDate($mysqlDate, $withTime = true) {
        if ($mysqlDate == "")
            return "";
        $tmp = strtotime($mysqlDate);
        $tmp = date("d-m-Y H:i:s", $tmp);
        $sep = explode(" ", $tmp);
        $tmp = $sep[0];
        if ($withTime) {
            $tmp .= "<br/>" . $sep[1] . " H.";
        }

        return "<span class=\"text-nowrap\">$tmp</span>";
    }

    public static function formatDateInline($mysqlDate, $withTime = true) {
        $resp = self::formatDate($mysqlDate, $withTime);
        return str_replace("<br/>", " ", $resp);
    }

    public static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        //$bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * start $string with $start ?
     * @param string $start Start string
     * @param string $string String that would start with $start
     * @return boolean
     */
    public static function str_start($start, $string) {
        if ($start == "") return true;
        $cap = substr($string, 0, strlen($start));
        return ($cap == $start);
    }

    /**
     * end $string with $end ?
     * @param string $end End string
     * @param string $string String that would end with $end
     * @return boolean
     */
    public static function str_end($end, $string) {
        if ($end == "") return true;
        $cap = substr($string, -1 * strlen($end));
        return ($cap == $end);
    }

    /**
     * http://stackoverflow.com/a/20075147<br>
     * //  url like: http://stackoverflow.com/questions/2820723/how-to-get-base-url-with-php<br>
     * <br>
     * echo base_url();    //  will produce something like: http://stackoverflow.com/questions/2820723/<br>
     * echo base_url(TRUE);    //  will produce something like: http://stackoverflow.com/<br>
     * echo base_url(TRUE, TRUE); || echo base_url(NULL, TRUE);    //  will produce something like: http://stackoverflow.com/questions/<br>
     * //  and finally<br>
     * echo base_url(NULL, NULL, TRUE);<br>
     * //  will produce something like: <br>
     * //      array(3) {<br>
     * //          ["scheme"] => string(4) "http"<br>
     * //          ["host"] => string(12) "stackoverflow.com"<br>
     * //          ["path"] => string(35) "/questions/2820723/"<br>
     * //      }
     * @param boolean $atRoot
     * @param boolean $atCore
     * @param boolean $parse
     * @return string
     */
    public static function base_url($atRoot = FALSE, $atCore = FALSE, $parse = FALSE) {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            if ($dir == '/') {
                $dir = '';
            }

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = str_replace('\\', '/', $core[0]);
            $core = str_replace('/etc/drivers', '', $core);
            
            if (driverTools::str_start('/', $core)) {
                $core = substr($core, 1);
            }
            
            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s" : "%s://%s") : ($atCore ? "%s://%s/%s" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
            if (!driverTools::str_end('/', $base_url)) {
                $base_url .= '/';
            }
        } else {
            $base_url = 'http://localhost/';
        }

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path']))
                if ($base_url['path'] == '/')
                    $base_url['path'] = '';
        }
        return $base_url;
    }

    /**
     * Generate a new password string
     * @link http://www.catchstudio.com/labs/password-generator/
     * @param boolean $alpha Use alpha lowercase characters
     * @param boolean $alpha_upper Use alpha uppercase characters
     * @param boolean $numeric Use numeric characters
     * @param boolean $special Use special characters
     * @param integer $length Password length
     * @return string The new password
     */
    public static function passNew($alpha, $alpha_upper, $numeric, $special, $length = 9) {
        $_alpha = "abcdefghijklmnopqrstuvwxyz";
        $_alpha_upper = strtoupper($_alpha);
        $_numeric = "0123456789";
        $_special = ".-+=_,!@$#*%<>[]{}";
        $chars = "";

        // if you want a form like above
        if ($alpha)         $chars .= $_alpha;
        if ($alpha_upper)   $chars .= $_alpha_upper;
        if ($numeric)       $chars .= $_numeric;
        if ($special)       $chars .= $_special;

        $len = strlen($chars);
        $pw = '';

        for ($i = 0; $i < $length; $i++) {
            $pw .= substr($chars, rand(0, $len - 1), 1);
        }

        // the finished password
        return str_shuffle($pw);
    }
    
    /**
     * Return the last lines of the file
     * @author Lorenzo Stanco <https://gist.github.com/lorenzos>
     * @link https://gist.github.com/lorenzos/1711e81a9162320fde20
     * @link http://stackoverflow.com/a/15025877
     * @param string $filepath File path
     * @param integer $lines Number of lines to return
     * @param boolean $adaptive Use adaptative buffer, TRUE to best performance
     * @return string
     */
    public static function tailCustom($filepath, $lines = 1, $adaptive = true) {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false)
            return false;
        // Sets buffer size
        if (!$adaptive)
            $buffer = 4096;
        else
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        // Jump to last character
        fseek($f, -1, SEEK_END);
        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n")
            $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';
        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }
        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        // Close file and return
        fclose($f);
        return trim($output);
    }
    
    /**
     * Get remote call user agent string based on configuration.
     * It replace {version} and {url_base} with default values.
     * @return string
     */
    public static function getUserAgent() {
        $rowUA = driverConfig::getCFG()->getSection('[core]')->get('CURLOPT_USERAGENT');
        if ($rowUA == null) {
            $rowUA = 'Mozilla/5.0 (compatible; Pharinix/{version}; +{url_base})';
        }
        $ua = str_replace('{version}', CMS_VERSION, $rowUA);
        $ua = str_replace('{url_base}', CMS_DEFAULT_URL_BASE, $ua);
        return $ua;
    }
    
    /**
     * Do a remote call with cURL
     * 
     * @param string $url URL to call
     * @param array $params Parameters list to send by POST, if no has value do a GET call.
     * @param boolean $parseParams If TRUE try to parse $params how array.
     * @param boolean $binary If TRUE add the --data-binary parameter to cURL.
     * @param array $headers Extra headers to add.
     * @param integer $timeoutsec Seconds before timeout.
     * @return array array ( "header" => Petition headers, "body" => Response body, "error" => Error message );
     * @link http://hayageek.com/php-curl-post-get
     */
    public static function apiCall($url, $params = null, $parseParams = true, $binary = false, $headers = null, $timeoutsec = 30) {
        return self::apiCallMS($url, $params, $parseParams, $binary, $headers, $timeoutsec * 1000);
    }
    
    /**
     * Do a remote call with cURL
     * 
     * @param string $url URL to call
     * @param array $params Parameters list to send by POST, if no has value do a GET call.
     * @param boolean $parseParams If TRUE try to parse $params how array.
     * @param boolean $binary If TRUE add the --data-binary parameter to cURL.
     * @param array $headers Extra headers to add.
     * @param integer $timeoutms Miliseconds before timeout.
     * @return array array ( "header" => Petition headers, "body" => Response body, "error" => Error message );
     * @link http://hayageek.com/php-curl-post-get
     */
    public static function apiCallMS($url, $params = null, $parseParams = true, $binary = false, $headers = null, $timeoutms = 30000) {
        $postData = '';
        if ($parseParams && $params != null) {
            //create name value pairs seperated by &
            foreach($params as $k => $v)
            {
               $postData .= $k . '='.$v.'&';
            }
            rtrim($postData, '&');
        } else {
            $postData = $params;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($binary) curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        if ($postData != "") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // Allow use @ to upload files
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeoutms); //timeout in seconds
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //not verify certificate
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow location headers
        curl_setopt($ch, CURLOPT_USERAGENT, driverTools::getUserAgent());
//        curl_setopt($ch, CURLOPT_REFERER, self::API_URL.'dashboard');
        curl_setopt($ch,CURLOPT_ENCODING , "");
        if ($headers != null) {
            $h = array();
            foreach($headers as $key => $value) {
                $h[] = $key.': '.$value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
        }
        $response = curl_exec($ch);

        // Then, after your curl_exec call:
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $lastError = curl_error($ch);

        $aux = explode("\n", $header);
        $rHeaders = array();
        foreach($aux as $head) {
            $rHeaders[] = trim($head);
        }

        $resp = array (
            "header" => $rHeaders,
            "request" => curl_getinfo($ch),
            "request_body" => $postData,
            "body" => $body,
            "error" => $lastError
        );
        curl_close($ch);
        return $resp;
    }
    
    /**
     * Return a label about de error level.
     * 
     * @param integer $type Error level
     * @return string
     */
    public static function getErrorLevelLabelByType($type) {
        switch ($type) {
                case E_ERROR:
                    return 'Fatal run-time error';
                case E_PARSE:
                    return 'Compile-time parse error';
                case E_CORE_ERROR:
                    return 'Fatal errors during PHP\'s initial startup';
                case E_COMPILE_ERROR:
                    return 'Fatal compile-time error';
                case E_RECOVERABLE_ERROR:
                    return 'Catchable fatal error not catched';
                case E_WARNING:
                    return 'Warning';
                case E_NOTICE:
                    return 'Notice';
                case E_CORE_WARNING:
                    return 'Core warnings';
                case E_COMPILE_WARNING:
                    return 'Compile-time warning';
                case E_USER_ERROR:
                    return 'User-generated error';
                case E_USER_WARNING:
                    return 'User-generated warning';
                case E_USER_NOTICE:
                    return 'User-generated notice';
                case E_DEPRECATED:
                    return 'Deprecated';
                case E_USER_DEPRECATED:
                    return 'User-generated deprecated';
                default:
                    return 'Error level '.$type;
        }
    }
}

if (!function_exists('json_last_error_msg')) {
    /**
     * http://es1.php.net/manual/es/function.json-last-error-msg.php#117393
     *
     * @staticvar array $ERRORS
     * @return string
     */
    function json_last_error_msg() {
        $ERRORS = array(
            JSON_ERROR_NONE => 'No error', // Required that this be how is, without translation.
            JSON_ERROR_DEPTH => __('Maximum stack depth exceeded'),
            JSON_ERROR_STATE_MISMATCH => __('State mismatch (invalid or malformed JSON)'),
            JSON_ERROR_CTRL_CHAR => __('Control character error, possibly incorrectly encoded'),
            JSON_ERROR_SYNTAX => __('Syntax error'),
            JSON_ERROR_UTF8 => __('Malformed UTF-8 characters, possibly incorrectly encoded')
        );

        $error = json_last_error();
        return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
    }

}

if (!function_exists('session_status')) {
    if (!defined('PHP_SESSION_DISABLED')) define('PHP_SESSION_DISABLED', 0);
    if (!defined('PHP_SESSION_NONE')) define('PHP_SESSION_NONE', 1);
    if (!defined('PHP_SESSION_ACTIVE')) define('PHP_SESSION_ACTIVE', 2);
    function session_status() {
        return session_id() === '' ? PHP_SESSION_NONE : PHP_SESSION_ACTIVE;
    }
}

// Internal PHP Server routering
if (php_sapi_name() == 'cli-server') {
    //http://stackoverflow.com/a/38926070
    $dir = str_replace('\\', '/', __DIR__);
    $dir = str_replace('/etc/drivers', '', $dir);
    $filePath = realpath(ltrim($_SERVER["REQUEST_URI"], '/'));
    if ($filePath && is_dir($filePath)){
        // attempt to find an index file
        foreach (array('index.php', 'index.html') as $indexFile){
            if ($filePath = realpath($filePath . DIRECTORY_SEPARATOR . $indexFile)){
                break;
            }
        }
    }
    if ($filePath && is_file($filePath)) {
        // 1. check that file is not outside of this directory for security
        // 2. check for circular reference to router.php
        // 3. don't serve dotfiles
        if (strpos($filePath, $dir . DIRECTORY_SEPARATOR) === 0 &&
                    $filePath != $dir . DIRECTORY_SEPARATOR . 'router.php' &&
                    substr(basename($filePath), 0, 1) != '.'
            ) {
//                if (strtolower(substr($filePath, -4)) == '.php') {
//                    // php file; serve through interpreter
////                    include $filePath;
////                    return true;
//                } else {
//                    // asset file; serve from filesystem
////                    return false;
//                }
//            } else {
//                // disallowed file
//                header("HTTP/1.1 404 Not Found");
//                echo "404 Not Found";
            }
        } else {
            // rewrite to our index file
            $query = '';
            if (isset($_SERVER['QUERY_STRING'])) {
                $query = '?'.$_SERVER['QUERY_STRING'];
            }
            $_GET['rewrite'] = str_replace($query, '', $_SERVER['REQUEST_URI']);
            $_GET['rewrite'] = substr($_GET['rewrite'], 1);
//            include $dir . DIRECTORY_SEPARATOR . 'index.php';
        }
}