<?php
require 'autoload.php';

$empresa = new \RedeCauzzoMais\Pagamento\Pessoa( [
    'nome'      => 'ACME',
    'endereco'  => 'Rua UM',
    'numero'    => '123',
    'bairro'    => 'Bairro',
    'cep'       => '99999-999',
    'uf'        => 'UF',
    'cidade'    => 'Cidade',
    'documento' => '99.999.999/0001-99',
] );

$remessa = new \RedeCauzzoMais\Pagamento\Cnab\Remessa\Cnab240\Banco\Sicredi( [
    'agencia'       => 9999,
    'agenciaDv'     => 9,
    'carteira'      => '1',
    'conta'         => 99999,
    'contaDv'       => 9,
    'idremessa'     => 1,
    'beneficiario'  => $empresa,
    'codigoCliente' => '99AA'
] );

$favorecido = new \RedeCauzzoMais\Pagamento\Pessoa( [
    'nome'      => 'Favorecido',
    'endereco'  => 'Rua Um',
    'numero'    => '123',
    'bairro'    => 'Bairro',
    'cep'       => '00000-000',
    'uf'        => 'UF',
    'cidade'    => 'Cidade',
    'documento' => '999.999.999-99',
] );

$pagamentoTed = new \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Ted( [
    'data'            => new \Carbon\Carbon(),
    'finalidade'      => '00011',
    'valor'           => 10,
    'numeroDocumento' => 1,
    'banco'           => 237,
    'agencia'         => 9999,
    'conta'           => 999999,
    'contaDv'         => 9,
    'favorecido'      => $favorecido
] );

$pagamentoPixEmail = new \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix( [
    'data'            => new \Carbon\Carbon(),
    'valor'           => 9.99,
    'numeroDocumento' => 0,
    'pixTipo'         => Pix::CHAVE_PIX_EMAIL,
    'pixChave'        => 'brunob1990@gmail.com',
    'favorecido'      => new \RedeCauzzoMais\Pagamento\Pessoa( [] )
] );

$transfer = new \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Transfer( [
    'data'            => new \Carbon\Carbon(),
    'valor'           => 9.99,
    'numeroDocumento' => 0,
    'banco'           => 748,
    'agencia'         => 9999,
    'conta'           => 9999,
    'contaDv'         => 9,
    'favorecido'      => $favorecido,
] );

$pixDadosBancarios = new \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix( [
    'data'            => new \Carbon\Carbon(),
    'valor'           => 9.99,
    'numeroDocumento' => 0,
    'pixTipo'         => \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix::CHAVE_DADOS_BANCARIOS,
    'tipoConta'       => \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix::TIPO_CONTA_CORRENTE,
    'banco'           => 237,
    'agencia'         => 9999,
    'conta'           => 99999,
    'contaDv'         => 9,
    'favorecido'      => new \RedeCauzzoMais\Pagamento\Pessoa( [
        'nome'      => 'Fulano de Almeida',
        'documento' => '000.000.000-00',
    ] ),
] );

$pagamentoCPF = new \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix( [
    'data'            => new \Carbon\Carbon(),
    'valor'           => 9.99,
    'numeroDocumento' => 0,
    'pixTipo'         => \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix::CHAVE_CPF,
    'pixChave'        => '000.000.000-00',
    'favorecido'      => new \RedeCauzzoMais\Pagamento\Pessoa( [
        'nome'      => 'Fulano de Lovato',
        'documento' => '000.000.000-00',
    ] ),
] );


$remessa->addPagamento( $pagamentoTed );
$remessa->addPagamento( $pagamentoPixEmail );
$remessa->addPagamento( $pagamentoCPF );
$remessa->addPagamento( $pixDadosBancarios );
$remessa->addPagamento( $transfer );

$hoje = new \DateTime();

$nomeclatura = $remessa->getCodigoCliente() . $hoje->format( 'd' ) . '1' . '00.REM';

echo $remessa->save( __DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . $nomeclatura );
