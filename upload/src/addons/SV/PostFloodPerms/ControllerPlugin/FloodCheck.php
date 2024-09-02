<?php

namespace SV\PostFloodPerms\ControllerPlugin;

use XF\ControllerPlugin\AbstractPlugin;
use XF\Mvc\Reply\Exception as ReplyException;
use XF\Pub\Controller\AbstractController;
use function strlen;

class FloodCheck extends AbstractPlugin
{
    /**
     * @param string $permGroup
     * @param string $type
     * @param string $prefixGeneral
     * @param string $prefixItem
     * @param int    $itemId
     * @param string $prefixContainer
     * @param int    $containerId
     * @return bool
     * @throws ReplyException
     */
    public function assertNotFlooding(string $permGroup, string $type, string $prefixGeneral, string $prefixItem, int $itemId,  string $prefixContainer = '', int $containerId = 0): bool
    {
        $controller = $this->controller;
        if (!($controller instanceof AbstractController))
        {
            return false;
        }

        $visitor = \XF::visitor();

        if ($visitor->hasPermission('general', 'bypassFloodCheck'))
        {
            return false;
        }

        if ($itemId !== 0 && strlen($prefixItem) !== 0 && $visitor->hasPermission($permGroup, 'svFlood' . $type . 'ItemOn'))
        {
            $rateLimit = (int)$visitor->hasPermission($permGroup, 'svFlood' . $type . 'Item');
            if ($rateLimit < 0)
            {
                return true;
            }
            else if ($rateLimit > 0)
            {
                $controller->assertNotFlooding($prefixItem . $itemId, $rateLimit);

                return true;
            }
        }

        if ($containerId !== 0 && strlen($prefixContainer) !== 0 && $visitor->hasPermission($permGroup, 'svFlood' . $type . 'ContainerOn'))
        {
            $rateLimit = (int)$visitor->hasPermission($permGroup, 'svFlood' . $type . 'Container');
            if ($rateLimit < 0)
            {
                return true;
            }
            else if ($rateLimit > 0)
            {
                $controller->assertNotFlooding($prefixContainer . $containerId, $rateLimit);

                return true;
            }
        }

        if (strlen($prefixGeneral) !== 0)
        {
            $rateLimit = (int)$visitor->hasPermission($permGroup, 'svFlood' . $type . 'General');
            if ($rateLimit < 0)
            {
                return true;
            }
            else if ($rateLimit > 0)
            {
                // do not user the $action, as that is shared with other stuff (ie reporting)
                $controller->assertNotFlooding($prefixGeneral, $rateLimit);

                return true;
            }
        }

        return false;
    }
}