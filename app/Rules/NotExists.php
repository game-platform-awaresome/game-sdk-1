<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class NotExists implements Rule
{
    private $exception;
    private $table;
    private $column;

    /**
     * Create a new rule instance.
     * @param $table
     * @param null $column
     * @param $exception ['column'=>'phone', 'value'=>'133265690000'] 该条记录则会被排除在notExist判断之外
     */
    public function __construct($table, $column = null, $exception = null)
    {
        $this->table     = $table;
        $this->column    = $column;
        $this->exception = $exception;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->column = $attribute;
        if (!is_null($this->exception)) {
            $exceptionWhere = [$this->exception['column'], '<>', $this->exception['value']];
        } else {
            $exceptionWhere = [];
        }
        return DB::table($this->table)->where($exceptionWhere)->where($this->column, $value)->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.no_exists');
    }
}
