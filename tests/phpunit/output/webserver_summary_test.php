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
 * Tests.
 *
 * @package    local_cleanurls
 * @author     Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright  2017 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_cleanurls\output\webserver_summary_renderer;
use local_cleanurls\test\webserver\webtest_fake;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../cleanurls_testcase.php');

/**
 * Tests.
 *
 * @package    local_cleanurls
 * @author     Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright  2017 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_cleanurls_output_webserver_summary_test extends local_cleanurls_testcase {
    /** @var webserver_summary_renderer */
    protected $renderer = null;

    protected function setUp() {
        global $PAGE;
        parent::setUp();
        $PAGE->set_url('/local/cleanurls/webservertest.php');
        $this->renderer = $PAGE->get_renderer('local_cleanurls', 'webserver_summary', RENDERER_TARGET_GENERAL);
    }

    public function test_it_outputs() {
        $contains = [
            '<html',
            '<body',
            '</body',
            '</html',
            'Clean URLs Webserver Test',
            'id="cleanurls_webservertest_table"',
        ];

        $output = $this->renderer->render_page([new webtest_fake()]);

        foreach ($contains as $contain) {
            self::assertContains($contain, $output);
        }
    }

    public function test_it_outputs_the_given_results() {
        $webtest = new webtest_fake();
        $webtest->run();
        $this->renderer->set_results([$webtest]);
        $html = $this->renderer->render_tests_table();
        self::assertContains('This is a fake test name', $html);
        self::assertContains('This is a fake test description.', $html);
    }

    public function test_it_outputs_passed_entry() {
        $webtest = new webtest_fake();
        $webtest->run();
        $this->renderer->set_results([$webtest]);
        $html = $this->renderer->render_tests_table();
        self::assertContains('<span class="statusok">Passed</span>', $html);
    }

    public function test_it_outputs_failed_entry() {
        $webtest = new webtest_fake();
        $webtest->fakeerrors = ['Error'];
        $webtest->run();
        $this->renderer->set_results([$webtest]);
        $html = $this->renderer->render_tests_table();
        self::assertContains('<span class="statuscritical">Failed</span>', $html);
    }

    public function test_it_outputs_link_for_single_test() {
        $webtest = new webtest_fake();
        $webtest->run();
        $this->renderer->set_results([$webtest]);
        $html = $this->renderer->render_tests_table();
        self::assertContains('webservertest.php?details=webtest_fake', $html);
    }

    public function test_it_can_show_details_of_a_test() {
        $webtest = new webtest_fake();
        $webtest->run();
        $this->renderer->set_results([$webtest]);
        $html = $this->renderer->render_tests_table();
        self::assertContains('webservertest.php?details=webtest_fake', $html);
    }
}
