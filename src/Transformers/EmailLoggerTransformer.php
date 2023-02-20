<?php

namespace Corals\Modules\Newsletter\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Newsletter\Models\EmailLogger;

class EmailLoggerTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('newsletter.models.email_logger.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param EmailLogger $emailLogger
     * @return array
     * @throws \Throwable
     */
    public function transform(EmailLogger $emailLogger)
    {



        $transformedArray = [
            'id' => $emailLogger->id,
            'subject' => '<a href="' . $emailLogger->getShowURL() . '">' . \Str::limit($emailLogger->email->subject, 50) . '</a>',
            'subscriber_name' => $emailLogger->subscriber->name ?? '-',
            'subscriber_email' => $emailLogger->subscriber->email,
            'status' => formatStatusAsLabels($emailLogger->status, [
                'text' => trans('Newsletter::attributes.email_logger.status_options.' . $emailLogger->status),
                'level' => config('newsletter.models.email_logger.status_level.' . $emailLogger->status)
            ]),
            'created_at' => format_date($emailLogger->email->created_at),
            'updated_at' => format_date($emailLogger->email->updated_at),
            'action' => $this->actions($emailLogger)
        ];

        return parent::transformResponse($transformedArray);
    }
}
