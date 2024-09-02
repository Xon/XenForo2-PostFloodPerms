<?php

namespace SV\PostFloodPerms\NF\Tickets\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use XF\Mvc\Reply\Exception as ExceptionAlias;

/**
 * @extends \NF\Tickets\Pub\Controller\Category
 */
class Category extends XFCP_Category
{
    /**
     * @param string $action
     * @param ?int $floodingLimit
     * @return void
     * @throws ExceptionAlias
     */
    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($action === 'ticket')
        {
            /** @var FloodCheckPlugin $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodChecked = $floodCheck->assertNotFlooding('ticket',
                'Post', 'ticket_message',
                'ticket', 0
            );

            if ($floodChecked)
            {
                return;
            }
        }

        parent::assertNotFlooding($action, $floodingLimit);
    }
}