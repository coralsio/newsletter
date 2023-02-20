<?php

namespace Corals\Modules\Newsletter\Widgets;

use ConsoleTVs\Charts\Facades\Charts;
use Corals\Modules\Newsletter\Charts\EmailLoggerByBrowser;

class EmailLoggerByBrowserWidget
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


            $data = $email->emailLoggers()->whereNotNull('device_type')->groupBy('browser');


            $chart = new EmailLoggerByBrowser();
            $chart->labels(array_keys($data));
            $chart->dataset(trans('Newsletter::labels.widgets.email_logger_by_browser'), 'pie', array_values($data));

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
