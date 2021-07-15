<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\PostFloodPerms\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

/**
 * Extends \XF\Pub\Controller\Forum
 */
class Forum extends XFCP_Forum
{
    /** @var int */
    protected $svFloodCheckNodeId = 0;

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\AbstractReply
     */
    public function actionAddReply(ParameterBag $params)
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

    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($this->svFloodCheckNodeId && $action === 'thread')
        {
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodChecked = $floodCheck->assertNotFlooding('forum',
                'Thread', 'thread_new',
                'n', $this->svFloodCheckNodeId
            );

            if ($floodChecked)
            {
                return;
            }
        }
        parent::assertNotFlooding($action, $floodingLimit);
    }
}