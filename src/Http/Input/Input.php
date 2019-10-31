<?php


namespace Two\Http\Input;


use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Rules\AllOf;
use Respect\Validation\Rules\KeyNested;
use Respect\Validation\Validatable;
use Respect\Validation\Validator;
use Symfony\Component\HttpFoundation\Request;
use Two\Util\Arrays;

class Input
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Input constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(array $filters)
    {
        /*
        $input = $this->post([
            'properties.yt-max-duration' => [Validator::intVal(), Validator::between(1, 3600)],
            'password' => [Validator::stringType(), Validator::length(6)],
            'str1/str2/str3' => Validator::stringType(),
            'optional?' => Validator::intVal()->between(1, 100),
        ]);
        if ( !$input->ok ) {

        }
        */

        return $this->filter($this->request->query->all(), $filters);
    }

    public function post(array $filters)
    {
        $data = array_merge($this->request->request->all(), $this->request->files->all());
        return $this->filter($data, $filters);
    }

    public function json(array $filters)
    {
        $json = $this->request->getContent();
        $data = json_decode($json, true);
        if ( !$data ) {
            return null;
        }
        return $this->filter($data, $filters);
    }

    public function filter(array $data, array $filters)
    {
        // this only contains data that has keys inside of filters.
        $filteredData = [];

        $validators = [];
        foreach ( $filters as $key => $spec ) {
            $spec = array_map([$this, 'handleSpec'], $spec);

            $names = explode('/', $key);
            foreach ( $names as $name ) {
                $required = true;
                if ( strpos($name, '?') !== false ) {
                    $required = false;
                    $name = str_replace('?', '', $name);
                }

                if ( strpos($name, '.') === false ) {
                    $filteredData[$name] = $data[$name] ?? null;
                } else {
                    Arrays::deepSet($filteredData, $name, Arrays::deepGet($data, $name));
                }
                $validators[] = new KeyNested($name, is_array($spec) ? Validator::allOf(...$spec) : $spec, $required);
            }
        }

        $all = new AllOf(...$validators);
        try {
            $all->assert($filteredData);
        } catch ( NestedValidationException $e ) {
            return Data::error($e);
        }

        return Data::success($filteredData);
    }

    private function handleSpec($specItem)
    {
        if ( $specItem instanceof Validatable ) {
            return $specItem;
        }

        // handle old-style RPG::input() rules
        switch ( $specItem ) {
            case 'int':
                return Validator::intVal();
            case 'uint':
                return Validator::intVal()->min(0);
            case 'num':
                return Validator::floatVal();
            case 'unum':
                return Validator::floatVal()->min(0);
            case 'string':
                return Validator::stringType();
            default:
                throw new ValidationException('Invalid validator, must be Validatable or int/uint/num/unum/string');
        }
    }
}