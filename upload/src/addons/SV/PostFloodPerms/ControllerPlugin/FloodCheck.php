<?php

namespace SV\PostFloodPerms\ControllerPlugin;

use XF\ControllerPlugin\AbstractPlugin;

class FloodCheck extends AbstractPlugin
{
    /**
     * @param string $permGroup
     * @param int    $itemId
     * @param int    $containerId
     * @param string $type
     * @param string $prefixItem
     * @param string $prefixContainer
     * @param string $prefixGeneral
     * @throws \XF\Mvc\Reply\Exception
     */
    public function assertNotFlooding(string $permGroup, int $itemId, int $containerId, string $type, string $prefixItem, string $prefixContainer, string $prefixGeneral)
    {
        $controller = $this->controller;
        if (!($controller instanceof \XF\Pub\Controller\AbstractController))
        {
            return;
        }

        $visitor = \XF::visitor();

        if ($itemId && $visitor->hasPermission($permGroup, 'svFlood' . $type . 'ItemOn'))
        {
            $rateLimit = $visitor->hasPermission($permGroup, 'svFlood' . $type . 'Item');
            if ($rateLimit < 0)
            {
                return;
            }
            else if ($rateLimit > 0)
            {
                $controller->assertNotFlooding($prefixItem . $itemId, $rateLimit);

                return;
            }
        }

        if ($containerId && $visitor->hasPermission($permGroup, 'svFlood' . $type . 'ContainerOn'))
        {
            $rateLimit = $visitor->hasPermission($permGroup, 'svFlood' . $type . 'Container');
            if ($rateLimit < 0)
            {
                return;
            }
            else if ($rateLimit > 0)
            {
                $controller->assertNotFlooding($prefixContainer . $containerId, $rateLimit);

                return;
            }
        }

        $rateLimit = $visitor->hasPermission($permGroup, 'svFlood' . $type . 'General');
        if ($rateLimit < 0)
        {
            return;
        }
        else if ($rateLimit > 0)
        {
            // do not user the $action, as that is shared with other stuff (ie reporting)
            $controller->assertNotFlooding($prefixGeneral, $rateLimit);

            return;
        }
    }
}