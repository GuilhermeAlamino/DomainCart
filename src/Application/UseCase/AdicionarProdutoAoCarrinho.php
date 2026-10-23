<?php

declare(strict_types=1);

namespace DomainCart\Application\UseCase;

use DomainCart\Domain\Entity\Carrinho;
use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\Entity\Item;
use DomainCart\Domain\Policy\AvaliadorDePoliticasDeVenda;
use DomainException;

final class AdicionarProdutoAoCarrinho
{
    public function __construct(
        private AvaliadorDePoliticasDeVenda $avaliadorDePoliticas
    ) {
    }

    public function executar(
        Cliente $cliente,
        Carrinho $carrinho,
        Item $item
    ): void {
        if ($this->avaliadorDePoliticas->permiteVenda($cliente, $item->produto()) === false) {
            throw new DomainException(
                'O produto não atende às políticas de venda.'
            );
        }

        $carrinho->adicionarNoCarrinho($item);
    }
}
