<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\PostFloodPerms\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

/**
 * Extends \XF\Pub\Controller\Post
 */
class Post extends XFCP_Post
{
    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\AbstractReply
     * @throws \XF\Mvc\Reply\Exception
     */
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
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding('forum',
                'React', 'thread_react',
                'tr', $post->thread_id,
                'nr', $post->Thread->node_id
            );
        }

        return parent::actionReact($params);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\AbstractReply
     * @throws \XF\Mvc\Reply\Exception
     */
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
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding('forum',
                'Delete', 'thread_delete',
                'td', $post->thread_id,
                'nd', $post->Thread->node_id
            );
        }

        return parent::actionDelete($params);
    }
}