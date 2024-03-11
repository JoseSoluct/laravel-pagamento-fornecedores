<?php

namespace RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi;

use RedeCauzzoMais\Pagamento\Contracts\Pagamento\Pagamento as PagamentoContract;
use RedeCauzzoMais\Pagamento\Pagamento\Banco\AbstractPagamento;
use Exception;
use Throwable;

class Ted extends AbstractPagamento implements PagamentoContract
{
    const TED_PAGAMENTO_IMPOSTOS_TRIBUTOS_TAXAS         = '00001';
    const TED_PAGAMENTO_CONCESSIONARIAS_SERVICO_PUBLICO = '00002';
    const TED_PAGAMENTOS_DIVIDENDOS                     = '00003';
    const TED_PAGAMENTO_SALARIOS                        = '00004';
    const TED_PAGAMENTO_FORNECEDORES                    = '00005';
    const TED_PAGAMENTO_HONORARIOS                      = '00006';
    const TED_PAGAMENTO_ALUGUEIS_TAXAS_CONDOMINIO       = '00007';
    const TED_PAGAMENTO_DUPLICATAS_TITULOS              = '00008';
    const TED_PAGAMENTO_MENSALIDADE_ESCOLAR             = '00009';
    const TED_CREDITO_EM_CONTA                          = '00010';
    const TED_PAGAMENTO_CORRETORAS                      = '00011';
    const TED_PENSAO_ALIMENTICIA                        = '00101';

    protected ?string $finalidade     = null;
    protected array   $finalidadesTED = [
        '00001',
        '00002',
        '00003',
        '00004',
        '00005',
        '00006',
        '00007',
        '00008',
        '00009',
        '00010',
        '00011',
        '00101',
    ];

    public function __construct( array $params = [] )
    {
        parent::__construct( $params );

        $this->addRequiredField( ['finalidade', 'banco', 'agencia', 'conta', 'contaDv'] );
    }


    public function getFinalidade(): ?string
    {
        return $this->finalidade;
    }

    /**
     * @throws Throwable
     */
    public function setFinalidade( $finalidade ): static
    {
        if ( !in_array( $finalidade, $this->getFinalidadesTED() ) ) {
            throw new Exception( 'Finalidade não encontrada' );
        }

        $this->finalidade = $finalidade;

        return $this;
    }

    public function getFinalidadesTED(): array
    {
        return $this->finalidadesTED;
    }

    public function setFinalidadesTED( array $finalidadesTED ): static
    {
        $this->finalidadesTED = $finalidadesTED;

        return $this;
    }

}
