<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use SV\StandardLib\Helper;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Exception as ReplyException;

/**
 * @extends \XF\Pub\Controller\Forum
 */
class Forum extends XFCP_Forum
{
    /** @var int */
    protected $svFloodCheckNodeId = 0;

    public function actionPostThread(ParameterBag $params)
    {
        $nodeId = $params->node_id ?? 0;
        $nodeName = $params->node_name ?? '';
        if ($nodeId || $nodeName)
        {
            $forum = $this->assertViewableForum($nodeId ?: $nodeName, ['DraftThreads|' . \XF::visitor()->user_id]);
            $this->svFloodCheckNodeId = $forum->node_id;
        }
        try
        {
            return parent::actionPostThread($params);
        }
        finally
        {
            $this->svFloodCheckNodeId = 0;
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
        if ($this->svFloodCheckNodeId && $action === 'thread')
        {
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodChecked = $floodCheck->assertNotFlooding('forum',
                'Thread', 'thread_new',
                '', 0,
                'n', $this->svFloodCheckNodeId
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