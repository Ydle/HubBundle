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
namespace Ydle\HubBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RoomType
 *
 * @ORM\Table(name="roomtype")
 * @ORM\Entity(repositoryClass="Ydle\HubBundle\Repository\RoomTypeRepository")
 */
class RoomType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Gedmo\Translatable
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Gedmo\Translatable
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="Room", mappedBy="type") 
     */
    protected $rooms;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return RoomType
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return RoomType
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return RoomType
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rooms = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add rooms
     *
     * @param \Ydle\HubBundle\Entity\Room $rooms
     * @return RoomType
     */
    public function addRoom(\Ydle\HubBundle\Entity\Room $rooms)
    {
        $this->rooms[] = $rooms;
    
        return $this;
    }

    /**
     * Remove rooms
     *
     * @param \Ydle\HubBundle\Entity\Room $rooms
     */
    public function removeRoom(\Ydle\HubBundle\Entity\Room $rooms)
    {
        $this->rooms->removeElement($rooms);
    }

    /**
     * Get rooms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Period
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Period
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    
    public function countRooms()
    {
        return $this->getRooms()->count();
    }
    
    public function getSlug()
    {
        return $this->slug;
    }
    
    /**
    * Custom toArray classe
    * 
    * @return array
    */
    public function toArray(){
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'is_active' => $this->getIsActive(),
            'nb_rooms' => $this->getRooms()->count()
        );
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
