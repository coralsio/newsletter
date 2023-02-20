<?php

namespace Corals\Modules\Newsletter\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class MailList extends BaseModel
{
    use PresentableTrait;
    use LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'newsletter.models.mail_list';

    protected $guarded = ['id'];

    protected $table = 'newsletter_mail_lists';

    public function subscribers()
    {
        return $this->belongsToMany(
            Subscriber::class,
            'newsletter_mail_list_subscriber',
            'list_id',
            'subscriber_id'
        );
    }
}
