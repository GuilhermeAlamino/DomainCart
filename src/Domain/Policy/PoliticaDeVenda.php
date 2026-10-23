<?php

declare(strict_types=1);

namespace DomainCart\Domain\Policy;

use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\Entity\Produto;

interface PoliticaDeVenda
{
    public function permiteVenda(Cliente $cliente, Produto $produto): bool;
}
