<?php
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
namespace Ydle\HubBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ydle\HubBundle\Entity\NodeType;

class LoadNodesData implements FixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
    $nodeTypeTemperature = new NodeType();
        $nodeTypeTemperature->setName('Temperature');
        $nodeTypeTemperature->setUnit('°C');
        $nodeTypeTemperature->setIsActive(true);

        $nodeTypeHumidity = new NodeType();
        $nodeTypeHumidity->setName('Humidity');
        $nodeTypeHumidity->setUnit('%');
        $nodeTypeHumidity->setIsActive(true);

        $nodeTypePressure = new NodeType();
        $nodeTypePressure->setName('Pressure');
        $nodeTypePressure->setUnit('Pa');
        $nodeTypePressure->setIsActive(true);

        $nodeTypeLuminosity = new NodeType();
        $nodeTypeLuminosity->setName('Luminosity');
        $nodeTypeLuminosity->setUnit('lux');
        $nodeTypeLuminosity->setIsActive(true);

        $manager->persist($nodeTypeTemperature);
        $manager->persist($nodeTypeHumidity);
        $manager->persist($nodeTypePressure);
        $manager->persist($nodeTypeLuminosity);
        $manager->flush();

    }
}
