<?php

declare(strict_types=1);

namespace DomainCart\Domain\Entity;

use DomainCart\Domain\ValueObject\DataDeNascimento;
use DomainCart\Domain\ValueObject\Email;
use InvalidArgumentException;

final class Cliente
{
    private string $nome;
    private Email $email;
    private DataDeNascimento $dataDeNascimento;
    private bool $intoleranciaLactose;

    public function __construct(
        string $nome,
        Email $email,
        DataDeNascimento $dataDeNascimento,
        bool $intoleranciaLactose
    ) {
        $removendoEspacosEmBranco = trim($nome);

        if ($removendoEspacosEmBranco === '') {
            throw new InvalidArgumentException(
                'O nome do cliente é obrigatório.'
            );
        }

        $this->nome = $removendoEspacosEmBranco;

        if (mb_strlen($this->nome) < 3) {
            throw new InvalidArgumentException(
                'O nome do cliente deve conter no mínimo 3 caracteres.'
            );
        }

        $this->email = $email;
        $this->dataDeNascimento = $dataDeNascimento;
        $this->intoleranciaLactose = $intoleranciaLactose;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function calcularIdade(): int
    {
        return $this->dataDeNascimento->calcularIdade();
    }

    public function intoleranciaLactose(): bool
    {
        return $this->intoleranciaLactose;
    }
}
