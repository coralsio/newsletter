<?php


namespace Corals\Modules\Newsletter\Excel;


use Maatwebsite\Excel\Concerns\FromArray;

class SubscribersErrorExport implements FromArray
{
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function array(): array
    {
        if (empty($this->errors)) {
            return [];
        }

        array_unshift($this->errors, array_keys($this->errors[0]));

        return $this->errors;
    }
}
