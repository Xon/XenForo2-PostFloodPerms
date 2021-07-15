<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

/**
 * Extends \XF\Pub\Controller\Thread
 */
class Thread extends XFCP_Thread
{
    /** @var \XF\Entity\Thread */
    var $floodThread  = null;

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
            $this->floodThread = $thread;
        }

        try
        {
            $response = parent::actionAddReply($params);
        }
        finally
        {
            $this->floodThread = null;
        }

        return $response;
    }


    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($action === 'post' && $this->floodThread)
        {
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding($this->floodThread,'post', 't', 'n', 'post_');
        }
        parent::assertNotFlooding($action, $floodingLimit);
    }
}