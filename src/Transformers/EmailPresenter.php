<?php

namespace Corals\Modules\Newsletter\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class EmailPresenter extends FractalPresenter
{
    /**
     * @param array $extras
     * @return EmailTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new EmailTransformer($extras);
    }
}
