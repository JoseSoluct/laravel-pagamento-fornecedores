<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Remessa\Cnab240;

use RedeCauzzoMais\Pagamento\Cnab\Remessa\AbstractRemessa as AbstractRemessaGeneric;
use Exception;

abstract class AbstractRemessa extends AbstractRemessaGeneric
{
    protected false|int $lineSize = 240;

    protected array $records = [
        self::HEADER       => [],
        self::HEADER_LOTE  => [],
        self::DETALHE      => [],
        self::TRAILER_LOTE => [],
        self::TRAILER      => [],
    ];

    abstract protected function headerLote( $pay );

    abstract protected function trailerLote( $pay );

    protected function getHeaderLote()
    {
        return $this->records[self::HEADER_LOTE];
    }

    protected function getTrailerLote()
    {
        return $this->records[self::TRAILER_LOTE];
    }

    protected function startHeader(): void
    {
        $this->records[self::HEADER] = array_fill( 0, 240, ' ' );

        $this->current = &$this->records[self::HEADER];
    }

    protected function startHeaderLote(): void
    {
        $this->records[self::HEADER_LOTE] = array_fill( 0, 240, ' ' );

        $this->current = &$this->records[self::HEADER_LOTE];
    }

    protected function startTrailerLote(): void
    {
        $this->records[self::TRAILER_LOTE] = array_fill( 0, 240, ' ' );

        $this->current = &$this->records[self::TRAILER_LOTE];
    }

    protected function startTrailer(): void
    {
        $this->records[self::TRAILER] = array_fill( 0, 240, ' ' );

        $this->current = &$this->records[self::TRAILER];
    }

    protected function startDetalhe( $payment ): void
    {
        $this->countRecords++;
        $this->records[self::DETALHE][$payment][$this->countRecords] = array_fill( 0, 240, ' ' );

        $this->current = &$this->records[self::DETALHE][$payment][$this->countRecords];
    }

    public function gerar(): string
    {
        if ( !$this->isValid( $errors ) ) {
            throw new Exception( 'Campos requeridos pelo banco, aparentam estar ausentes: ' . implode( ', ', $errors ) );
        }

        $stringRemessa = '';
        if ( $this->countRecords < 1 ) {
            throw new Exception( 'Nenhuma linha detalhe foi adicionada' );
        }

        $this->header();
        $stringRemessa .= $this->valida( $this->getHeader() ) . $this->endLine;

        $this->headerLote( null );
        $stringRemessa .= $this->valida( $this->getHeaderLote() ) . $this->endLine;

        foreach ( $this->getDetalhes() as $detalhe ) {
            $stringRemessa .= $this->valida( $detalhe ) . $this->endLine;
        }

        $this->trailerLote( null );
        $stringRemessa .= $this->valida( $this->getTrailerLote() ) . $this->endLine;


        $this->trailer();
        $stringRemessa .= $this->valida( $this->getTrailer() ) . $this->endFile;

        return $stringRemessa;
    }
}
