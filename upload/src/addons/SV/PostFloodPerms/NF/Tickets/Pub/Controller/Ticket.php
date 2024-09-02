<?php

namespace SV\PostFloodPerms\NF\Tickets\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Exception as ExceptionAlias;

/**
 * @extends \NF\Tickets\Pub\Controller\Ticket
 */
class Ticket extends XFCP_Ticket
{
    /** @var bool */
    protected $svDoFloodCheck = false;
    /** @var \NF\Tickets\Entity\Ticket|null */
    protected $svFloodTicket = null;

    public function actionAddMessage(ParameterBag $params)
    {
        $this->assertPostOnly();

        /** @noinspection PhpUndefinedFieldInspection */
        $ticketId = (int)$params->ticket_id;

        $this->svDoFloodCheck = true;
        $this->svFloodTicket = $this->assertViewableTicket($ticketId);
        try
        {
            return parent::actionAddMessage($params);
        }
        finally
        {
            $this->svDoFloodCheck = false;
            $this->svFloodTicket = null;
        }
    }

    /**
     * @param string $action
     * @param ?int $floodingLimit
     * @return void
     * @throws ExceptionAlias
     */
    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($this->svDoFloodCheck && $action === 'nf_tickets_message')
        {
            /** @var FloodCheckPlugin $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodChecked = $floodCheck->assertNotFlooding('ticket',
                'Post', 'ticket_message',
                'ticket', $this->svFloodTicket->ticket_id ?? 0
            );

            if ($floodChecked)
            {
                return;
            }
        }

        parent::assertNotFlooding($action, $floodingLimit);
    }
}