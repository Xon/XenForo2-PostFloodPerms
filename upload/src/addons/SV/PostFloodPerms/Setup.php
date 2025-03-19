<?php

namespace SV\PostFloodPerms;

use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Entity\User as UserEntity;

class Setup extends AbstractSetup
{
    use InstallerHelper;
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1(): void
    {
        $this->applyGlobalPermissionIntForGroup('forum', 'svFloodReactGeneral', 1, UserEntity::GROUP_REG);
        $this->applyGlobalPermissionIntForGroup('conversation', 'svFloodReactGeneral', 1, UserEntity::GROUP_REG);
        $this->applyGlobalPermissionIntForGroup('profilePost', 'svFloodReactGeneral', 1, UserEntity::GROUP_REG);
    }

    public function upgrade2020000Step1(): void
    {
        $this->renamePermission('forum', 'sv_deleteflood_general', 'forum', 'svFloodDeleteGeneral');
        $this->renamePermission('forum', 'sv_deleteflood_node', 'forum', 'svFloodDeleteContainer');
        $this->renamePermission('forum', 'sv_deleteflood_node_on', 'forum', 'svFloodDeleteContainerOn');
        $this->renamePermission('forum', 'sv_deleteflood_thread', 'forum', 'svFloodDeleteItem');
        $this->renamePermission('forum', 'sv_deleteflood_thread_on', 'forum', 'svFloodDeleteItemOn');

        $this->renamePermission('forum', 'sv_likeflood_general', 'forum', 'svFloodReactGeneral');
        $this->renamePermission('forum', 'sv_likeflood_node', 'forum', 'svFloodReactContainer');
        $this->renamePermission('forum', 'sv_likeflood_node_on', 'forum', 'svFloodReactContainerOn');
        $this->renamePermission('forum', 'sv_likeflood_thread', 'forum', 'svFloodReactItem');
        $this->renamePermission('forum', 'sv_likeflood_thread_on', 'forum', 'svFloodReactItemOn');

        $this->renamePermission('forum', 'sv_postflood_general', 'forum', 'svFloodPostGeneral');
        $this->renamePermission('forum', 'sv_postflood_node', 'forum', 'svFloodPostContainer');
        $this->renamePermission('forum', 'sv_postflood_node_on', 'forum', 'svFloodPostContainerOn');
        $this->renamePermission('forum', 'sv_postflood_thread', 'forum', 'svFloodPostItem');
        $this->renamePermission('forum', 'sv_postflood_thread_on', 'forum', 'svFloodPostItemOn');
    }

    public function upgrade2020000Step2(): void
    {
        $this->applyGlobalPermissionIntForGroup('conversation', 'svFloodReactGeneral', 1, UserEntity::GROUP_REG);
    }

    public function upgrade1725242271Step1(): void
    {
        $this->applyGlobalPermissionIntForGroup('profilePost', 'svFloodReactGeneral', 1, UserEntity::GROUP_REG);
    }
}