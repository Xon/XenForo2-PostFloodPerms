<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use SV\StandardLib\Helper;
use XF\Entity\ProfilePost as ProfilePostEntity;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Exception as ExceptionAlias;

/**
 * @extends \XF\Pub\Controller\Member
 */
class Member extends XFCP_Member
{
    /** @var int */
    protected $svFloodCheckUserId = 0;

    public function actionPost(ParameterBag $params)
    {
        $this->assertPostOnly();

        /** @noinspection PhpUndefinedFieldInspection */
        $userId = $params->user_id;

        $this->svFloodCheckUserId = $userId;
        try
        {
            return parent::actionPost($params);
        }
        finally
        {
            $this->svFloodCheckUserId = 0;
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
        if ($this->svFloodCheckUserId !== 0 && $action === 'post')
        {
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodChecked = $floodCheck->assertNotFlooding('profilePost',
                'Post', 'profile_post',
                'pp', $this->svFloodCheckUserId
            );

            if ($floodChecked)
            {
                return;
            }
        }

        parent::assertNotFlooding($action, $floodingLimit);
    }
}