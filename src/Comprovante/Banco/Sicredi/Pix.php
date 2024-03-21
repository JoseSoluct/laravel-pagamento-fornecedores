<?php

namespace RedeCauzzoMais\Pagamento\Comprovante\Banco\Sicredi;

use RedeCauzzoMais\Pagamento\Comprovante\AbstractComprovante;
use RedeCauzzoMais\Pagamento\Contracts\Comprovante\Comprovante as ComprovanteContract;
use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Detalhe as DetalheContract;
use RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix as PagamentoPix;
use RedeCauzzoMais\Pagamento\Util;
use Carbon\Carbon;

class Pix extends AbstractComprovante implements ComprovanteContract
{
    public static function make( DetalheContract $d, array $extra = [] ): string
    {
        static::isValid( $d );

        $d->getContaPagador()->setAgencia( str_pad( $d->getContaPagador()->getAgencia(), 4, 0, STR_PAD_LEFT ) );
        $d->getContaFavorecido()->setAgencia( str_pad( (int) $d->getContaFavorecido()
                                                               ->getAgencia(), 4, 0, STR_PAD_LEFT ) );
        $d->getContaFavorecido()->setConta( (int) $d->getContaFavorecido()->getConta() );

        $docLength = strlen( Util::onlyNumbers( $d->getFavorecido()->getDocumento() ) );

        $tipoPix = [
            PagamentoPix::CHAVE_TELEFONE        => 'Telefone',
            PagamentoPix::CHAVE_EMAIL           => 'E-mail',
            PagamentoPix::CHAVE_CPF             => 'CPF/CNPJ',
            PagamentoPix::CHAVE_ALEATORIA       => 'Chave aleatória',
            PagamentoPix::CHAVE_DADOS_BANCARIOS => 'Dados bancários',
        ];

        $data[] = ['Nome do Pagador:', $d->getPagador()->getNome()];
        $data[] = ['CPF/CNPJ do Pagador:', $d->getPagador()->getDocumento()];
        $data[] = [
            'Instituição do Pagador:',
            ( $b = $d->getContaPagador()->getBanco() ) . ' - ' . Util::bancoNome( $b )
        ];
        $data[] = ['Cooperativa do Pagador:', $d->getContaPagador()->getAgencia()];
        $data[] = ['Conta do Pagador:', $d->getContaPagador()->getConta() . '-' . $d->getContaPagador()->getContaDv()];
        $data[] = ['Nome Destinatário:', $d->getFavorecido()->getNome()];
        $data[] = ['Tipo de Chave:', $tipoPix[$d->getPixTipo()]];

        if ( $d->getPixTipo() == PagamentoPix::CHAVE_CPF and $docLength == 11 ) {
            $data[] = ['CPF do Destinatário:', $d->getFavorecido()->getDocumento()];
        } elseif ( $d->getPixTipo() == PagamentoPix::CHAVE_CNPJ ) {
            $data[] = ['CNPJ do Destinatário:', $d->getFavorecido()->getDocumento()];
        } elseif ( $d->getPixTipo() == PagamentoPix::CHAVE_DADOS_BANCARIOS ) {
            $data[] = [
                'Instituição do Destinatário',
                $d->getContaFavorecido()->getBanco() . ' - ' . Util::bancoNome( $d->getContaFavorecido()->getBanco() )
            ];
            $data[] = ['Agência do Destinatário', $d->getContaFavorecido()->getAgencia()];
            $data[] = [
                'Conta do Destinatário',
                $d->getContaFavorecido()->getConta() . '-' . $d->getContaFavorecido()->getContaDv()
            ];
        } else {
            $data[] = [$tipoPix[$d->getPixTipo()] . ' do Destinatário:', $d->getPixChave()];
        }

        $data[] = ['Data do Pagamento:', $d->getDataPagamento()];
        $data[] = ['Valor Total (R$):', number_format( $d->getValorPagamento(), 2, ',', '.' )];
        $data[] = ['Observação:', $extra['motivo'] ?? ''];
        $data[] = ['Autenticação Eletrônica:', $d->getAutenticacao()];
        $data[] = ['Código da Empresa:', $extra['codigo_empresa'] ?? ''];
        $data[] = ['Número Sequencial do Arquivo:', $extra['sequencial_arquivo'] ?? ''];

        $html[] = Logo::draw();
        $html[] = "<p><strong>Associado:</strong> {$d->getPagador()->getNome()}<br>";
        $html[] = "<strong>Cooperativa:</strong> {$d->getContaPagador()->getAgencia()}";
        $html[] = "<span style='padding-right: 90px'></span>";
        $html[] = "<strong>Conta Corrente:</strong> {$d->getContaPagador()->getConta()}-{$d->getContaPagador()->getContaDv()}";
        $html[] = "<span style='padding-right: 90px'></span>";
        $html[] = "<strong>Salvo em:</strong> " . Carbon::now()->format( 'd/m/Y H:i:s' ) . "</p>";

        $html[] = '<div style="border: 1px solid black; padding: 0 1rem;">';
        $html[] = "<h4 style=\"margin-bottom: 5px;\">COMPROVANTE DE PAGAMENTO PIX</h4>";
        $html[] = '<table style="font-size: 14px">';

        foreach ( $data as $row ) {
            $html[] = "<tr>";
            $html[] = "<td style=\"text-align: right; padding-right: 5px; white-space: nowrap;\">{$row[0]}</td>";
            $html[] = "<td style=\"white-space: nowrap;\">{$row[1]}</td>";
            $html[] = "</tr>";
        }

        $html[] = '</table>';
        $html[] = '</div>';

        $html[] = '<p style="font-size: 12px;">';
        $html[] = '* A transação acima foi realizada por Arquivo de Remessa conforme as condições especificadas no comprovante.<br>';
        $html[] = '* Os dados informados no arquivo de remessa são de responsabilidade do usuário.';
        $html[] = '</p>';

        $html[] = '<p style="font-size: 12px; text-align: center;">Serviços por telefone 3003 4770 (Capitais e Regiões Metropolitanas)<br>';
        $html[] = '0800 724 4770 (Demais Regiões)<br>';
        $html[] = 'SAC 0800 724 7220<br>';
        $html[] = 'Ouvidoria 0800 646 2519<br>';
        $html[] = 'Atendimento aos deficientes auditivos ou de fala 0800 724 0525</p>';
        $html[] = '</p>';

        return static::makePdf( implode( '', $html ) );
    }
}
