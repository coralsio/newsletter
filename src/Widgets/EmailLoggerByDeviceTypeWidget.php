<?php

namespace Corals\Modules\Newsletter\Widgets;

use Corals\Modules\Newsletter\Charts\EmailLoggerByDeviceType;

class EmailLoggerByDeviceTypeWidget
{

    function __construct()
    {
    }

    function run($args)
    {
        return rescue(function () use ($args) {
            $email = $args['email'] ?? null;

            if (is_null($email)) {
                return '';
            }

            $data = $email->emailLoggers()->whereNotNull('device_type')->groupBy('device_type');


            $chart = new EmailLoggerByDeviceType();
            $chart->labels(array_keys($data));
            $chart->dataset(trans('Newsletter::labels.widgets.email_logger_by_device'), 'pie', array_values($data));

            $chart->options([
                'plugins' => '{
                    colorschemes: {
                        scheme: \'brewer.Paired12\'
                    }
                }'
            ]);


            return view('Corals::chart')->with(['chart' => $chart])->render();

        });
    }
}
