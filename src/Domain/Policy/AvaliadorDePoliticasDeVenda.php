<?php

declare(strict_types=1);

namespace DomainCart\Domain\Policy;

use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\Entity\Produto;
use InvalidArgumentException;

final class AvaliadorDePoliticasDeVenda
{
    /** @var list<PoliticaDeVenda> */
    private array $politicas;

    public function __construct(PoliticaDeVenda ...$politicas)
    {
        if ($politicas === []) {
            throw new InvalidArgumentException(
                'É necessário informar ao menos uma política de venda.'
            );
        }

        $this->politicas = array_values($politicas);
    }

    public function permiteVenda(Cliente $cliente, Produto $produto): bool
    {
        foreach ($this->politicas as $politica) {
            if ($politica->permiteVenda($cliente, $produto) === false) {
                return false;
            }
        }

        return true;
    }
}
