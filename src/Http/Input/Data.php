<?php


namespace Two\Http\Input;


use Respect\Validation\Exceptions\NestedValidationException;

class Data
{
    public $ok;
    public $data;

    /** @var NestedValidationException */
    private $exception;

    public function getErrors()
    {
        return $this->ok ? [] : $this->exception->getMessages();
    }

    /**
     * Adds errors to session flash and redirects to given $url, or the current page if left empty.
     *
     * @param string $url
     */
    public function redirectIfFailed(string $url = '')
    {
        if ( !$this->ok ) {

        }
    }

    public static function error(NestedValidationException $exception)
    {
        $d = new Data();
        $d->ok = false;
        $d->exception = $exception;
        return $d;
    }

    public static function success(array $data)
    {
        $d = new Data();
        $d->ok = true;
        $d->data = $data;
        return $d;
    }
}