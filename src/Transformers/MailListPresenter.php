<?php

namespace Corals\Modules\Newsletter\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class MailListPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return MailListTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new MailListTransformer($extras);
    }
}
