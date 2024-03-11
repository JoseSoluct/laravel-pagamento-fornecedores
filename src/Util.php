<?php

namespace RedeCauzzoMais\Pagamento;

use RedeCauzzoMais\Pagamento\Contracts\Conta as ContaContract;
use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;
use Illuminate\Support\Str;
use NumberFormatter;
use Exception;
use Throwable;

final class Util
{
    public static array $bancos = [
        '001' => 'Banco do Brasil S.A.',
        '003' => 'Banco da Amazônia S.A.',
        '004' => 'Banco do Nordeste do Brasil S.A.',
        '007' => 'Banco Nacional de Desenvolvimento Econômico e Social – BNDES',
        '033' => 'Banco Santander (Brasil) S.A.',
        '036' => 'Banco Bradesco BBI S.A.',
        '041' => 'Banco do Estado do Rio Grande do Sul S.A.',
        '070' => 'BRB – Banco de Brasília S.A.',
        '104' => 'Caixa Econômica Federal',
        '136' => 'Confederação Nacional das Cooperativas Centrais UNICRED LTDA. – UNICRED DO BRASIL',
        '237' => 'Banco Bradesco S.A.',
        '212' => 'Banco Original S.A.',
        '260' => 'Nu Pagamentos S.A. – Nubank',
        '341' => 'Itaú Unibanco S.A.',
        '356' => 'Nu Pagamentos S.A. (Nubank)',
        '389' => 'Banco Mercantil do Brasil S.A.',
        '422' => 'Banco Safra S.A.',
        '633' => 'Banco Rendimento S.A.',
        '652' => 'Itaú Unibanco Holding S.A.',
        '745' => 'Banco Citibank S.A.',
        '748' => 'Banco Cooperativo Sicredi S.A.',
        '756' => 'Banco Cooperativo do Brasil S.A. – BANCOOB',
        '077' => 'Banco Inter S.A.',
        '246' => 'Banco ABC Brasil S.A.',
        '336' => 'Banco C6 S.A.',
        '102' => 'XP INVESTIMENTOS CCTVM S.A.',
        'XXX' => 'Desconhecido',
    ];

    public static function bancoNome( $codigo ): string
    {
        return self::$bancos[$codigo] ?? self::$bancos['XXX'];
    }

    public static function onlyNumbers( $string ): array|string|null
    {
        return preg_replace( '/[^[:digit:]]/', '', $string ?? '' );
    }

    public static function onlyChars( $string ): array|string|null
    {
        return preg_replace( '/[^[:alnum:]]/', '', $string );
    }

    public static function toChar( $string )
    {
        if ( empty( $string ) ) {
            return $string;
        }

        return preg_replace( '/[`^~\'"]/', '', iconv( 'UTF-8', 'ASCII//TRANSLIT', $string ) );
    }

    public static function toFloat( $number, int|false $decimals = 2, bool $showThousands = false ): string
    {
        if ( is_null( $number ) or empty( self::onlyNumbers( $number ) ) ) {
            return '';
        }

        $punctuation = preg_replace( '/[0-9]/', '', $number );
        $locale      = ( mb_substr( $punctuation, -1, 1 ) == ',' ) ? 'pt-BR' : 'en-US';
        $formater    = new NumberFormatter( $locale, NumberFormatter::DECIMAL );

        if ( $decimals === false ) {
            $decimals = 2;
            preg_match_all( '/[0-9][^0-9]([0-9]+)/', $number, $matches );
            if ( !empty( $matches[1] ) ) {
                $decimals = mb_strlen( rtrim( $matches[1][0], 0 ) );
            }
        }

        return number_format( $formater->parse( $number ), $decimals, '.', ( $showThousands ? ',' : '' ) );
    }

    public static function toMask( $str, $mask )
    {
        $str = str_replace( ' ', '', $str );

        for ( $i = 0; $i < strlen( $str ); $i++ ) {
            $mask[strpos( $mask, '#' )] = $str[$i];
        }

        return $mask;
    }

    /**
     * @throws \Exception
     */
    public static function formatCnab( $tipo, $valor, $tamanho, $dec = 0, $sFill = '' ): string
    {
        $tipo = Str::upper( $tipo );

        if ( in_array( $tipo, ['9', 9, 'N', '9L', 'NL'] ) ) {
            if ( $tipo == '9L' or $tipo == 'NL' ) {
                $valor = self::onlyNumbers( $valor );
            }
            $left  = '';
            $sFill = 0;
            $type  = 's';
            $valor = ( $dec > 0 ) ? sprintf( "%.{$dec}f", $valor ) : $valor;
            $valor = str_replace( [',', '.'], '', $valor ?? '' );
        } elseif ( in_array( $tipo, ['A', 'X', 'AC'] ) ) {
            $left  = '-';
            $type  = 's';
            $valor = self::toChar( $valor ) ?? '';

            if ( in_array( $tipo, ['A', 'X'] ) ) {
                $valor = Str::upper( $valor );
            }
        } else {
            throw new Exception( 'Tipo inválido' );
        }

        return sprintf( "%{$left}{$sFill}{$tamanho}{$type}", mb_substr( $valor, 0, $tamanho ) );
    }

    public static function modulo11( $n, $factor = 2, $base = 9, $x10 = 0, $resto10 = 0 )
    {
        $sum = 0;
        for ( $i = mb_strlen( $n ); $i > 0; $i-- ) {
            $sum += mb_substr( $n, $i - 1, 1 ) * $factor;
            if ( $factor == $base ) {
                $factor = 1;
            }
            $factor++;
        }

        if ( $x10 == 0 ) {
            $sum    *= 10;
            $digito = $sum % 11;
            if ( $digito == 10 ) {
                $digito = $resto10;
            }

            return $digito;
        }

        return $sum % 11;
    }

    public static function modulo10( $n ): int
    {
        $chars = array_reverse( str_split( $n, 1 ) );
        $odd   = array_intersect_key( $chars, array_fill_keys( range( 1, count( $chars ), 2 ), null ) );
        $even  = array_intersect_key( $chars, array_fill_keys( range( 0, count( $chars ), 2 ), null ) );
        $even  = array_map( function ( $n ) {
            return ( $n >= 5 ) ? 2 * $n - 9 : 2 * $n;
        }, $even );
        $total = array_sum( $odd ) + array_sum( $even );

        return ( ( floor( $total / 10 ) + 1 ) * 10 - $total ) % 10;
    }

    /**
     * @throws Throwable
     */
    public static function removeInPosition( $begin, $end, &$array ): string
    {
        if ( is_string( $array ) ) {
            $array = str_split( rtrim( $array, chr( 10 ) . chr( 13 ) . "\n" . "\r" ), 1 );
        }

        $begin--;

        if ( $begin > 398 or $end > 400 ) {
            throw new Exception( "{$begin} ou {$end} ultrapassam o limite máximo de 400" );
        }

        if ( $end < $begin ) {
            throw new Exception( "{$begin} é maior que o {$end}" );
        }

        $toSplice = $array;

        return trim( implode( '', array_splice( $toSplice, $begin, $end - $begin ) ) );
    }

    /**
     * @throws \Exception
     */
    public static function addInPosition( &$line, $begin, $end, $value ): array
    {
        $begin--;

        if ( $begin > 398 or $end > 400 ) {
            throw new Exception( "{$begin} ou {$end} ultrapassam o limite máximo de 400" );
        }

        if ( $end < $begin ) {
            throw new Exception( "{$begin} é maior que o {$end}" );
        }

        $length = $end - $begin;

        if ( mb_strlen( $value ) > $length ) {
            throw new Exception( sprintf( "String {$value} maior que o tamanho definido em {$begin} e {$end}: {$value}=%s e tamanho é de: %s", mb_strlen( $value ), $length ) );
        }

        $value = sprintf( "%{$length}s", $value );
        $value = preg_split( '//u', $value, -1, PREG_SPLIT_NO_EMPTY );

        return array_splice( $line, $begin, $length, $value );
    }

    public static function isCnab240( $content ): bool
    {
        $content = is_array( $content ) ? $content[0] : $content;

        return mb_strlen( rtrim( $content, "\r\n" ) ) == 240;
    }

    public static function isCnab400( $content ): bool
    {
        $content = is_array( $content ) ? $content[0] : $content;

        return mb_strlen( rtrim( $content, "\r\n" ) ) == 400;
    }

    public static function file2array( $file ): array|false
    {
        if ( is_array( $file ) and isset( $file[0] ) and is_string( $file[0] ) ) {
            return $file;
        }

        if ( is_string( $file ) and is_file( $file ) and file_exists( $file ) ) {
            return file( $file );
        }

        if ( is_string( $file ) and str_contains( $file, PHP_EOL ) ) {
            $fileContent = explode( PHP_EOL, $file );

            if ( empty( end( $fileContent ) ) ) {
                array_pop( $fileContent );
            }
            reset( $fileContent );

            return $fileContent;
        }

        return false;
    }

    public static function isHeaderRetorno( $header ): bool
    {
        if ( !self::isCnab240( $header ) and !self::isCnab400( $header ) ) {
            return false;
        }

        if ( self::isCnab400( $header ) and mb_substr( $header, 0, 9 ) <> '02RETORNO' ) {
            return false;
        }

        if ( self::isCnab240( $header ) and mb_substr( $header, 142, 1 ) <> '2' ) {
            return false;
        }

        return true;
    }

    public static function fillClass( $obj, array $params ): void
    {
        foreach ( $params as $param => $value ) {
            $param = str_replace( ' ', '', ucwords( str_replace( '_', ' ', $param ) ) );

            if ( method_exists( $obj, 'set' . ucwords( $param ) ) ) {
                $obj->{'set' . ucwords( $param )}( $value );
            }
        }
    }

    /**
     * @throws \Exception
     */
    public static function addPessoa( &$property, $obj ): PessoaContract
    {
        if ( is_subclass_of( $obj, PessoaContract::class ) ) {
            $property = $obj;

            return $obj;
        }

        if ( is_array( $obj ) ) {
            $obj      = new Pessoa( $obj );
            $property = $obj;

            return $obj;
        }

        throw new Exception( 'Objeto inválido, somente pessoa e Array' );
    }

    /**
     * @throws \Exception
     */
    public static function addConta( &$property, $obj ): ContaContract
    {
        if ( is_subclass_of( $obj, ContaContract::class ) ) {
            $property = $obj;

            return $obj;
        }

        if ( is_array( $obj ) ) {
            $obj      = new Conta( $obj );
            $property = $obj;

            return $obj;
        }

        throw new Exception( 'Objeto inválido, somente conta e Array' );
    }
}
