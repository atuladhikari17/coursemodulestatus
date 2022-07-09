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
 * Renderer for block coursemodulestatus
 *
 * @package    block_coursemodulestatus
 * @copyright  Atul Adhikari <atuladhikari17@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class block_coursemodulestatus_renderer extends plugin_renderer_base {

    /**
     * Renders HTML to display coursemodulestatus block
     * @param array $modulestatuses array of module details
     * @return string
     */
    public function coursemodulestatus($modulestatuses) {
        $output = html_writer::tag('div', "" , array('class' => 'oursemodulestatus'));
        $output .= html_writer::start_tag('ul', array('class' => 'list'));
        $content = false;
        foreach ($modulestatuses as $ms) {
            $content = true;
            $completiondate = $ms['date'] == 0 ? "" : "-".date('d-M-Y', $ms['date']);
            $cmlink = '/mod/'.$ms['modname'].'/view.php';
            $cmtext = $ms['cmid']."-".$ms['name'].$completiondate."<span class='text-".$ms['badge']."'>".$ms['status']."</span>";
            $output .= html_writer::tag('li', html_writer::link(new moodle_url($cmlink, array('id' => $ms['cmid'])), $cmtext),
                        array('class' => 'name'));
        }
        $output .= html_writer::end_tag('ul');
        $output .= html_writer::end_tag('div');

        if (! $content) {
            $output .= html_writer::tag('p', get_string('noactivity', 'block_coursemodulestatus'), array('class' => 'message'));
        }
        return $output;
    }
}
