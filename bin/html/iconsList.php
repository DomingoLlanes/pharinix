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

if (!class_exists("commandIconsList")) {
    class commandIconsList extends driverCommand {

        public static function runMe(&$params, $debug = true) {
            echo '<link href="'.CMS_DEFAULT_URL_BASE.'usr/bootstrap/css/docs.min.css" rel="stylesheet">';
            echo '<legend>Bootstrap icons</legend>';
            $glClass = "glyphicon";
            $glIcons = array(
                'glyphicon-asterisk', 'glyphicon-plus', 'glyphicon-euro', 
                'glyphicon-eur', 'glyphicon-minus', 'glyphicon-cloud', 
                'glyphicon-envelope', 'glyphicon-pencil', 'glyphicon-glass', 
                'glyphicon-music', 'glyphicon-search', 'glyphicon-heart', 
                'glyphicon-star', 'glyphicon-star-empty', 'glyphicon-user', 
                'glyphicon-film', 'glyphicon-th-large', 'glyphicon-th', 
                'glyphicon-th-list', 'glyphicon-ok', 'glyphicon-remove', 
                'glyphicon-zoom-in', 'glyphicon-zoom-out', 'glyphicon-off', 
                'glyphicon-signal', 'glyphicon-cog', 'glyphicon-trash', 
                'glyphicon-home', 'glyphicon-file', 'glyphicon-time', 
                'glyphicon-road', 'glyphicon-download-alt', 'glyphicon-download', 
                'glyphicon-upload', 'glyphicon-inbox', 'glyphicon-play-circle', 
                'glyphicon-repeat', 'glyphicon-refresh', 'glyphicon-list-alt', 
                'glyphicon-lock', 'glyphicon-flag', 'glyphicon-headphones', 
                'glyphicon-volume-off', 'glyphicon-volume-down', 'glyphicon-volume-up', 
                'glyphicon-qrcode', 'glyphicon-barcode', 'glyphicon-tag', 
                'glyphicon-tags', 'glyphicon-book', 'glyphicon-bookmark', 
                'glyphicon-print', 'glyphicon-camera', 'glyphicon-font', 
                'glyphicon-bold', 'glyphicon-italic', 'glyphicon-text-height',
                'glyphicon-text-width', 'glyphicon-align-left', 'glyphicon-align-center', 
                'glyphicon-align-right', 'glyphicon-align-justify', 'glyphicon-list', 
                'glyphicon-indent-left', 'glyphicon-indent-right', 'glyphicon-facetime-video', 
                'glyphicon-picture', 'glyphicon-map-marker', 'glyphicon-adjust', 
                'glyphicon-tint', 'glyphicon-edit', 'glyphicon-share', 
                'glyphicon-check', 'glyphicon-move', 'glyphicon-step-backward', 
                'glyphicon-fast-backward', 'glyphicon-backward', 'glyphicon-play', 
                'glyphicon-pause', 'glyphicon-stop', 'glyphicon-forward', 
                'glyphicon-fast-forward', 'glyphicon-step-forward', 'glyphicon-eject', 
                'glyphicon-chevron-left', 'glyphicon-chevron-right', 'glyphicon-plus-sign', 
                'glyphicon-minus-sign', 'glyphicon-remove-sign', 'glyphicon-ok-sign', 
                'glyphicon-question-sign', 'glyphicon-info-sign', 'glyphicon-screenshot', 
                'glyphicon-remove-circle', 'glyphicon-ok-circle', 'glyphicon-ban-circle', 
                'glyphicon-arrow-left', 'glyphicon-arrow-right', 'glyphicon-arrow-up', 
                'glyphicon-arrow-down', 'glyphicon-share-alt', 'glyphicon-resize-full', 
                'glyphicon-resize-small', 'glyphicon-exclamation-sign', 'glyphicon-gift', 
                'glyphicon-leaf', 'glyphicon-fire', 'glyphicon-eye-open', 
                'glyphicon-eye-close', 'glyphicon-warning-sign', 'glyphicon-plane', 
                'glyphicon-calendar', 'glyphicon-random', 'glyphicon-comment', 
                'glyphicon-magnet', 'glyphicon-chevron-up', 'glyphicon-chevron-down', 
                'glyphicon-retweet', 'glyphicon-shopping-cart', 'glyphicon-folder-close', 
                'glyphicon-folder-open', 'glyphicon-resize-vertical', 'glyphicon-resize-horizontal', 
                'glyphicon-hdd', 'glyphicon-bullhorn', 'glyphicon-bell', 
                'glyphicon-certificate', 'glyphicon-thumbs-up', 'glyphicon-thumbs-down', 
                'glyphicon-hand-right', 'glyphicon-hand-left', 'glyphicon-hand-up', 
                'glyphicon-hand-down', 'glyphicon-circle-arrow-right', 
                'glyphicon-circle-arrow-left', 'glyphicon-circle-arrow-up', 
                'glyphicon-circle-arrow-down', 'glyphicon-globe', 
                'glyphicon-wrench', 'glyphicon-tasks', 'glyphicon-filter', 
                'glyphicon-briefcase', 'glyphicon-fullscreen', 'glyphicon-dashboard', 
                'glyphicon-paperclip', 'glyphicon-heart-empty', 'glyphicon-link', 
                'glyphicon-phone', 'glyphicon-pushpin', 'glyphicon-usd', 
                'glyphicon-gbp', 'glyphicon-sort', 'glyphicon-sort-by-alphabet', 
                'glyphicon-sort-by-alphabet-alt', 'glyphicon-sort-by-order', 
                'glyphicon-sort-by-order-alt', 'glyphicon-sort-by-attributes', 
                'glyphicon-sort-by-attributes-alt', 'glyphicon-unchecked', 
                'glyphicon-expand', 'glyphicon-collapse-down', 'glyphicon-collapse-up', 
                'glyphicon-log-in', 'glyphicon-flash', 'glyphicon-log-out', 
                'glyphicon-new-window', 'glyphicon-record', 'glyphicon-save', 
                'glyphicon-open', 'glyphicon-saved', 'glyphicon-import', 
                'glyphicon-export', 'glyphicon-send', 'glyphicon-floppy-disk', 
                'glyphicon-floppy-saved', 'glyphicon-floppy-remove', 
                'glyphicon-floppy-save', 'glyphicon-floppy-open', 
                'glyphicon-credit-card', 'glyphicon-transfer', 'glyphicon-cutlery', 
                'glyphicon-header', 'glyphicon-compressed', 'glyphicon-earphone', 
                'glyphicon-phone-alt', 'glyphicon-tower', 'glyphicon-stats', 
                'glyphicon-sd-video', 'glyphicon-hd-video', 'glyphicon-subtitles', 
                'glyphicon-sound-stereo', 'glyphicon-sound-dolby', 'glyphicon-sound-5-1', 
                'glyphicon-sound-6-1', 'glyphicon-sound-7-1', 'glyphicon-copyright-mark', 
                'glyphicon-registration-mark', 'glyphicon-cloud-download', 
                'glyphicon-cloud-upload', 'glyphicon-tree-conifer', 
                'glyphicon-tree-deciduous',
            );
            echo "<div class=\"bs-glyphicons\">";
            echo "<ul class=\"bs-glyphicons-list\">";
            foreach($glIcons as $icon) {
                echo '<li>';
                echo '<span class="'.$glClass.' '.$icon.'" aria-hidden="true"></span>';
                echo '<span class="glyphicon-class">'.$glClass.' '.$icon.'</span>';
                echo '</li>';
            }
            echo "</ul>";
            echo "</div>";
?>
<p>Includes 260 glyphs in font format from the Glyphicon Halflings set. <a href="http://glyphicons.com/">Glyphicons</a> Halflings are normally not available for free, but their creator has made them available for Bootstrap free of cost. As a thank you, we only ask that you include a link back to <a href="http://glyphicons.com/">Glyphicons</a> whenever possible.</p>
<?php
        }

        public static function getAccess($ignore = "") {
            $me = __FILE__;
            return parent::getAccess($me);
        }
        
        public static function getAccessFlags() {
            return driverUser::PERMISSION_FILE_ALL_EXECUTE;
        }
        
        public static function getHelp() {
            return array(
                "description" => "Display reference icon's list.", 
                "parameters" => array(), 
                "response" => array()
            );
        }
    }
}
return new commandIconsList();