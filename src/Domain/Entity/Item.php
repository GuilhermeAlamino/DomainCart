<?php

declare(strict_types=1);

namespace DomainCart\Domain\Entity;

use InvalidArgumentException;

final class Item
{
    private string $id;
    private Produto $produto;
    private int $quantidade;
    private int $valorUnitario;

    public function __construct(
        string $id,
        Produto $produto,
        int $quantidade,
        int $valorUnitario
    ) {
        if (trim($id) === '') {
            throw new InvalidArgumentException(
                'O identificador do item é obrigatório.'
            );
        }

        if ($quantidade <= 0) {
            throw new InvalidArgumentException(
                'A quantidade deve ser maior que zero.'
            );
        }

        if ($valorUnitario <= 0) {
            throw new InvalidArgumentException(
                'O valor unitário deve ser maior que zero.'
            );
        }

        $this->id = trim($id);
        $this->produto = $produto;
        $this->quantidade = $quantidade;
        $this->valorUnitario = $valorUnitario;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function produto(): Produto
    {
        return $this->produto;
    }

    public function quantidade(): int
    {
        return $this->quantidade;
    }

    public function valorUnitario(): int
    {
        return $this->valorUnitario;
    }

    public function valorTotal(): int
    {
        return $this->quantidade * $this->valorUnitario;
    }

    public function adicionarQuantidade(int $quantidade): int
    {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException(
                'A quantidade adicionada deve ser maior que zero.'
            );
        }

        $this->quantidade += $quantidade;

        return $this->quantidade;
    }

    public function removerQuantidade(int $quantidade): int
    {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException(
                'A quantidade removida deve ser maior que zero.'
            );
        }

        if ($quantidade > $this->quantidade) {
            throw new InvalidArgumentException(
                'A quantidade removida não pode superar a quantidade do item.'
            );
        }

        $this->quantidade -= $quantidade;

        return $this->quantidade;
    }
}
