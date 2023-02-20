<?php

namespace Corals\Modules\Newsletter\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Newsletter\Models\Email;
use Corals\Modules\Newsletter\Models\MailList;
use Corals\Modules\Newsletter\Models\Subscriber;

class EmailTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('newsletter.models.email.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param Email $email
     * @return array
     * @throws \Throwable
     */
    public function transform(Email $email)
    {
        $transformedArray = [
            'id' => $email->id,
            'subject' => '<a href="' . $email->getShowURL() . '">' . \Str::limit($email->subject, 50) . '</a>',
            'email_body' => $email->email_body,
            'status' => formatStatusAsLabels($email->status, [
                'text' => trans('Newsletter::attributes.email.status_options.' . $email->status),
                'level' => config('newsletter.models.email.status_level.' . $email->status),
            ]),
            'mail_lists' => formatArrayAsLabels(MailList::query()->whereIn('id', $email->mail_lists ?? [])->pluck('name')->toArray() ?: [], 'success', '<i class="fa fa-folder-open"></i>') ?: '-',
            'subscribers' => formatArrayAsLabels(Subscriber::query()->whereIn('id', $email->subscribers ?? [])->pluck('name')->toArray() ?: [], 'success', '<i class="fa fa-folder-open"></i>') ?: '-',
            'created_at' => format_date($email->created_at),
            'updated_at' => format_date($email->updated_at),
            'action' => $this->actions($email),
        ];

        return parent::transformResponse($transformedArray);
    }
}
