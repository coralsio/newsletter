<?php

namespace Corals\Modules\Newsletter\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Newsletter\Models\MailList;
use Corals\User\Models\User;

class MailListPolicy extends BasePolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('Newsletter::mail_list.view')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Newsletter::mail_list.create');
    }

    /**
     * @param User $user
     * @param MailList $subscriber
     * @return bool
     */
    public function update(User $user, MailList $subscriber)
    {
        if ($user->can('Newsletter::mail_list.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param MailList $subscriber
     * @return bool
     */
    public function destroy(User $user, MailList $subscriber)
    {
        if ($user->can('Newsletter::mail_list.delete')) {
            return true;
        }
        return false;
    }

}
