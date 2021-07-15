<?php

namespace SV\PostFloodPerms\ControllerPlugin;

use XF\ControllerPlugin\AbstractPlugin;

class FloodCheck extends AbstractPlugin
{
    /**
     * @param string      $permGroup
     * @param string      $type
     * @param string      $prefixItem
     * @param int         $itemId
     * @param string|null $prefixGeneral
     * @param string|null $prefixContainer
     * @param int|null    $containerId
     * @throws \XF\Mvc\Reply\Exception
     */
    public function assertNotFlooding(string $permGroup, string $type, string $prefixItem, int $itemId, string $prefixGeneral = null, string $prefixContainer = null, int $containerId = null)
    {
        $controller = $this->controller;
        if (!($controller instanceof \XF\Pub\Controller\AbstractController))
        {
            return;
        }

        $visitor = \XF::visitor();

        if ($visitor->hasPermission('general', 'bypassFloodCheck'))
        {
            return;
        }

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

        if ($containerId && \strlen($prefixContainer) && $visitor->hasPermission($permGroup, 'svFlood' . $type . 'ContainerOn'))
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

        if (\strlen($prefixGeneral))
        {
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
}