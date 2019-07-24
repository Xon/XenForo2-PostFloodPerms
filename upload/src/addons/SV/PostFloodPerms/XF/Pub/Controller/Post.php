<?php

namespace SV\PostFloodPerms\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

/**
 * Extends \XF\Pub\Controller\Post
 */
class Post extends XFCP_Post
{
    /**
     * XF2.0 support
     *
     * @param ParameterBag $params
     * @return mixed
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionLike(ParameterBag $params)
    {
        if (\XF::$versionId > 2010000)
        {
            return $this->notFound();
        }

        if ($this->isPost())
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $post = $this->assertViewablePost($params->post_id);
            /** @noinspection PhpUndefinedMethodInspection */
            if (!$post->canLike($error))
            {
                return $this->noPermission($error);
            }
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding($post->Thread,'like', 'dlt', 'ln', 'like');
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return parent::actionLike($params);
    }

    /**
     * XF2.1 support
     *
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Message|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionReact(ParameterBag $params)
    {
        if (\XF::$versionId < 2010000)
        {
            return $this->notFound();
        }

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
            $floodCheck->assertNotFlooding($post->Thread,'like', 'dlt', 'ln', 'like');
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
            /** @var \SV\PostFloodPerms\ControllerPlugin\FloodCheck $floodCheck */
            $floodCheck = $this->plugin('SV\PostFloodPerms:FloodCheck');
            $floodCheck->assertNotFlooding($post->Thread,'delete', 'dt', 'dn', 'delete');
        }

        return parent::actionDelete($params);
    }
}