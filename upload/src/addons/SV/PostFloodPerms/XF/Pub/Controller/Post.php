<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

/**
 * Extends \XF\Pub\Controller\Post
 */
class Post extends XFCP_Post
{
    public function actionLike(ParameterBag $params)
    {
        if ($this->isPost())
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $post = $this->assertViewablePost($params->post_id);
            if (!$post->canLike($error))
            {
                return $this->noPermission($error);
            }
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding($post->Thread,'like', 'dlt', 'ln', 'like');
        }

        return parent::actionLike($params);
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
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding($post->Thread,'delete', 'dt', 'dn', 'delete');
        }

        return parent::actionDelete($params);
    }
}