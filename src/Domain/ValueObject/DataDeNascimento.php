<?php

declare(strict_types=1);

namespace DomainCart\Domain\ValueObject;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class DataDeNascimento
{
    private DateTimeImmutable $dataDeNascimento;

    public function __construct(string $dataDeNascimento)
    {
        $removendoEspacosEmBranco = trim($dataDeNascimento);

        if ($removendoEspacosEmBranco === '') {
            throw new InvalidArgumentException(
                'A data de nascimento é obrigatória.'
            );
        }

        if (
            preg_match(
                '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',
                $removendoEspacosEmBranco
            ) !== 1
        ) {
            throw new InvalidArgumentException(
                'A data de nascimento deve seguir o formato AAAA-MM-DD.'
            );
        }

        $data = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $removendoEspacosEmBranco
        );

        if (
            $data === false
            || $data->format('Y-m-d') !== $removendoEspacosEmBranco
        ) {
            throw new InvalidArgumentException(
                'Não foi possível interpretar a data de nascimento ou a data informada não existe no calendário.'
            );
        }

        $hoje = new DateTimeImmutable('today');

        if ($data > $hoje) {
            throw new InvalidArgumentException(
                'A data de nascimento não deve ser posterior à data atual.'
            );
        }

        $this->dataDeNascimento = $data;
    }

    public function calcularIdade(): int
    {
        return $this->dataDeNascimento
            ->diff(new DateTimeImmutable('today'))
            ->y;
    }
}
