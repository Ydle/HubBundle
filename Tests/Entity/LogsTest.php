<?php
/*
    Dev : Titz
    Date : 2015-01-18
*/

/*
  This file is part of Ydle.

    Ydle is free software: you can redistribute it and/or modify
    it under the terms of the GNU  Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ydle is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU  Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Ydle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Ydle\HubBundle\Tests\Entity;

use Ydle\HubBundle\Entity\Logs;

class LogsTest extends \PHPUnit_Framework_TestCase
{
    public function testSetType()
    {
        $logs = new Logs();
        $text = 'Logs Type Test';
        $logs->setType($text);
        $this->assertEquals($text, $logs->getType());
    }

    public function testSetContent()
    {
        $logs = new Logs();
        $text = 'Logs Content Test';

        $logs->setContent($text);
        $this->assertEquals($text, $logs->getContent());
    }

    public function testSetCreatedAt()
    {
        $logs = new Logs();
        $date = new \DateTime('2014-12-12');

        $logs->setCreatedAt($date);
        $this->assertEquals($date, $logs->getCreatedAt());
    }

    public function testSetUpdatedAt()
    {
        $logs = new Logs();
        $date = new \DateTime('2014-12-12');

        $logs->setUpdatedAt($date);
        $this->assertEquals($date, $logs->getUpdatedAt());
    }

    public function testSetSource()
    {
        $logs = new Logs();
        $text = 'Logs Source Test';

        $logs->setSource($text);
        $this->assertEquals($text, $logs->getSource());
    }
}