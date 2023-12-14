<?php

namespace RedeCauzzoMais\Pagamento\Contracts;

interface Pessoa
{
    public function getNome(): ?string;

    public function getDocumento(): ?string;

    public function getBairro(): ?string;

    public function getEndereco(): ?string;

    public function getNumero(): ?string;

    public function getComplemento(): ?string;

    public function getCep(): ?string;

    public function getCidade(): ?string;

    public function getUf(): ?string;

    public function toArray(): array;
}
