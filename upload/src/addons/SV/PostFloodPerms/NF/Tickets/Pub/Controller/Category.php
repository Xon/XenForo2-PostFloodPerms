<?php

namespace SV\PostFloodPerms\NF\Tickets\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use SV\StandardLib\Helper;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Exception as ReplyException;

/**
 * @extends \NF\Tickets\Pub\Controller\Category
 */
class Category extends XFCP_Category
{
    /** @var int */
    protected $svFloodCheckCategoryId = 0;

    /** @noinspection PhpMissingReturnTypeInspection */
    public function actionCreate(ParameterBag $parameterBag)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $category = $this->assertViewableCategory($parameterBag->ticket_category_id);
        $this->svFloodCheckCategoryId = $category->ticket_category_id;
        try
        {
            return parent::actionCreate($parameterBag);
        }
        finally
        {
            $this->svFloodCheckCategoryId = 0;
        }
    }

    /**
     * @param string $action
     * @param ?int $floodingLimit
     * @return void
     * @throws ReplyException
     */
    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($this->svFloodCheckCategoryId !== 0 && $action === 'ticket')
        {
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodChecked = $floodCheck->assertNotFlooding('ticket',
                'Post', 'ticket_message',
                'ticket', 0,
                'ticketCat', $this->svFloodCheckCategoryId,
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