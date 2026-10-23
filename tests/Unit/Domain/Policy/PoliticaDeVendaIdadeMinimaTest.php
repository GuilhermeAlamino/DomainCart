<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\Policy;

use DateTimeImmutable;
use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\Entity\Produto;
use DomainCart\Domain\Policy\PoliticaDeVendaIdadeMinima;
use DomainCart\Domain\ValueObject\DataDeNascimento;
use DomainCart\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

final class PoliticaDeVendaIdadeMinimaTest extends TestCase
{
    public function testDevePermitirVendaQuandoClienteAtendeIdadeMinima(): void
    {
        $cenarios = [
            ['idade' => 18, 'idadeMinima' => 18],
            ['idade' => 25, 'idadeMinima' => 18],
            ['idade' => 17, 'idadeMinima' => 0],
        ];

        $politica = new PoliticaDeVendaIdadeMinima();

        foreach ($cenarios as $cenario) {
            $cliente = $this->criarCliente(
                idade: $cenario['idade']
            );

            $produto = $this->criarProduto(
                idadeMinima: $cenario['idadeMinima']
            );

            $this->assertTrue(
                $politica->permiteVenda($cliente, $produto)
            );
        }
    }

    public function testNaoDevePermitirVendaQuandoClienteNaoAtendeIdadeMinima(): void
    {
        $cenarios = [
            ['idade' => 17, 'idadeMinima' => 18],
            ['idade' => 15, 'idadeMinima' => 16],
            ['idade' => 10, 'idadeMinima' => 18],
        ];

        $politica = new PoliticaDeVendaIdadeMinima();

        foreach ($cenarios as $cenario) {
            $cliente = $this->criarCliente(
                idade: $cenario['idade']
            );

            $produto = $this->criarProduto(
                idadeMinima: $cenario['idadeMinima']
            );

            $this->assertFalse(
                $politica->permiteVenda($cliente, $produto)
            );
        }
    }

    private function criarCliente(
        string $nome = 'Cliente Teste',
        ?Email $email = null,
        ?DataDeNascimento $dataDeNascimento = null,
        bool $intoleranciaLactose = false,
        int $idade = 25
    ): Cliente {
        return new Cliente(
            nome: $nome,
            email: $email ?? new Email('cliente@email.com'),
            dataDeNascimento: $dataDeNascimento
                ?? new DataDeNascimento(
                    (new DateTimeImmutable('today'))
                        ->modify("-{$idade} years")
                        ->format('Y-m-d')
                ),
            intoleranciaLactose: $intoleranciaLactose
        );
    }

    private function criarProduto(
        string $nome = 'Produto Teste',
        string $descricao = 'Descrição do produto.',
        int $quantidadeEmEstoque = 10,
        int $valor = 2500,
        int $idadeMinima = 0,
        bool $contemLactose = false
    ): Produto {
        return new Produto(
            nome: $nome,
            descricao: $descricao,
            quantidadeEmEstoque: $quantidadeEmEstoque,
            valor: $valor,
            idadeMinima: $idadeMinima,
            contemLactose: $contemLactose
        );
    }
}
