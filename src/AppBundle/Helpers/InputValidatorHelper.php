<?php

namespace AppBundle\Helpers;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\SCClasses\SCJsonResponse;

use JMS\Serializer\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InputValidatorHelper
{
    /* Type of Data */
    const SC_INTEGER = 'integer';
    const SC_FLOAT   = 'float';
    const SC_STRING  = 'string';
    const SC_BOOL    = 'bool';

    /* Array with type of Data */
    public static $SC_TYPES = array(
        self::SC_INTEGER,
        self::SC_FLOAT,
        self::SC_STRING,
        self::SC_BOOL
    );

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $value Value to be checked
     * @param $type Type that be wanted
     * @param $name Name of parameter to be checked
     * @param bool $isRequired If htis parameter is required
     * @return float|int|null|string|bool
     */
    public function check($value, $type, $name, $isRequired = FALSE, $isNullable = TRUE)
    {
        $newValue = null;
        $type = strtolower($type);
        /* Check if $type is valid */
        if (!in_array($type, self::$SC_TYPES)) {
            throw new HttpException(400, "Type: $type is not defined!");
        }

        if ($isRequired && !is_bool($value)) {
            if ($value === '' || $value === 'NULL' || $value === 'null' || $value == null) {
                throw new HttpException(400, "Parameter $name is required!");
            }
        } elseif ($isNullable) {
            if ($value == null || $value === '' || $value === 'NULL' || $value === 'null') {
                return null;
            }
        }

        switch ($type) {
            case self::SC_INTEGER:
                $newValue = $this->isInt($value, $name);
                break;
            case self::SC_FLOAT:
                $newValue = $this->isFloat($value, $name);
                break;
            case self::SC_STRING:
                $newValue = $this->isString($value, $name);
                break;
            case self::SC_BOOL:
                $newValue = $this->isBool($value, $name);
                break;
        }

        return $newValue;
    }

    /**
     * Check if parameter is Float or acceptable Type of data
     * @param $value Valute to check
     * @param $name Name of parameter to check
     * @return float
     */
    private function isFloat($value, $name)
    {
        $value = preg_replace('/[^A-Za-z0-9\-.,]/', '', $value);
        if (is_numeric($value)) {
            if ($value !== 0 and $value !== '0') {
                if (!is_float((float)$value)) {
                    throw new HttpException(400, "Parameter $name is not Float!");
                }
            } else {
                return (float)0;
            }
            return (float)$value;
        } else if ($value == '') {
            return NULL;
        } else {
            throw new HttpException(400, "Parameter $name is not Numeric!");
        }
    }

    /**
     * Check if parameter is Integer or acceptable Type of data
     * @param $value Valute to check
     * @param $name Name of parameter to check
     * @return int
     */
    private function isInt($value, $name)
    {
        $value = preg_replace('/[^A-Za-z0-9\-.,]/', '', $value);
        if (is_numeric($value)) {
            if ($value !== 0 and $value !== '0') {
                if (!(int)$value) {
                    throw new HttpException(400, "Parameter $name is not Integer!");
                }
            } else {
                return 0;
            }
            return (int)$value;
        } else if ($value == '') {
            return NULL;
        } else {
            throw new HttpException(400, "Parameter $name is not Numeric!");
        }
    }

    /**
     * Check if parameter is String or acceptable Type of data
     * @param $value Valute to check
     * @param $name Name of parameter to check
     * @return string
     */
    private function isString($value, $name)
    {
        if (is_string($value)) {
            $value = trim($value);
        } else {
            throw new HttpException(400, "Parameter $name is not String!");
        }
        return (string)$value;
    }

    private function isBool($value, $name)
    {
        if (is_bool($value)) {
            $value = boolval($value);
        } else {
            throw new HttpException(400, "Parameter $name is not boolean!");
        }
        return (bool)$value;
    }

}
