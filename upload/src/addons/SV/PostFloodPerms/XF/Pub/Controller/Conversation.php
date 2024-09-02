<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use XF\Entity\ConversationMaster;
use XF\Mvc\ParameterBag;

/**
 * @extends \XF\Pub\Controller\Conversation
 */
class Conversation extends XFCP_Conversation
{
    /** @var ConversationMaster|null */
    protected $svFloodConversation = null;
    /** @var bool */
    protected $svDoFloodCheck = false;

    public function actionMessagesReact(ParameterBag $params)
    {
        if ($this->isPost())
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $message = $this->assertViewableMessage($params->message_id);
            if (!$message->canReact($error))
            {
                return $this->noPermission($error);
            }
            /** @var FloodCheckPlugin $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding('conversation',
                'React', 'conversation_react',
                'cr', $message->conversation_id
            );
        }

        return parent::actionMessagesReact($params);
    }

    public function actionAddReply(ParameterBag $params)
    {
        $this->assertPostOnly();

        /** @noinspection PhpUndefinedFieldInspection */
        $userConv = $this->assertViewableUserConversation($params->conversation_id);
        $this->svDoFloodCheck = true;
        $this->svFloodConversation = $userConv->Master ?? null;
        try
        {
            return parent::actionAddReply($params);
        }
        finally
        {
            $this->svDoFloodCheck = false;
            $this->svFloodConversation = null;
        }
    }

    public function actionAdd()
    {
        $this->svDoFloodCheck = true;
        $this->svFloodConversation = null;
        try
        {
            return parent::actionAdd();
        }
        finally
        {
            $this->svDoFloodCheck = false;
        }
    }

    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($this->svDoFloodCheck && ($action === 'conversation' || $action === 'conversation_message'))
        {
            /** @var FloodCheckPlugin $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodChecked = $floodCheck->assertNotFlooding('conversation',
                'Post', 'conversation_post',
                'c', $this->svFloodConversation->conversation_id ?? 0
            );

            if ($floodChecked)
            {
                return;
            }
        }

        parent::assertNotFlooding($action, $floodingLimit);
    }
}