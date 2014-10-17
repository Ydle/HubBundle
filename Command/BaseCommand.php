<?php

namespace Ydle\HubBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * This command can be used to re-generate the thumbnails for all uploaded medias.
 *
 * Useful if you have existing media content and added new formats.
 *
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    public function getDispatcher()
    {
        return  $this->getContainer()->get('event_dispatcher');
    }

    public function getRootDir()
    {
        return $this->getContainer()->get('kernel')->getRootDir();
    }

    public function getNodeManager()
    {
	return $this->getContainer()->get('ydle.node.manager');
    }

    public function getNodeTypeManager()
    {
        return $this->getContainer()->get('ydle.nodetype.manager');
    }

    public function getRoomManager()
    {
        return $this->getContainer()->get('ydle.room.manager');
    }

    public function getRoomTypeManager()
    {
        return $this->getContainer()->get('ydle.roomtype.manager');
    }

    public function getDataManager()
    {
        return $this->getContainer()->get('ydle.data.manager');
    }
}
