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

class block_mcms extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mcms');
    }

    public function hide_header() {
        return true;
    }

    public function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $text = '';
        if ($this->config) {
            $layout = isset($this->config->layout) ? $this->config->layout : 'layout_one';
            $layoutclass = "\\block_mcms\\output\\{$layout}";

            $renderable = new $layoutclass($this->config, $this->context->id);

            $renderer = $this->page->get_renderer('block_mcms');
            $text = $renderer->render($renderable);
        }
        $this->content = new stdClass();
        $this->content->text = $text;
        $this->content->items = array();
        $this->content->icons = array('t/edit');
        $this->content->footer = '';

        return $this->content;
    }

    const ALL_CONFIGS = ['title', 'images', 'text', 'backgroundcolor', 'classes', 'layout'];

    public function specialization() {
        if (isset($this->config)) {
            foreach (self::ALL_CONFIGS as $configname) {
                if (empty($this->config->$configname)) {
                    $this->$configname = get_string('config:' . $configname, 'block_mcms');
                } else {
                    $this->$configname = $this->config->$configname;
                }

            }
        }
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function instance_allow_multiple() {
        return true;
    }

    public function has_config() {
        return true;
    }

    /**
     * Serialize and store config data
     */
    public function instance_config_save($data, $nolongerused = false) {
        global $DB;

        $config = clone($data);
        // Move embedded files into a proper filearea and adjust HTML links to match.
        $config->text = file_save_draft_area_files($data->text['itemid'], $this->context->id, 'block_mcms', 'content', 0,
            array('subdirs' => true), $data->text['text']);
        $config->format = $data->text['format'];

        // Save the images.
        file_save_draft_area_files($data->images,
            $this->context->id,
            'block_mcms',
            'images',
            0,
            array('subdirs' => true));
        parent::instance_config_save($config, $nolongerused);
    }

    public function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_mcms');
        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     *
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {
        $fromcontext = context_block::instance($fromid);
        $fs = get_file_storage();
        // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
        if (!$fs->is_area_empty($fromcontext->id, 'block_mcms', 'content', 0, false)) {
            $draftitemid = 0;
            file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_mcms', 'content', 0, array('subdirs' => true));
            file_save_draft_area_files($draftitemid, $this->context->id, 'block_mcms', 'content', 0, array('subdirs' => true));
        }
        if (!$fs->is_area_empty($fromcontext->id, 'block_mcms', 'images', 0, false)) {
            $draftitemid = 0;
            file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_mcms', 'images', 0, array('subdirs' => true));
            file_save_draft_area_files($draftitemid, $this->context->id, 'block_mcms', 'images', 0, array('subdirs' => true));
        }
        return true;
    }

    /**
     * Is con
     *
     * @return false
     */
    public function content_is_trusted() {
        global $SCRIPT;
        return false;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }

    /*
     * Add custom html attributes to aid with theming and styling
     *
     * @return array
     */
    public function html_attributes() {
        global $CFG;

        $attributes = parent::html_attributes();

        if (!empty($this->config->classes)) {
            $attributes['class'] .= ' ' . $this->config->classes;
        }
        if (!empty($this->config->layout)) {
            $attributes['class'] .= ' ' . str_replace('_', '-', $this->config->layout);
        } else {
            $attributes['class'] .= ' layout-one';
        }

        return $attributes;
    }
}
