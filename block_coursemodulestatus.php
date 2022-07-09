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
 * Self completion block.
 *
 * @package   block_coursemodulestatus
 * @copyright Atul Adhikari <atuladhikari17@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/completionlib.php');

class block_coursemodulestatus extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_coursemodulestatus');
    }

    public function applicable_formats() {
        return array('course' => true);
    }

    public function get_content() {
        global $CFG, $USER, $COURSE, $OUTPUT;
        // If content is cached.
        if ($this->content !== null) {
            return $this->content;
        }
        if (!isloggedin() or isguestuser()) {
            return $this->content;
        }
        $completion = new \completion_info($COURSE);

        // First, let's make sure completion is enabled.
        if (!$completion->is_enabled()) {
            return null;
        }
        if ($completion->is_tracked_user($USER->id)) {
            // Get the number of modules that support completion.
            $modules = $completion->get_activities();
            foreach ($modules as $key => $module) {
                $data[$key] = $completion->get_data($module, true, $USER->id);
                $data[$key]->name = $module->name;
                $data[$key]->modname = $module->modname;
            }
            $this->content = new stdClass;
            $notcompleted = get_string('not_completed', 'block_coursemodulestatus');
            $completed = get_string('completed', 'block_coursemodulestatus');
            $completedwithpass = get_string('completed_with_pass', 'block_coursemodulestatus');
            $completedwithfail = get_string('completed_with_fail', 'block_coursemodulestatus');

            $status = array(0 => $notcompleted , 1 => $completed, 2 => $completedwithpass, 3 => $completedwithfail);
            $statuscode = array(0 => 'danger', 1 => 'success', 2 => 'success', 3 => 'warning');
            $modulestatus = array();
            foreach ($data as $module) {

                $temp['cmid'] = $module->coursemoduleid;
                $temp['name'] = $module->name;
                $temp['date'] = $module->timemodified;
                $temp['status'] = $module->completionstate == 0 ? "" : "-".$status[$module->completionstate];
                $temp['badge'] = $statuscode[$module->completionstate];
                $temp['modname'] = $module->modname;
                array_push($modulestatus, $temp);
            }
            $renderer = $this->page->get_renderer('block_coursemodulestatus');
            $this->content->text = $renderer->coursemodulestatus($modulestatus);
        } else {
            // If user is not enrolled, show error.
            $this->content->text = get_string('nottracked', 'completion');
        }
        return $this->content;
    }
}

