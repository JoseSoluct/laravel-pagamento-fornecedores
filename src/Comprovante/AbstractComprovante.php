<?php

namespace RedeCauzzoMais\Pagamento\Comprovante;

use RedeCauzzoMais\Pagamento\Contracts\Comprovante\Comprovante as ComprovanteContract;
use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Detalhe as DetalheContract;
use Knp\Snappy\Pdf;
use InvalidArgumentException;
use Exception;
use Throwable;

abstract class AbstractComprovante implements ComprovanteContract
{
    protected static string $binary = '/usr/local/bin/wkhtmltopdf';

    abstract public static function make( DetalheContract $d, array $extra = [] ): string;

    /**
     * @throws Throwable
     */
    public function saveAs( string $path, string $filename, DetalheContract $detalhe ): bool
    {
        if ( is_writable( $path ) === false ) {
            throw new Exception( 'Diretório não encontrado' );
        }

        $ext = explode( '.', $filename );
        if ( end( $ext ) <> 'pdf' ) {
            $filename = $filename . '.pdf';
        }

        return file_put_contents( $path . $filename, static::make( $detalhe ) ) !== false;
    }

    protected static function makePdf( string|array $html, $encoding = 'UTF-8' ): string
    {
        $pdf = new Pdf( static::$binary );

        if ( $encoding !== false ) {
            $pdf->setOption( 'encoding', $encoding );
        }

        $pdf->setOption( 'page-size', 'A4' );

        return $pdf->getOutputFromHtml( $html );
    }

    public static function setBinary( $binary ): void
    {
        static::$binary = $binary;
    }

    protected static function isValid( DetalheContract $detalhe ): void
    {
        if ( empty( $detalhe->getAutenticacao() ) ) {
            throw new InvalidArgumentException( 'Autenticação inválida' );
        }
    }
}
