<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Exception as ExceptionAlias;

/**
 * Extends \XF\Pub\Controller\Thread
 */
class Thread extends XFCP_Thread
{
    /** @var \XF\Entity\Thread|null */
    protected $svFloodThread = null;

    public function actionAddReply(ParameterBag $params)
    {
        $this->assertPostOnly();

        $visitor = \XF::visitor();
        /** @noinspection PhpUndefinedFieldInspection */
        $threadId = $params->thread_id;

        $this->svFloodThread = $this->assertViewableThread($threadId, ['Watch|' . $visitor->user_id]);
        try
        {
            return parent::actionAddReply($params);
        }
        finally
        {
            $this->svFloodThread = null;
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
        if ($this->svFloodThread !== null && $action === 'post')
        {
            /** @var FloodCheckPlugin $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodChecked = $floodCheck->assertNotFlooding('forum',
                'Post', 'thread_post',
                't', $this->svFloodThread->thread_id,
                'n', $this->svFloodThread->node_id
            );

            if ($floodChecked)
            {
                return;
            }
        }
        parent::assertNotFlooding($action, $floodingLimit);
    }
}