<?php

namespace Ydle\HubBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NodeData
 *
 * @ORM\Table(name="node_data")
 * @ORM\Entity(repositoryClass="Ydle\HubBundle\Repository\NodeDataRepository")
 */
class NodeData
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
     * @var float
     *
     * @ORM\Column(name="data", type="float")
     */
    private $data;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Ydle\NodesBundle\Entity\SensorType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Ydle\NodesBundle\Entity\Node", inversedBy="nodes")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
     */
    private $node;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;


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
     * Set data
     *
     * @param integer $data
     * @return NodeData
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return integer 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return NodeData
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set node
     *
     * @param integer $node
     * @return NodeData
     */
    public function setNode($node)
    {
        $this->node = $node;
    
        return $this;
    }

    /**
     * Get node
     *
     * @return integer 
     */
    public function getNode()
    {
        return $this->node;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }
    
    public function setCreated($created)
    {
        $this->created = $created;
    }
    
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }
}
