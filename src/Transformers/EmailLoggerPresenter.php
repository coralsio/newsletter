<?php

namespace Corals\Modules\Newsletter\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class EmailLoggerPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return EmailLoggerTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new EmailLoggerTransformer($extras);
    }
}
