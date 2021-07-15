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
        if ($this->svFloodThread !== null && $action === 'post')
        {
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
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