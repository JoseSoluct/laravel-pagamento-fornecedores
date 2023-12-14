<?php

if ( !function_exists( 'array_get' ) ) {
    function array_get( array $array, ?string $key, mixed $default = null )
    {
        if ( is_null( $key ) ) {
            return $array;
        }

        if ( isset( $array[$key] ) ) {
            return $array[$key];
        }

        foreach ( explode( '.', $key ) as $segment ) {
            if ( !is_array( $array ) || !array_key_exists( $segment, $array ) ) {
                return $default instanceof Closure ? $default() : $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }
}
