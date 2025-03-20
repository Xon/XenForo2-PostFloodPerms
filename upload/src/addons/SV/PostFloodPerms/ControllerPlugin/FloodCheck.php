<?php

namespace SV\PostFloodPerms\ControllerPlugin;

use XF\ControllerPlugin\AbstractPlugin;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\Reply\Exception as ReplyException;
use XF\Pub\Controller\AbstractController;
use function count;
use function implode;
use function round;
use function strlen;

class FloodCheck extends AbstractPlugin
{
    /**
     * @param string $permGroup
     * @param string $type
     * @param string $prefixGeneral
     * @param string $prefixItem
     * @param int    $itemId
     * @param string $prefixContainer
     * @param int    $containerId
     * @return bool
     * @throws ReplyException
     */
    public function assertNotFlooding(string $permGroup, string $type, string $prefixGeneral, string $prefixItem, int $itemId,  string $prefixContainer = '', int $containerId = 0): bool
    {
        $controller = $this->controller;
        if (!($controller instanceof AbstractController))
        {
            return false;
        }

        $visitor = \XF::visitor();

        if ($visitor->hasPermission('general', 'bypassFloodCheck'))
        {
            return false;
        }

        if ($itemId !== 0 && strlen($prefixItem) !== 0 && $visitor->hasPermission($permGroup, 'svFlood' . $type . 'ItemOn'))
        {
            $rateLimit = (int)$visitor->hasPermission($permGroup, 'svFlood' . $type . 'Item');
            if ($rateLimit < 0)
            {
                return true;
            }
            else if ($rateLimit > 0)
            {
                $controller->assertNotFlooding($prefixItem . $itemId, $rateLimit);

                return true;
            }
        }

        if ($containerId !== 0 && strlen($prefixContainer) !== 0 && $visitor->hasPermission($permGroup, 'svFlood' . $type . 'ContainerOn'))
        {
            $rateLimit = (int)$visitor->hasPermission($permGroup, 'svFlood' . $type . 'Container');
            if ($rateLimit < 0)
            {
                return true;
            }
            else if ($rateLimit > 0)
            {
                $controller->assertNotFlooding($prefixContainer . $containerId, $rateLimit);

                return true;
            }
        }

        if (strlen($prefixGeneral) !== 0)
        {
            $rateLimit = (int)$visitor->hasPermission($permGroup, 'svFlood' . $type . 'General');
            if ($rateLimit < 0)
            {
                return true;
            }
            else if ($rateLimit > 0)
            {
                // do not user the $action, as that is shared with other stuff (ie reporting)
                $controller->assertNotFlooding($prefixGeneral, $rateLimit);

                return true;
            }
        }

        return false;
    }

    protected $timeParts = [
        24 * 60 * 60 => 'time.day',
        60 * 60 => 'time.hour',
        60 => 'time.minute',
        1 => 'time.second',
    ];

    public function responseFlooding(int $floodSeconds): AbstractReply
    {
        $timeFragments = [];
        foreach ($this->timeParts as $threshold => $phrase)
        {
            if ($floodSeconds < $threshold)
            {
                continue;
            }

            $part = (int)($floodSeconds / $threshold);
            if ($part === 0)
            {
                continue;
            }
            $floodSeconds -= $part * $threshold;

            $timeFragments[] = \XF::phrase($phrase . ($part === 1 ? '' : 's'), [
                'count' => $part,
            ]);

            if (count($timeFragments) >= 2)
            {
                break;
            }
        }

        if (count($timeFragments) === 0)
        {
            return $this->error(\XF::phrase('must_wait_x_seconds_before_performing_this_action', ['count' => $floodSeconds]));
        }

        return $this->error(\XF::phrase('svPostFloodPerms_must_wait_x_before_performing_this_action', [
            'time' => implode(', ', $timeFragments),
        ]));
    }
}