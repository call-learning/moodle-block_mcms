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
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_mcms_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG, $OUTPUT;

        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('config:title', 'block_mcms'));
        $mform->setType('config_title', PARAM_TEXT);

        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $this->block->context);
        $mform->addElement('editor', 'config_text', get_string('config:text', 'block_mcms'), null, $editoroptions);
        $mform->setType('config_text', PARAM_RAW); // XSS is prevented when printing the block contents and serving files

        $mform->addElement('filemanager', 'config_images', get_string('config:images', 'block_mcms'));
        $mform->setType('config_images', PARAM_RAW);
        $mform->addHelpButton('config_images', 'config:images', 'block_mcms');

        $mform->addElement('text', 'config_backgroundcolor', get_string('config:backgroundcolor', 'block_mcms'));
        $mform->setType('config_backgroundcolor', PARAM_RAW);
        $mform->addHelpButton('config_backgroundcolor', 'config:backgroundcolor', 'block_mcms');

        $mform->addElement('text', 'config_classes', get_string('config:classes', 'block_mcms'));
        $mform->setType('config_classes', PARAM_TEXT);
        $mform->addHelpButton('config_classes', 'config:classes', 'block_mcms');

        $mform->addElement('text', 'config_decorations', get_string('config:decorations', 'block_mcms'), array('size' => 250));
        $mform->setType('config_decorations', PARAM_TEXT);
        $mform->addHelpButton('config_decorations', 'config:decorations', 'block_mcms');

        $radioarray = [];
        $imgstyles = ['class' => 'img-thumbnail', 'style' => 'max-width:300px; max-height:300px'];
        $layouts = ['layout_one', 'layout_two', 'layout_three', 'layout_four'];
        foreach ($layouts as $layoutname) {
            $image = html_writer::img($OUTPUT->image_url($layoutname, 'block_mcms')->out(),
                get_string($layoutname, 'block_mcms'),
                $imgstyles);
            $radioarray[] =& $mform->createElement('radio', 'config_layout', '', $image, $layoutname);
        }
        $mform->addGroup($radioarray, 'configlayoutarray', get_string('config:layout', 'block_mcms'), array(' '), false);
        $mform->addHelpButton('configlayoutarray', 'config:layout', 'block_mcms');
    }

    /**
     * After the form has been defined
     */
    public function display() {
        parent::display();
        $mform =& $this->_form;
        //$elementID = $mform->getElement('config_backgroundcolor')->getAttribute('id');
        //$this->page->requires->js_init_call('M.util.init_colour_picker', array($elementID, null));
    }

    /**
     * Set for data
     *
     * @param array|stdClass $defaults
     */
    function set_data($defaults) {
        global $USER;
        $text = '';
        $draftid_images = 0;
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $text = $this->block->config->text;
            $draftid_editor = file_get_submitted_draft_itemid('config_text');
            if (empty($text)) {
                $currenttext = '';
            } else {
                $currenttext = $text;
            }
            $defaults->config_text['text'] =
                file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_mcms', 'content', 0,
                    array('subdirs' => true), $currenttext);
            $defaults->config_text['itemid'] = $draftid_editor;
            $defaults->config_text['format'] = $this->block->config->format;

            ////// Now the same for the images.
            //$draftid_images =
            //    isset($this->block->config->images) ? $this->block->config->images :
            //        file_get_submitted_draft_itemid('config_images');
            //file_prepare_draft_area($draftid_images, $this->block->context->id, 'block_mcms', 'images', 0,
            //    array('subdirs' => true));
            //$defaults->config_image = $draftid_images;
        }
        if (!$this->block->user_can_edit() && !empty($this->block->config->title)) {
            // If a title has been set but the user cannot edit it format it nicely.
            $title = $this->block->config->title;
            $defaults->config_title = format_string($title, true, $this->page->context);
            // Remove the title from the config so that parent::set_data doesn't set it.
            unset($this->block->config->title);
        }

        // Have to delete text here, otherwise parent::set_data will empty content
        // of editor.
        unset($this->block->config->text);
        parent::set_data($defaults);
        // Restore $text.
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        $this->block->config->text = $text;
        $this->block->config->images = $draftid_images;
        if (isset($title)) {
            // Reset the preserved title.
            $this->block->config->title = $title;
        }
    }
}
