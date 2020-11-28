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

/**
 * Class block_mcms_edit_form
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mcms_edit_form extends block_edit_form {
    /**
     * Add relevant fields
     *
     * @param object $mform
     * @throws coding_exception
     */
    protected function specific_definition($mform) {
        global $OUTPUT;

        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('config:title', 'block_mcms'));
        $mform->setType('config_title', PARAM_TEXT);

        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $this->block->context);
        $mform->addElement('editor', 'config_text', get_string('config:text', 'block_mcms'), null, $editoroptions);
        $mform->setType('config_text', PARAM_RAW); // XSS is prevented when printing the block contents and serving files.

        $mform->addElement(
            'filemanager',
            'config_images',
            get_string('config:images', 'block_mcms'),
            $this->get_file_manager_options()
        );
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
     * Set for data
     *
     * @param array|stdClass $defaults
     */
    public function set_data($defaults) {
        parent::set_data($defaults);
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $filefields = new stdClass();
            $text = $this->block->config->text;
            $draftideditor = file_get_submitted_draft_itemid('config_text');
            if (empty($text)) {
                $currenttext = '';
            } else {
                $currenttext = $text;
            }
            $filefields->config_text['text'] =
                file_prepare_draft_area($draftideditor, $this->block->context->id,
                    'block_mcms',
                    'content',
                    0,
                    array('subdirs' => true), $currenttext);
            $filefields->config_text['itemid'] = $draftideditor;
            $filefields->config_text['format'] = $this->block->config->format;

            // Now the file manager for images.
            $draftitemid = file_get_submitted_draft_itemid('config_images');
            file_prepare_draft_area($draftitemid,
                $this->block->context->id,
                'block_mcms',
                'images',
                0,
                $this->get_file_manager_options());
            $filefields->config_images = $draftitemid;
            moodleform::set_data($filefields);
        }
    }

    /**
     * Get usual options for filemanager
     *
     * @return array
     */
    protected function get_file_manager_options() {
        return array('subdirs' => 0,
            'maxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
            'context' => $this->block->context);
    }
}
