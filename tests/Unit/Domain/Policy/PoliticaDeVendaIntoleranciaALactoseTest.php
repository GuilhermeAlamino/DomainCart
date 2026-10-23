<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\Policy;

use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\Entity\Produto;
use DomainCart\Domain\Policy\PoliticaDeVendaIntoleranciaALactose;
use DomainCart\Domain\ValueObject\DataDeNascimento;
use DomainCart\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

final class PoliticaDeVendaIntoleranciaALactoseTest extends TestCase
{
    public function testDevePermitirVendaQuandoNaoHaRestricaoDeLactose(): void
    {
        $cenarios = [
            ['intolerancia' => false, 'contemLactose' => false],
            ['intolerancia' => false, 'contemLactose' => true],
            ['intolerancia' => true, 'contemLactose' => false],
        ];

        $politica = new PoliticaDeVendaIntoleranciaALactose();

        foreach ($cenarios as $cenario) {
            $cliente = $this->criarCliente(
                intoleranciaLactose: $cenario['intolerancia']
            );

            $produto = $this->criarProduto(
                contemLactose: $cenario['contemLactose']
            );

            $this->assertTrue(
                $politica->permiteVenda($cliente, $produto)
            );
        }
    }

    public function testNaoDevePermitirVendaDeProdutoComLactoseParaIntolerante(): void
    {
        $cliente = $this->criarCliente(
            intoleranciaLactose: true
        );

        $produto = $this->criarProduto(
            contemLactose: true
        );

        $politica = new PoliticaDeVendaIntoleranciaALactose();

        $this->assertFalse(
            $politica->permiteVenda($cliente, $produto)
        );
    }

    private function criarCliente(
        string $nome = 'Cliente Teste',
        ?Email $email = null,
        ?DataDeNascimento $dataDeNascimento = null,
        bool $intoleranciaLactose = false
    ): Cliente {
        return new Cliente(
            nome: $nome,
            email: $email ?? new Email('cliente@email.com'),
            dataDeNascimento: $dataDeNascimento
                ?? new DataDeNascimento('2000-01-01'),
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
