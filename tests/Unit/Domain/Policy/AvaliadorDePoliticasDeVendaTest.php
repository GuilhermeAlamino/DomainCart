<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\Policy;

use DateTimeImmutable;
use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\Entity\Produto;
use DomainCart\Domain\Policy\AvaliadorDePoliticasDeVenda;
use DomainCart\Domain\Policy\PoliticaDeVendaIdadeMinima;
use DomainCart\Domain\Policy\PoliticaDeVendaIntoleranciaALactose;
use DomainCart\Domain\ValueObject\DataDeNascimento;
use DomainCart\Domain\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AvaliadorDePoliticasDeVendaTest extends TestCase
{
    public function testDevePermitirVendaQuandoTodasAsPoliticasSaoAtendidas(): void
    {
        $cliente = $this->criarCliente(
            idade: 25,
            intoleranciaLactose: false
        );

        $produto = $this->criarProduto(
            idadeMinima: 18,
            contemLactose: true
        );

        $avaliador = $this->criarAvaliador();

        $this->assertTrue(
            $avaliador->permiteVenda($cliente, $produto)
        );
    }

    public function testNaoDevePermitirVendaQuandoIdadeMinimaNaoEhAtendida(): void
    {
        $cliente = $this->criarCliente(
            idade: 17,
            intoleranciaLactose: false
        );

        $produto = $this->criarProduto(
            idadeMinima: 18,
            contemLactose: false
        );

        $this->assertFalse(
            $this->criarAvaliador()->permiteVenda($cliente, $produto)
        );
    }

    public function testNaoDevePermitirVendaQuandoPoliticaDeVendaLactoseNaoEhAtendida(): void
    {
        $cliente = $this->criarCliente(
            idade: 25,
            intoleranciaLactose: true
        );

        $produto = $this->criarProduto(
            idadeMinima: 18,
            contemLactose: true
        );

        $this->assertFalse(
            $this->criarAvaliador()->permiteVenda($cliente, $produto)
        );
    }

    public function testNaoDevePermitirVendaQuandoNenhumaRestricaoEhAtendida(): void
    {
        $cliente = $this->criarCliente(
            idade: 17,
            intoleranciaLactose: true
        );

        $produto = $this->criarProduto(
            idadeMinima: 18,
            contemLactose: true
        );

        $this->assertFalse(
            $this->criarAvaliador()->permiteVenda($cliente, $produto)
        );
    }

    public function testDevePermitirAvaliacaoComUmaUnicaPolitica(): void
    {
        $cliente = $this->criarCliente(idade: 25);
        $produto = $this->criarProduto(idadeMinima: 18);

        $avaliador = new AvaliadorDePoliticasDeVenda(
            new PoliticaDeVendaIdadeMinima()
        );

        $this->assertTrue(
            $avaliador->permiteVenda($cliente, $produto)
        );
    }

    public function testNaoDevePermitirAvaliadorSemPoliticas(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'É necessário informar ao menos uma política de venda.'
        );

        new AvaliadorDePoliticasDeVenda();
    }

    private function criarAvaliador(): AvaliadorDePoliticasDeVenda
    {
        return new AvaliadorDePoliticasDeVenda(
            new PoliticaDeVendaIdadeMinima(),
            new PoliticaDeVendaIntoleranciaALactose()
        );
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
