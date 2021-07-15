<?php

namespace SV\PostFloodPerms;

use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

/**
 * Class Setup
 *
 * @package SV\ReportImprovements
 */
class Setup extends AbstractSetup
{
    use InstallerHelper;
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function upgrade2020000Step1()
    {
        $this->renamePermission('forum','sv_deleteflood_general', 'forum','svFloodDeleteGeneral');
        $this->renamePermission('forum','sv_deleteflood_node', 'forum','svFloodDeleteContainer');
        $this->renamePermission('forum','sv_deleteflood_node_on', 'forum','svFloodDeleteContainerOn');
        $this->renamePermission('forum','sv_deleteflood_thread', 'forum','svFloodDeleteItem');
        $this->renamePermission('forum','sv_deleteflood_thread_on', 'forum','svFloodDeleteItemOn');

        $this->renamePermission('forum','sv_likeflood_general', 'forum','svFloodReactGeneral');
        $this->renamePermission('forum','sv_likeflood_node', 'forum','svFloodReactContainer');
        $this->renamePermission('forum','sv_likeflood_node_on', 'forum','svFloodReactContainerOn');
        $this->renamePermission('forum','sv_likeflood_thread', 'forum','svFloodReactItem');
        $this->renamePermission('forum','sv_likeflood_thread_on', 'forum','svFloodReactItemOn');

        $this->renamePermission('forum','sv_postflood_general', 'forum','svFloodPostGeneral');
        $this->renamePermission('forum','sv_postflood_node', 'forum','svFloodPostContainer');
        $this->renamePermission('forum','sv_postflood_node_on', 'forum','svFloodPostContainerOn');
        $this->renamePermission('forum','sv_postflood_thread', 'forum','svFloodPostItem');
        $this->renamePermission('forum','sv_postflood_thread_on', 'forum','svFloodPostItemOn');
    }

    public function upgrade2020000Step2()
    {
        $this->renamePermission('forum','svFlood-DeleteContainerOn', 'forum','svFloodDeleteContainerOn');
        $this->renamePermission('forum','svFlood-DeleteItemOn', 'forum','svFloodDeleteItemOn');
        $this->renamePermission('forum','svFlood-ReactContainerOn', 'forum','svFloodReactContainerOn');
        $this->renamePermission('forum','svFlood-ReactItemOn', 'forum','svFloodReactItemOn');
        $this->renamePermission('forum','svFlood-PostContainerOn', 'forum','svFloodPostContainerOn');
        $this->renamePermission('forum','svFlood-PostItemOn', 'forum','svFloodPostItemOn');
    }
}