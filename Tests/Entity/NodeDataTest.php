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

use Ydle\HubBundle\Entity\NodeData;
use Ydle\HubBundle\Entity\Node;
use Ydle\HubBundle\Entity\NodeType;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCase;

class NodeDataTest extends WebTestCase 
{

    protected $em;

    public function testSetData()
    {
        $nodeData = new NodeData();
        $float = 3.5;

        $nodeData->setData($float);
        $this->assertEquals($float, $nodeData->getData());
    }

    public function testSetType()
    {
        $nodeData = new NodeData();
        $nodeType = new NodeType();
        $nodeType->setName('NodeType Test');
        $nodeType->setIsActive(1);

        $nodeData->setType($nodeType);
        $this->assertEquals($nodeType, $nodeData->getType());
    }

    public function testSetNode()
    {
        $nodeData = new NodeData();
        $node = new Node();
        $node->setName('Node Test');

        $nodeData->setNode($node);
        $this->assertEquals($node, $nodeData->getNode());
    }
    public function testSetCreated()
    {
        $nodeData = new NodeData();
        $date = new \DateTime('2014-12-12');

        $nodeData->setCreated($date);
        $this->assertEquals($date, $nodeData->getCreated());
    }

    public function testSetUpdated()
    {
        $nodeData = new NodeData();
        $date = new \DateTime('2014-12-12');

        $nodeData->setUpdated($date);
        $this->assertEquals($date, $nodeData->getUpdated());
    }
}