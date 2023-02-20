<?php

namespace Corals\Modules\Newsletter\Excel;

use Corals\Modules\Newsletter\Models\MailList;
use Corals\Modules\Newsletter\Models\Subscriber;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;

class SubscribersImport implements ToArray
{
    public function __construct()
    {
    }

    /**
     * @param array $rows
     * @throws \Exception
     */
    public function array(array $rows)
    {
        $headers = $rows[0] ?? [];

        if (empty($headers)) {
            throw new \Exception('Invalid file structure');
        }

        unset($rows[0]);

        $wrongData = [];
        $wrongCounter = 0;
        $successCounter = 0;

        foreach ($rows as $index => $row) {
            $row = array_combine($headers, $row);

            $mailLists = $row['mail_lists'] ?? $row['mail lists'] ?? '';

            $mailLists = array_map(
                'trim',
                explode(config('newsletter.models.subscriber.import.delimiter'), $mailLists)
            );

            $mailListsObjects = MailList::query()->whereIn('name', $mailLists)->get();

            $validMailLists = $mailListsObjects->count() == count(array_filter($mailLists));

            $validator = Validator::make($row, [
                'name' => 'max:191',
                'email' => 'required|email|max:191|unique:newsletter_subscribers,email',
            ]);

            if ($validator->fails() || ! $validMailLists) {
                $errors = $validator->errors()->all();

                if (! $validMailLists) {
                    $errors = array_merge($errors, [trans('Newsletter::exception.subscribers.unknown_mail_list')]);
                }

                $row['errors'] = '[' . implode(", ", $errors) . ']';

                $wrongData[] = $row;

                $wrongCounter++;
            } else {
                unset($row['mail_lists']);
                unset($row['mail lists']);

                $ids = $mailListsObjects->pluck('id')->toArray();

                $subscriber = Subscriber::create($row);

                $subscriber->mailLists()->sync($ids);

                $successCounter++;
            }
        }

        if (count($wrongData) > 0) {
            $exportName = 'errors/subscribers_errors_' . now()->format('Y-m-d_h-m-s') . '.xlsx';

            Excel::store(new SubscribersErrorExport($wrongData), $exportName);

            session()->put('import-subscribers-report', storage_path('app/' . $exportName));
        }

        flash(trans(
            'Newsletter::messages.subscriber.success.import',
            ['successCount' => $successCounter, 'wrongCount' => $wrongCounter]
        ))->success();
    }
}
