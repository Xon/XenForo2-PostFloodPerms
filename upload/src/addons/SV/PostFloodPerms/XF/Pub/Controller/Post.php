<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use SV\PostFloodPerms\ControllerPlugin\FloodCheck as FloodCheckPlugin;
use SV\StandardLib\Helper;
use XF\Mvc\ParameterBag;

/**
 * @extends \XF\Pub\Controller\Post
 */
class Post extends XFCP_Post
{
    public function actionReact(ParameterBag $params)
    {
        if ($this->isPost())
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $post = $this->assertViewablePost($params->post_id);
            if (!$post->canReact($error))
            {
                return $this->noPermission($error);
            }
            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodCheck->assertNotFlooding('forum',
                'React', 'thread_react',
                'tr', $post->thread_id,
                'nr', $post->Thread->node_id
            );
        }

        return parent::actionReact($params);
    }

    public function actionDelete(ParameterBag $params)
    {
        if ($this->isPost())
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $post = $this->assertViewablePost($params->post_id);
            $type = $this->filter('hard_delete', 'bool') ? 'hard' : 'soft';
            if (!$post->canDelete($type, $error))
            {
                return $this->noPermission($error);
            }

            $floodCheck = Helper::plugin($this, FloodCheckPlugin::class);
            $floodCheck->assertNotFlooding('forum',
                'Delete', 'thread_delete',
                'td', $post->thread_id,
                'nd', $post->Thread->node_id
            );
        }

        return parent::actionDelete($params);
    }

    public function responseFlooding($floodSeconds)
    {
        return Helper::plugin($this, FloodCheckPlugin::class)->responseFlooding($floodSeconds);
    }
}