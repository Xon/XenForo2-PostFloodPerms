<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\PostFloodPerms\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

/**
 * Extends \XF\Pub\Controller\Thread
 */
class Thread extends XFCP_Thread
{
    /** @var \XF\Entity\Thread */
    protected $svFloodThread = null;

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\AbstractReply
     * @throws \XF\Mvc\Reply\Exception
     */
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

    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($action === 'post' && $this->svFloodThread !== null)
        {
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding('forum', 'Post',
                't', $this->svFloodThread->thread_id,
                'thread_post',
                'n', $this->svFloodThread->node_id
            );
        }
        parent::assertNotFlooding($action, $floodingLimit);
    }
}