<?php

namespace SV\PostFloodPerms\NF\Tickets\Pub\Controller;

use NF\Tickets\Entity\Ticket as TicketEntity;
use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use SV\StandardLib\Helper;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Exception as ExceptionAlias;

/**
 * @extends \NF\Tickets\Pub\Controller\Ticket
 */
class Ticket extends XFCP_Ticket
{
    /** @var bool */
    protected $svDoFloodCheck = false;
    /** @var TicketEntity|null */
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
        if ($this->svDoFloodCheck && $this->svFloodTicket !== null && $action === 'nf_tickets_message')
        {
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodChecked = $floodCheck->assertNotFlooding('ticket',
                'Post', 'ticket_message',
                'ticket', $this->svFloodTicket->ticket_id,
                'ticketCat', $this->svFloodTicket->ticket_category_id,
                'nf_tickets_category'
            );

            if ($floodChecked)
            {
                return;
            }
        }

        parent::assertNotFlooding($action, $floodingLimit);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function responseFlooding($floodSeconds)
    {
        return Helper::plugin($this, FloodCheckPlugin::class)->responseFlooding($floodSeconds);
    }
}