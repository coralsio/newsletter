<?php

namespace Corals\Modules\Newsletter\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class SubscriberPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return SubscriberTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new SubscriberTransformer($extras);
    }
}
