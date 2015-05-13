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

use Ydle\HubBundle\Entity\Node;
use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\Room;

class NodeTest extends \PHPUnit_Framework_TestCase
{

    public function testSetName()
    {
        $node = new Node();

        $node->setName('Node Name Test');
        $this->assertEquals('Node Name Test', $node->getName());
    }

    public function testSetCode()
    {
        $node = new Node();

        $node->setCode('NodeCode');
        $this->assertEquals('NodeCode', $node->getCode());
    }

    public function testSetDescription()
    {
        $node = new Node();

        $node->setDescription('Node Description Test');
        $this->assertEquals('Node Description Test', $node->getDescription());
    }

    public function testSetIsActive()
    {
        $node = new Node();

        $node->setIsActive(FALSE);
        $this->assertEquals(FALSE, $node->getIsActive());
        $node->setIsActive(TRUE);
        $this->assertEquals(TRUE, $node->getIsActive());
    }
 
    // Voir comment on créer un RoomType
    public function testSetType()
    {
        $node = new Node();

        $typeNode = new NodeType();
        $typeNode->setName('Node Type Test');
        $typeNode->setIsActive(true);
        $node->setTypes($typeNode);
        $this->assertEquals($typeNode, $node->getTypes());

        $typeNodeB = new NodeType();
        $typeNodeB->setName('Node Type Test 2');
        $typeNodeB->setIsActive(true);
        $node->setTypes($typeNode);
    }

    // AddType fonctionne mal ?
    public function testAddType()
    {
        $node = new Node();

        $typeNode = new NodeType();
        $typeNode->setName('Node Type Test');
        $typeNode->setIsActive(true);
        $node->setTypes($typeNode);

        $typeNodeB = new NodeType();
        $typeNodeB->setName('Node Type Test 2');
        $typeNodeB->setIsActive(true);
//      $node->addType($typeNode2);

        // Le test est probablement faux
//      $this->assertEquals($typeNode, $node->getTypes());
    }

    // TODO gestion de la clef + 'Bug' addType
    public function testRemoveType()
    {
        $node = new Node();

        $typeNode = new NodeType();
        $typeNode->setName('Node Type Test');
        $typeNode->setIsActive(true);
        $node->setTypes($typeNode);

        $typeNodeB = new NodeType();
        $typeNodeB->setName('Node Type Test 2');
        $typeNodeB->setIsActive(true);

//      $node->addType($typeNode2);

/*
        $key = 0;
        $node->removeType($key);
        $this->assertEquals(1, count($node->getTypes()));
        $this->assertEquals($typdeNode2, $node->getTypes());
*/
    }

    // TODO, il va falloir faire un persist + flush pour générer l'ID du nodeType
    public function testHasType()
    {
        $node = new Node();

        $typeNode = new NodeType();
        $typeNode->setName('Node Type Test');
        $typeNode->setIsActive(true);
        $node->setTypes($typeNode);

        $typeNodeB = new NodeType();
        $typeNodeB->setName('Node Type Test 2');
        $typeNodeB->setIsActive(true);

//var_dump($node->hasType($typeNode));
//      $this->assertEquals(TRUE, $node->hasType($typeNode));
//      $this->assertEquals(FALSE, $node->hasType($typeNode2));

    }

    public function testSetRoom()
    {
        $node = new Node();
        $room = new Room();

        $node->setRoom($room);
        $this->assertEquals($room, $node->getRoom());
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
    
    // TODO
    public function testHasTypes()
    {
    }

}