<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\ValueObject;

use DateTimeImmutable;
use DomainCart\Domain\ValueObject\DataDeNascimento;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DataDeNascimentoTest extends TestCase
{
    public function testDeveCriarDataDeNascimentoValida(): void
    {
        $datasValidas = [
            '2000-01-15',
            '1995-12-31',
            '2000-02-29',
            '2024-02-29',
        ];

        foreach ($datasValidas as $data) {
            $objeto = new DataDeNascimento($data);

            $this->assertInstanceOf(
                DataDeNascimento::class,
                $objeto
            );
        }
    }

    public function testNaoDevePermitirDataDeNascimentoVazia(): void
    {
        $datasInvalidas = [
            '',
            ' ',
            '   ',
        ];

        foreach ($datasInvalidas as $data) {
            try {
                new DataDeNascimento($data);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A data de nascimento é obrigatória.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                'Uma data de nascimento vazia deveria ser rejeitada.'
            );
        }
    }

    public function testNaoDevePermitirDataInvalidaNoCalendario(): void
    {
        $datasInvalidas = [
            '2025-02-29',
            '2024-02-30',
            '2000-13-01',
            '2000-00-15',
            '2000-01-32',
            '2000-04-31',
        ];

        foreach ($datasInvalidas as $data) {
            try {
                new DataDeNascimento($data);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'Não foi possível interpretar a data de nascimento ou a data informada não existe no calendário.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "A data '{$data}' deveria ser rejeitada."
            );
        }
    }

    public function testNaoDevePermitirUmFormatoDeDataInvalido(): void
    {
        $datasInvalidas = [
            '2025/02-29',
            '2024-02/30',
            '2000.13.01',
            '20000015',
            '2000_01_32',
        ];

        foreach ($datasInvalidas as $data) {
            try {
                new DataDeNascimento($data);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A data de nascimento deve seguir o formato AAAA-MM-DD.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "A data '{$data}' deveria ser rejeitada."
            );
        }
    }

    public function testNaoDevePermitirDataDeNascimentoNoFuturo(): void
    {
        $datasFuturas = [
            new DateTimeImmutable('tomorrow'),
            new DateTimeImmutable('+1 month'),
            new DateTimeImmutable('+1 year'),
        ];

        foreach ($datasFuturas as $data) {
            try {
                new DataDeNascimento($data->format('Y-m-d'));
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A data de nascimento não deve ser posterior à data atual.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "A data '{$data->format('Y-m-d')}' deveria ser rejeitada."
            );
        }
    }
}
