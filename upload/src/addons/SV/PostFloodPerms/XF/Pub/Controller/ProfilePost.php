<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use SV\StandardLib\Helper;
use XF\Entity\ProfilePost as ProfilePostEntity;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Exception as ExceptionAlias;

/**
 * @extends \XF\Pub\Controller\ProfilePost
 */
class ProfilePost extends XFCP_ProfilePost
{
    /** @var ProfilePostEntity|null */
    protected $svFloodProfilePost = null;

    public function actionAddComment(ParameterBag $params)
    {
        $this->assertPostOnly();

        /** @noinspection PhpUndefinedFieldInspection */
        $profilePostId = $params->profile_post_id;

        $this->svFloodProfilePost = $this->assertViewableProfilePost($profilePostId);
        try
        {
            return parent::actionAddComment($params);
        }
        finally
        {
            $this->svFloodProfilePost = null;
        }
    }

    public function actionReact(ParameterBag $params)
    {
        if ($this->isPost())
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $profilePost = $this->assertViewableProfilePost($params->profile_post_id);
            if (!$profilePost->canReact($error))
            {
                return $this->noPermission($error);
            }
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodCheck->assertNotFlooding('profilePost',
                'React', 'profile_react',
                'pr', $profilePost->profile_user_id
            );
        }

        return parent::actionReact($params);
    }

    public function actionCommentsReact(ParameterBag $params)
    {
        if ($this->isPost())
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $comment = $this->assertViewableComment($params->profile_post_comment_id);
            if (!$comment->canReact($error))
            {
                return $this->noPermission($error);
            }
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodCheck->assertNotFlooding('profilePost',
                'React', 'profile_react',
                'pr', $comment->ProfilePost->profile_user_id
            );
        }

        return parent::actionCommentsReact($params);
    }

    /**
     * @param string $action
     * @param ?int $floodingLimit
     * @return void
     * @throws ExceptionAlias
     */
    public function assertNotFlooding($action, $floodingLimit = null)
    {
        if ($this->svFloodProfilePost !== null && $action === 'post')
        {
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodChecked = $floodCheck->assertNotFlooding('profilePost',
                'Post', 'profile_post',
                'pp', $this->svFloodProfilePost->profile_user_id
            );

            if ($floodChecked)
            {
                return;
            }
        }

        parent::assertNotFlooding($action, $floodingLimit);
    }

    public function responseFlooding($floodSeconds)
    {
        return Helper::plugin($this, FloodCheckPlugin::class)->responseFlooding($floodSeconds);
    }
}