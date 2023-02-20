<?php

namespace Corals\Modules\Newsletter\Widgets;

use Corals\Modules\Newsletter\Charts\EmailLoggerByStatus;

class EmailLoggerByStatusWidget
{
    public function __construct()
    {
    }

    public function run($args)
    {
        return rescue(function () use ($args) {
            $email = $args['email'] ?? null;

            if (is_null($email)) {
                return '';
            }


            $data = $email->emailLoggers()->whereNotNull('device_type')->groupBy('status');


            $chart = new EmailLoggerByStatus();
            $chart->labels(array_keys($data));
            $chart->dataset(trans('Newsletter::attributes.email_logger.status_options'), 'pie', array_values($data));

            $chart->options([
                'plugins' => '{
                    colorschemes: {
                        scheme: \'brewer.Paired12\'
                    }
                }',
            ]);


            return view('Corals::chart')->with(['chart' => $chart])->render();
        });
    }
}
