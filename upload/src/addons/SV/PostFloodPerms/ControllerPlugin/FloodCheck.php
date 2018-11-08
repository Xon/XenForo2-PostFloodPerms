<?php

namespace SV\PostFloodPerms\ControllerPlugin;

use XF\ControllerPlugin\AbstractPlugin;
use XF\Entity\Thread;

class FloodCheck extends AbstractPlugin
{
    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @param Thread $thread
     * @param string $type
     * @param string $prefixThread
     * @param string $prefixNode
     * @param string $prefixGeneral
     */
    public function assertNotFlooding(Thread $thread, $type, $prefixThread, $prefixNode, $prefixGeneral)
    {
        $controller = $this->controller;
        if (!($controller instanceof \XF\Pub\Controller\AbstractController))
        {
            return;
        }

        $visitor = \XF::visitor();


        if ($thread->thread_id && $visitor->hasPermission('forum', 'sv_' . $type . 'flood_thread_on'))
        {
            $rateLimit = $visitor->hasPermission('forum', 'sv_' . $type . 'flood_thread');
            if ($rateLimit < 0)
            {
                return;
            }
            else if ($rateLimit > 0)
            {
                $controller->assertNotFlooding($prefixThread . $thread->thread_id, $rateLimit);

                return;
            }
        }

        if ($thread->node_id && $visitor->hasPermission('forum', 'sv_' . $type . 'flood_node_on'))
        {
            $rateLimit = $visitor->hasPermission('forum', 'sv_' . $type . 'flood_node');
            if ($rateLimit < 0)
            {
                return;
            }
            else if ($rateLimit > 0)
            {
                $controller->assertNotFlooding($prefixNode . $thread->node_id, $rateLimit);

                return;
            }
        }

        $rateLimit = $visitor->hasPermission('forum', 'sv_' . $type . 'flood_general');
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