<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\Entity;

use DateTimeImmutable;
use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\ValueObject\DataDeNascimento;
use DomainCart\Domain\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ClienteTest extends TestCase
{
    public function testDeveCriarClienteComDadosValidos(): void
    {
        $email = new Email('cliente@email.com');

        $cliente = $this->criarCliente(
            nome: 'Guilherme Alamino',
            email: $email
        );

        $this->assertInstanceOf(Cliente::class, $cliente);
        $this->assertSame($email, $cliente->email());
        $this->assertTrue($cliente->intoleranciaLactose());
    }

    public function testNaoDevePermitirNomeVazio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'O nome do cliente é obrigatório.'
        );

        $this->criarCliente(nome: '');
    }

    public function testNaoDevePermitirNomeSomenteComEspacos(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'O nome do cliente é obrigatório.'
        );

        $this->criarCliente(nome: '   ');
    }

    public function testNaoDevePermitirNomeComMenosDeTresCaracteres(): void
    {
        $nomesInvalidos = [
            'A',
            'Ab',
            '  Ab  ',
        ];

        foreach ($nomesInvalidos as $nome) {
            try {
                $this->criarCliente(nome: $nome);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'O nome do cliente deve conter no mínimo 3 caracteres.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "O nome '{$nome}' deveria ser rejeitado."
            );
        }
    }

    public function testDeveCalcularIdadeDeDezoitoAnos(): void
    {
        $dataNascimento = new DateTimeImmutable('today -18 years');

        $cliente = $this->criarCliente(
            dataDeNascimento: new DataDeNascimento(
                $dataNascimento->format('Y-m-d')
            )
        );

        $this->assertSame(18, $cliente->calcularIdade());
    }

    private function criarCliente(
        string $nome = 'Guilherme Alamino',
        ?Email $email = null,
        ?DataDeNascimento $dataDeNascimento = null,
        bool $intoleranciaLactose = true
    ): Cliente {
        return new Cliente(
            nome: $nome,
            email: $email ?? new Email('cliente@email.com'),
            dataDeNascimento: $dataDeNascimento
                ?? new DataDeNascimento('2000-01-15'),
            intoleranciaLactose: $intoleranciaLactose
        );
    }
}
