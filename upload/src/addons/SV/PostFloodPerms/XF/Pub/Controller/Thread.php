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
        $thread = $this->assertViewableThread($params->thread_id, ['Watch|' . $visitor->user_id]);
        if (!$thread->canReply($error))
        {
            return $this->noPermission($error);
        }

        $visitor = \XF::visitor();
        if (!$visitor->hasPermission('general', 'bypassFloodCheck'))
        {
            $this->svFloodThread = $thread;
        }

        try
        {
            $response = parent::actionAddReply($params);
        }
        finally
        {
            $this->svFloodThread = null;
        }

        return $response;
    }


    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($action === 'post' && $this->svFloodThread !== null)
        {
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding($this->svFloodThread,'post', 't', 'n', 'post_');
        }
        parent::assertNotFlooding($action, $floodingLimit);
    }
}