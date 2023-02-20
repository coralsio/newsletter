<?php

namespace Corals\Modules\Newsletter\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Newsletter\Models\EmailLogger;
use Corals\User\Models\User;

class EmailLoggerPolicy extends BasePolicy
{
    protected $skippedAbilities = [
        'sendEmail' , 'create',
    ];

    /**
     * @param User $user
     * @param EmailLogger $emailLogger
     * @return bool
     */
    public function sendEmail(User $user, EmailLogger $emailLogger)
    {
        return in_array($emailLogger->status, ['failed', 'draft']);
    }
}
