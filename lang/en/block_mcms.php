<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle Mini CMS block
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['descconfig'] = 'Configure the MCMS (Moodle Mini CMS) block';
$string['headerconfig'] = 'Config section header';
$string['mcms:addinstance'] = 'Add a MCMS block';
$string['mcms:myaddinstance'] = 'Add a MCMS block to my moodle';
$string['pluginname'] = 'MCMS';
$string['config:classes'] = 'Additional CSS classes';
$string['config:classes_help'] =
    'The purpose of this configuration is to aid with theming by helping distinguish HTML blocks from each other. Any CSS classes entered here (space delimited) will be appended to the block\'s default classes.';
$string['config:text'] = 'Content';
$string['config:title'] = 'Title';
$string['config:images'] = 'Image';
$string['config:decorations'] = 'Decoration styles for this block';
$string['config:decorations_help'] = 'Decoration styles for this block, a set of classes that, put together will
be added to an empty div. We can have several divs inserted in the block if you separate the classes by comma.';
$string['config:images_help'] = 'Image for either background (background.jpg), icons (icon.svg) or illustration (side-image.jpg).';
$string['config:backgroundcolor'] = 'Background color';
$string['config:backgroundcolor_help'] = 'Background color. It can also be an url to an image as in CSS background image.';
$string['config:layout'] = 'Layout';
$string['layout_one'] = 'Layout 1: Icon(icon.svg or icon.png or icon.jpg)/Title/Description';
$string['layout_two'] = 'Layout 2: Icon(icon.svg or icon.png or icon.jpg)/Title/Background(background.jpg)/FullSize';
$string['layout_three'] = 'Layout 3: Side image (side-image.jpg) on the right and text';
$string['layout_four'] = 'Layout 4: Side image (side-image.jpg) on the left and text';
$string['sideimage'] = "Illustrative side image";
$string['config:layout_help'] = 'Layout: 4 layouts from icon/description to image on the sides and centered title with background.
<br>' . $string['layout_one'] . '<br>' . $string['layout_two'] . '<br>' . $string['layout_three'] . '<br>' .
    $string['layout_four'] . '<br>';
