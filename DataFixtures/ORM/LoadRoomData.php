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
use Ydle\HubBundle\Entity\Room;
use Ydle\HubBundle\Entity\RoomType;

class LoadRoomData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $typeLivingRoom = new RoomType();
        $typeLivingRoom->setName('Living room');
        $typeLivingRoom->setIsActive(true);

        $typeKitchen = new RoomType();
        $typeKitchen->setName('Kitchen');
        $typeKitchen->setIsActive(true);

        $typeBedroom = new RoomType();
        $typeBedroom->setName('Bedroom');
        $typeBedroom->setIsActive(true);
		
        $typeAttic = new RoomType();
        $typeAttic->setName('Attic');
        $typeAttic->setIsActive(true);
		
        $typeGarage = new RoomType();
        $typeGarage->setName('Garage');
        $typeGarage->setIsActive(true);
		
        $typeBathroom = new RoomType();
        $typeBathroom->setName('Bathroom');
        $typeBathroom->setIsActive(true);
		
        $typeToilet = new RoomType();
        $typeToilet->setName('Toilet');
        $typeToilet->setIsActive(true);

	$typeOffice = new RoomType();
        $typeOffice->setName('Office');
        $typeOffice->setIsActive(true);
		
        $typeCellar = new RoomType();
        $typeCellar->setName('Cellar');
        $typeCellar->setIsActive(true);
		
        $typeDressing = new RoomType();
        $typeDressing->setName('Dressing');
        $typeDressing->setIsActive(true);

        $bedroom = new Room();
        $bedroom->setName('My bedroom');
        $bedroom->setDescription('Test bedroom');
        $bedroom->setType($typeBedroom);
        $bedroom->setIsActive(true);

        $manager->persist($typeLivingRoom);
        $manager->persist($typeKitchen);
        $manager->persist($typeBedroom);
        $manager->persist($typeAttic);
        $manager->persist($typeGarage);
        $manager->persist($typeBathroom);
        $manager->persist($typeToilet);
        $manager->persist($typeOffice);
        $manager->persist($typeCellar);
        $manager->persist($typeDressing);

        $manager->persist($bedroom);
        $manager->flush();
    }
}
