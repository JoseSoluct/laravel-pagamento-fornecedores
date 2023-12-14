<?php

namespace RedeCauzzoMais\Pagamento;

use Exception;
use Illuminate\Support\Str;
use NumberFormatter;
use RedeCauzzoMais\Pagamento\Contracts\Conta as ContaContract;
use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;

final class Util
{
    public static array $bancos = [
        '246' => 'Banco ABC Brasil S.A.',
        '025' => 'Banco Alfa S.A.',
        '641' => 'Banco Alvorada S.A.',
        '029' => 'Banco Banerj S.A.',
        '000' => 'Banco Bankpar S.A.',
        '740' => 'Banco Barclays S.A.',
        '107' => 'Banco BBM S.A.',
        '031' => 'Banco Beg S.A.',
        '739' => 'Banco BGN S.A.',
        '096' => 'Banco BM&F de Serviços de Liquidação e Custódia S.A',
        '318' => 'Banco BMG S.A.',
        '752' => 'Banco BNP Paribas Brasil S.A.',
        '248' => 'Banco Boavista Interatlântico S.A.',
        '218' => 'Banco Bonsucesso S.A.',
        '065' => 'Banco Bracce S.A.',
        '036' => 'Banco Bradesco BBI S.A.',
        '204' => 'Banco Bradesco Cartões S.A.',
        '394' => 'Banco Bradesco Financiamentos S.A.',
        '237' => 'Banco Bradesco S.A.',
        '225' => 'Banco Brascan S.A.',
        '208' => 'Banco BTG Pactual S.A.',
        '044' => 'Banco BVA S.A.',
        '263' => 'Banco Cacique S.A.',
        '473' => 'Banco Caixa Geral - Brasil S.A.',
        '040' => 'Banco Cargill S.A.',
        '233' => 'Banco Cifra S.A.',
        '745' => 'Banco Citibank S.A.',
        'M08' => 'Banco Citicard S.A.',
        'M19' => 'Banco CNH Capital S.A.',
        '215' => 'Banco Comercial e de Investimento Sudameris S.A.',
        '756' => 'Banco Cooperativo do Brasil S.A. - BANCOOB',
        '748' => 'Banco Cooperativo Sicredi S.A.',
        '222' => 'Banco Credit Agricole Brasil S.A.',
        '505' => 'Banco Credit Suisse (Brasil) S.A.',
        '229' => 'Banco Cruzeiro do Sul S.A.',
        '003' => 'Banco da Amazônia S.A.',
        '083' => 'Banco da China Brasil S.A.',
        '707' => 'Banco Daycoval S.A.',
        'M06' => 'Banco de Lage Landen Brasil S.A.',
        '024' => 'Banco de Pernambuco S.A. - BANDEPE',
        '456' => 'Banco de Tokyo-Mitsubishi UFJ Brasil S.A.',
        '214' => 'Banco Dibens S.A.',
        '001' => 'Banco do Brasil S.A.',
        '047' => 'Banco do Estado de Sergipe S.A.',
        '037' => 'Banco do Estado do Pará S.A.',
        '041' => 'Banco do Estado do Rio Grande do Sul S.A.',
        '004' => 'Banco do Nordeste do Brasil S.A.',
        '265' => 'Banco Fator S.A.',
        'M03' => 'Banco Fiat S.A.',
        '224' => 'Banco Fibra S.A.',
        '626' => 'Banco Ficsa S.A.',
        'M18' => 'Banco Ford S.A.',
        'M07' => 'Banco GMAC S.A.',
        '612' => 'Banco Guanabara S.A.',
        'M22' => 'Banco Honda S.A.',
        '063' => 'Banco Ibi S.A. Banco Múltiplo',
        'M11' => 'Banco IBM S.A.',
        '604' => 'Banco Industrial do Brasil S.A.',
        '320' => 'Banco Industrial e Comercial S.A.',
        '653' => 'Banco Indusval S.A.',
        '249' => 'Banco Investcred Unibanco S.A.',
        '184' => 'Banco Itaú BBA S.A.',
        '479' => 'Banco ItaúBank S.A',
        'M09' => 'Banco Itaucred Financiamentos S.A.',
        '376' => 'Banco J. P. Morgan S.A.',
        '074' => 'Banco J. Safra S.A.',
        '217' => 'Banco John Deere S.A.',
        '600' => 'Banco Luso Brasileiro S.A.',
        '389' => 'Banco Mercantil do Brasil S.A.',
        '746' => 'Banco Modal S.A.',
        '045' => 'Banco Opportunity S.A.',
        '079' => 'Banco Original do Agronegócio S.A.',
        '623' => 'Banco Panamericano S.A.',
        '611' => 'Banco Paulista S.A.',
        '643' => 'Banco Pine S.A.',
        '638' => 'Banco Prosper S.A.',
        '747' => 'Banco Rabobank International Brasil S.A.',
        '356' => 'Banco Real S.A.',
        '633' => 'Banco Rendimento S.A.',
        'M16' => 'Banco Rodobens S.A.',
        '072' => 'Banco Rural Mais S.A.',
        '453' => 'Banco Rural S.A.',
        '422' => 'Banco Safra S.A.',
        '033' => 'Banco Santander (Brasil) S.A.',
        '749' => 'Banco Simples S.A.',
        '366' => 'Banco Société Générale Brasil S.A.',
        '637' => 'Banco Sofisa S.A.',
        '012' => 'Banco Standard de Investimentos S.A.',
        '464' => 'Banco Sumitomo Mitsui Brasileiro S.A.',
        '082' => 'Banco Topázio S.A.',
        'M20' => 'Banco Toyota do Brasil S.A.',
        '634' => 'Banco Triângulo S.A.',
        'M14' => 'Banco Volkswagen S.A.',
        'M23' => 'Banco Volvo (Brasil) S.A.',
        '655' => 'Banco Votorantim S.A.',
        '610' => 'Banco VR S.A.',
        '119' => 'Banco Western Union do Brasil S.A.',
        '370' => 'Banco WestLB do Brasil S.A.',
        '021' => 'BANESTES S.A. Banco do Estado do Espírito Santo',
        '719' => 'Banif-Banco Internacional do Funchal (Brasil)S.A.',
        '755' => 'Bank of America Merrill Lynch Banco Múltiplo S.A.',
        '073' => 'BB Banco Popular do Brasil S.A.',
        '250' => 'BCV - Banco de Crédito e Varejo S.A.',
        '078' => 'BES Investimento do Brasil S.A.-Banco de Investimento',
        '069' => 'BPN Brasil Banco Múltiplo S.A.',
        '070' => 'BRB - Banco de Brasília S.A.',
        '104' => 'Caixa Econômica Federal',
        '477' => 'Citibank S.A.',
        '081' => 'Concórdia Banco S.A.',
        '487' => 'Deutsche Bank S.A. - Banco Alemão',
        '064' => 'Goldman Sachs do Brasil Banco Múltiplo S.A.',
        '062' => 'Hipercard Banco Múltiplo S.A.',
        '399' => 'HSBC Bank Brasil S.A.',
        '492' => 'ING Bank N.V.',
        '652' => 'Itaú Unibanco Holding S.A.',
        '341' => 'Itaú Unibanco S.A.',
        '488' => 'JPMorgan Chase Bank',
        '751' => 'Scotiabank Brasil S.A. Banco Múltiplo',
        '409' => 'UNIBANCO - União de Bancos Brasileiros S.A.',
        '230' => 'Unicard Banco Múltiplo S.A.',
        '260' => 'Nu Pagamentos S.A.',
        'XXX' => 'Desconhecido',
    ];

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
     * @throws Exception
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
