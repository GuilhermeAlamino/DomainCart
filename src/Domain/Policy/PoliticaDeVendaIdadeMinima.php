<?php

declare(strict_types=1);

namespace DomainCart\Domain\Policy;

use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\Entity\Produto;

final class PoliticaDeVendaIdadeMinima implements PoliticaDeVenda
{
    public function permiteVenda(Cliente $cliente, Produto $produto): bool
    {
        return $cliente->calcularIdade() >= $produto->idadeMinima();
    }
}
