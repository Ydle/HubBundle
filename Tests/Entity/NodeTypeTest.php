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

use Ydle\HubBundle\Entity\NodeType;

class NodeTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testSetName()
    {
        $nodeType = new NodeType();

        $nodeType->setName('NodeType Name Test');
        $this->assertEquals('NodeType Name Test', $nodeType->getName());
    }

    public function testSetDescription()
    {
        $nodeType = new NodeType();

        $nodeType->setDescription('NodeType Description Test');
        $this->assertEquals('NodeType Description Test', $nodeType->getDescription());
    }

    // TODO : Remplacer le contenu de la variable unit
    public function testSetUnit()
    {
        $nodeType = new NodeType();
        $unit = "unit";

        $nodeType->setUnit($unit);
        $this->assertEquals($unit,$nodeType->getUnit());
    }

    public function testSetIsActive()
    {
        $nodeType = new NodeType();

        $nodeType->setIsActive(FALSE);
        $this->assertEquals(FALSE, $nodeType->getIsActive());
        $nodeType->setIsActive(TRUE);
        $this->assertEquals(TRUE, $nodeType->getIsActive());
    }

    public function testToString() {
        $nodeType = new NodeType();
        $name = "NodeType Name";
        
        $nodeType->setName($name);

        $this->assertEquals($name,$nodeType->__toString());
    }

    public function testSetCreatedAt()
    {
        $nodeType = new NodeType();
        $date = new \DateTime('2014-12-12');

        $nodeType->setCreatedAt($date);
        $this->assertEquals($date, $nodeType->getCreatedAt());
    }

    public function testSetUpdatedAt()
    {
        $nodeType = new NodeType();
        $date = new \DateTime('2014-12-12');

        $nodeType->setUpdatedAt($date);
        $this->assertEquals($date, $nodeType->getUpdatedAt());
    }

    // TODO : Remplacer le contenu de la variable unit
    public function testToArray()
    {
        $nodeType = new NodeType();
        $nodeType->setName('nodeType name');
        $nodeType->setDescription('nodeType description');
        $nodeType->setIsActive(TRUE);
        $nodeType->setUnit("nodeTypeUnit");

        $nodeTypeComparative = array(
            'id' => $nodeType->getId(),
            'name' => 'nodeType name',
            'description' => 'nodeType description',
            'is_active' => TRUE,
            'unit' => 'nodeTypeUnit'
        );

        $this->assertEquals($nodeTypeComparative,$nodeType->toArray());
    }

}