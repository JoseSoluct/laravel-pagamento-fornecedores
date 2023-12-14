<?php

namespace RedeCauzzoMais\Pagamento\Traits;

trait MagicTrait
{
    public function __set( $name, $value )
    {
        if ( property_exists( $this, $name ) ) {
            $this->$name = $value;
        }
    }

    public function __get( $name )
    {
        if ( property_exists( $this, $name ) ) {
            $method = 'get' . ucwords( $name );

            return $this->{$method}();
        }

        return null;
    }

    public function __isset( $key )
    {
        return isset( $this->$key );
    }

    public function toArray(): array
    {
        $vars = array_keys( get_class_vars( self::class ) );
        $aRet = [];
        foreach ( $vars as $var ) {
            $methodName = 'get' . ucfirst( $var );
            $aRet[$var] = method_exists( $this, $methodName ) ? $this->$methodName() : $this->$var;

            if ( is_object( $aRet[$var] ) and method_exists( $aRet[$var], 'toArray' ) ) {
                $aRet[$var] = $aRet[$var]->toArray();
            }
        }

        return $aRet;
    }
}
