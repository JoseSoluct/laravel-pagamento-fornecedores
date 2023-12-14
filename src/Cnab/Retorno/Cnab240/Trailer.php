<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240;

use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240\Trailer as TrailerContract;
use RedeCauzzoMais\Pagamento\Traits\MagicTrait;

class Trailer implements TrailerContract
{
    use MagicTrait;

    protected int $numeroLote;
    protected int $tipoRegistro;
    protected int $qtdLotesArquivo;
    protected int $qtdRegistroArquivo;

    public function getTipoRegistro(): ?int
    {
        return $this->tipoRegistro;
    }

    public function setNumeroLote( int $numeroLote ): static
    {
        $this->numeroLote = $numeroLote;

        return $this;
    }

    public function getNumeroLote(): ?int
    {
        return $this->numeroLote;
    }

    public function setQtdLotesArquivo( int $qtdLotesArquivo ): static
    {
        $this->qtdLotesArquivo = $qtdLotesArquivo;

        return $this;
    }

    public function getQtdLotesArquivo(): ?int
    {
        return $this->qtdLotesArquivo;
    }

    public function setQtdRegistroArquivo( int $qtdRegistroArquivo ): static
    {
        $this->qtdRegistroArquivo = $qtdRegistroArquivo;

        return $this;
    }

    public function getQtdRegistroArquivo(): ?int
    {
        return $this->qtdRegistroArquivo;
    }

    public function setTipoRegistro( int $tipoRegistro ): static
    {
        $this->tipoRegistro = $tipoRegistro;

        return $this;
    }
}
