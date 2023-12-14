## Agradecimentos

Projeto baseado no [murilosandiego/pagamento](https://github.com/murilosandiego/pagamento)
de [Murilo Sandiego](https://github.com/murilosandiego)

# Pagamento, Remessa e Retorno PHP

Pacote para gerar pagamentos via arquivos de remessas e leitura de
retorno.

## Requerimentos

- [PHP Extensão Intl](http://php.net/manual/pt_BR/book.intl.php)

## Bancos suportados

| Banco                 |         Homologado         |            PIX             |            TED             | Transferências entre contas |        Comprovante         |
|-----------------------|:--------------------------:|:--------------------------:|:--------------------------:|:----------------------------:|:--------------------------:|
| Sicredi - Manual V3.4 |     :white_check_mark:     |     :white_check_mark:     |     :white_check_mark:     |      :white_check_mark:      |     :white_check_mark:     |
| Bancoob (Sicoob)      | :eight_pointed_black_star: | :eight_pointed_black_star: | :eight_pointed_black_star: |  :eight_pointed_black_star:  | :eight_pointed_black_star: |

## Instalação

Via composer:

```
composer require rede-cauzzo-mais/pagamento:dev-master
```

Ou adicione manualmente ao seu composer.json:

```
"rede-cauzzo-mais/pagamento": "dev-master"
```

## Remessa

### Gerar Pagamento

#### Criando a empresa ou favorecido

```php
$empresa = new \RedeCauzzoMais\Pagamento\Pessoa(
    [
        'nome'        => 'ACME',
        'endereco'    => 'Rua UM',
        'numero'      => '123',
        'bairro'      => 'Bairro',
        'cep'         => '99999-999',
        'uf'          => 'UF',
        'cidade'      => 'Cidade',
        'documento'   => '99.999.999/0001-99',
    ]
);

$favorecido = new \RedeCauzzoMais\Pagamento\Pessoa(
    [
        'nome'      => 'Favorecido',
        'endereco'  => 'Rua Um',
        'numero'    => '123',
        'bairro'    => 'Bairro',
        'cep'       => '00000-000',
        'uf'        => 'UF',
        'cidade'    => 'Cidade',
        'documento' => '999.999.999-99',
    ]
);
```

#### Criando o pagamento

```php
$pagamento = new \RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix(
    [
        'data' => new \Carbon\Carbon(),
        'finalidade' => '00011',
        'valor' => 10,
        'numeroDocumento' => 1,
        'banco' => 237,
        'agencia' => 9999,
        'conta' => 999999,
        'contaDv' => 9,
        'favorecido' => $favorecido
    ]
);
```

### Gerar remessa

```php
$remessa = new \RedeCauzzoMais\Pagamento\Cnab\Remessa\Cnab240\Banco\Sicredi(
    [
        'agencia'      => 9999,
        'agenciaDv'    => 9,
        'carteira'     => '1',
        'conta'        => 99999,
        'contaDv'      => 9,
        'idremessa'    => 1,
        'beneficiario' => $empresa,
        'codigoCliente' => '99AA'
    ]
);

// Adicionar um pagamento
$remessa->addPagamento($pagamentoTed);

// Ou para adicionar um array de pagamentos
$pagamentos = [];
$pagamentos[] = $pagamento1;
$pagamentos[] = $pagamento2;
$pagamentos[] = $pagamento3;
$remessa->addPagamentos($pagamentos);

//Gerar remessa
echo $remessa->gerar();

//Salvar remessa
echo $remessa->save(__DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'sicredi_pagamento.txt');
```

## Retorno

### Tratar retorno

```php
$retorno = \RedeCauzzoMais\Pagamento\Cnab\Retorno\Factory::make('/caminho/para/arquivo.RET');
$retorno->processar();
echo $retorno->getBancoNome();

// Retorno implementa \SeekableIterator, sendo assim, podemos utilizar o foreach da seguinte forma:
foreach($retorno as $registro) {
	var_dump($registro->toArray());
}

// Ou também podemos:
$detalheCollection = $retorno->getDetalhes();
foreach($detalheCollection as $detalhe) {
	var_dump($detalhe->toArray());
}

// Ou até mesmo do jeito laravel
$detalheCollection->each(function ($detalhe, $index) {
    var_dump($detalhe->toArray())
});
```

**Métodos disponíveis:**

```php
$retorno->getDetalhes();

$retorno->getHeader();

$retorno->getTrailer();
```

## Licença

Este projeto está licenciado sob a licença MIT - consulte o arquivo [LICENSE.md](LICENSE.md) para obter detalhes
