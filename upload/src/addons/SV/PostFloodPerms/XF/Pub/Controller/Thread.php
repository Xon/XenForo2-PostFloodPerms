<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use SV\StandardLib\Helper;
use XF\Entity\Thread as ThreadEntity;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Exception as ExceptionAlias;

/**
 * @extends \XF\Pub\Controller\Thread
 */
class Thread extends XFCP_Thread
{
    /** @var ThreadEntity|null */
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
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodChecked = $floodCheck->assertNotFlooding('forum',
                'Post', 'thread_post',
                't', $this->svFloodThread->thread_id,
                'n', $this->svFloodThread->node_id,
                'node'
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