<?php

declare(strict_types=1);

namespace DomainCart\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Email
{
    private string $email;

    public function __construct(string $email)
    {
        $removendoEspacosEmBranco = trim($email);

        if ($removendoEspacosEmBranco === '') {
            throw new InvalidArgumentException(
                'O endereço de e-mail é obrigatório.'
            );
        }

        if (filter_var($removendoEspacosEmBranco, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException(
                'O endereço de e-mail informado é inválido.'
            );
        }

        $this->email = $removendoEspacosEmBranco;
    }

    public function email(): string
    {
        return $this->email;
    }
}
