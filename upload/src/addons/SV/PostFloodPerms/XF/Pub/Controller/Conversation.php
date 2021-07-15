<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;



use XF\Mvc\ParameterBag;

/**
 * Extends \XF\Pub\Controller\Conversation
 */
class Conversation extends XFCP_Conversation
{
    /** @var \XF\Entity\ConversationMaster */
    protected $svFloodConversation;
    /** @var bool */
    protected $svDoFloodCheck;

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
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding('conversation', 'React',
                'cr', $message->conversation_id,
                'conversation_react'
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
        if ($action === 'post' && $this->svDoFloodCheck)
        {
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding('conversation', 'Post',
                't', $this->svFloodConversation->conversation_id ?? 0,
                'conversation_post'
            );
        }

        parent::assertNotFlooding($action, $floodingLimit);
    }
}