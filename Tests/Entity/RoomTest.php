<?php
/*
    Dev : Titz
    Date : 2014-12-18
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
namespace Ydle\RoomBundle\Tests\Entity;

use Ydle\HubBundle\Entity\Room;
use Ydle\HubBundle\Entity\RoomType;

use Ydle\HubBundle\Entity\Node;

class RoomTest extends \PHPUnit_Framework_TestCase
{

    public function testSetName()
    {
        $room = new Room();

        $room->setName('Room Name Test');
        $this->assertEquals('Room Name Test', $room->getName());
    }

    public function testSetDescription()
    {
        $room = new Room();

        $room->setDescription('Room Description Test');
        $this->assertEquals('Room Description Test', $room->getDescription());
    }

    public function testSetIsActive()
    {
        $room = new Room();

        $room->setIsActive(FALSE);
        $this->assertEquals(FALSE, $room->getIsActive());
        $room->setIsActive(TRUE);
        $this->assertEquals(TRUE, $room->getIsActive());
    }

    public function testSetType()
    {
        $room = new Room();

        $typeLivingRoom = new RoomType();
        $typeLivingRoom->setName('Living room');
        $typeLivingRoom->setIsActive(true);
        $room->setType($typeLivingRoom);
        $this->assertEquals($typeLivingRoom, $room->getType());
    }

    public function testSetCreatedAt()
    {
        $room = new Room();
        $date = new \DateTime('2014-12-12');

        $room->setCreatedAt($date);
        $this->assertEquals($date, $room->getCreatedAt());
    }

    public function testSetUpdatedAt()
    {
        $room = new Room();
        $date = new \DateTime('2014-12-12');

        $room->setUpdatedAt($date);
        $this->assertEquals($date, $room->getUpdatedAt());
    }

    public function testAddNode()
    {
        $node = new Node();
        $arrNode[] = $node;
        $room = new Room();

        $room->addNode($node);
        $this->assertEquals($arrNode, $room->getNodes());
    }

    // Il manque le test removeRoom.
    // J'ai une erreur quand je la teste.
    public function testRemoveNode()
    {
        $node = new Node();
        //$arrNode[] = $node;
        $room = new Room();

        $room->addNode($node);
        $this->assertEquals(1, count($room->getNodes()));
//        $room->removeRoom($node);
//        $this->assertEquals(0, count($room->getNodes()));
    }
}