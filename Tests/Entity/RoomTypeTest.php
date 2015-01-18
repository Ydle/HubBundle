<?php
/*
    Dev : Titz
    Date : 2014-12-21
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

use Ydle\HubBundle\Entity\RoomType;
use Ydle\HubBundle\Entity\Room;
use Ydle\HubBundle\Entity\Node;

class RoomTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testSetName()
    {
        $roomType = new RoomType();

        $roomType->setName('RoomType Name Test');
        $this->assertEquals('RoomType Name Test', $roomType->getName());
    }

    public function testSetDescription()
    {
        $roomType = new RoomType();

        $roomType->setDescription('Room Type Description Test');
        $this->assertEquals('Room Type Description Test', $roomType->getDescription());
    }

    public function testSetIsActive()
    {
        $roomType = new RoomType();

        $roomType->setIsActive(FALSE);
        $this->assertEquals(FALSE, $roomType->getIsActive());
        $roomType->setIsActive(TRUE);
        $this->assertEquals(TRUE, $roomType->getIsActive());
    }

    public function testAddRoom()
    {
        $room = new Room;
        $roomType = new RoomType();
        $arrRooms = new \Doctrine\Common\Collections\ArrayCollection();
        $arrRooms[] = $room;

        $roomType->addRoom($room);
        $this->assertEquals($arrRooms, $roomType->getRooms());
    }

    public function testRemoveRoom()
    {
        $room = new Room;
        $roomType = new RoomType();
        $arrRooms = new \Doctrine\Common\Collections\ArrayCollection();
        $arrRooms[] = $room;

        $roomType->addRoom($room);

        $roomType->removeRoom($room);
        $this->assertEquals(0, count($roomType->getRooms()));
    }

    public function testSetCreatedAt()
    {
        $roomType = new RoomType();
        $date = new \DateTime('2014-12-12');

        $roomType->setCreatedAt($date);
        $this->assertEquals($date, $roomType->getCreatedAt());
    }

    public function testSetUpdatedAt()
    {
        $roomType = new RoomType();
        $date = new \DateTime('2014-12-12');

        $roomType->setUpdatedAt($date);
        $this->assertEquals($date, $roomType->getUpdatedAt());
    }

    public function testCountRooms()
    {
        $room = new Room;
        $roomType = new RoomType();
        $arrRooms = new \Doctrine\Common\Collections\ArrayCollection();
        $arrRooms[] = $room;

        $roomType->addRoom($room);

        $this->assertEquals(1, count($roomType->countRooms()));
    }

    public function testToArray(){
        $room = new Room;

        $roomType = new RoomType();
        $roomType->setName('roomType name');
        $roomType->setDescription('roomType description');
        $roomType->setIsActive(TRUE);
        $roomType->addRoom($room);

        $roomTypeComparative = array(
            'id' => $roomType->getId(),
            'name' => 'roomType name',
            'description' => 'roomType description',
            'is_active' => TRUE,
            'nb_rooms' => 1
        );

        $this->assertEquals($roomTypeComparative,$roomType->toArray());
    }

    // TODO
    public function testSetTranslatableLocale() {

    }

}