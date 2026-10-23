<?php

declare(strict_types=1);

namespace DomainCart\Domain\Entity;

use InvalidArgumentException;

final class Produto
{
    private string $nome;
    private string $descricao;
    private int $quantidadeEmEstoque;
    private int $valor;
    private int $idadeMinima;
    private bool $contemLactose;

    public function __construct(
        string $nome,
        string $descricao,
        int $quantidadeEmEstoque,
        int $valor,
        int $idadeMinima,
        bool $contemLactose
    ) {
        $nomeNormalizado = trim($nome);

        if ($nomeNormalizado === '') {
            throw new InvalidArgumentException(
                'O nome é obrigatório.'
            );
        }

        $this->nome = $nomeNormalizado;

        if (mb_strlen($this->nome) < 3) {
            throw new InvalidArgumentException(
                'O nome deve conter pelo menos três caracteres.'
            );
        }

        $descricaoNormalizada = trim($descricao);

        if ($descricaoNormalizada === '') {
            throw new InvalidArgumentException(
                'A descrição é obrigatória.'
            );
        }

        $this->descricao = $descricaoNormalizada;

        if ($quantidadeEmEstoque < 0) {
            throw new InvalidArgumentException(
                'A quantidade em estoque não pode ser negativa.'
            );
        }

        $this->quantidadeEmEstoque = $quantidadeEmEstoque;

        if ($valor <= 0) {
            throw new InvalidArgumentException(
                'O valor deve ser maior que zero.'
            );
        }

        $this->valor = $valor;

        if ($idadeMinima < 0) {
            throw new InvalidArgumentException(
                'A idade mínima deve ser maior ou igual a zero.'
            );
        }

        $this->idadeMinima = $idadeMinima;

        $this->contemLactose = $contemLactose;
    }

    public function descricao(): string
    {
        return $this->descricao;
    }

    public function quantidadeEmEstoque(): int
    {
        return $this->quantidadeEmEstoque;
    }

    public function idadeMinima(): int
    {
        return $this->idadeMinima;
    }

    public function valor(): int
    {
        return $this->valor;
    }

    public function contemLactose(): bool
    {
        return $this->contemLactose;
    }
}
