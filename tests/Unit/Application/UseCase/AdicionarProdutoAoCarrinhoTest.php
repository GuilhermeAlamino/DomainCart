<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Application\UseCase;

use DomainCart\Application\UseCase\AdicionarProdutoAoCarrinho;
use DomainCart\Domain\Entity\Carrinho;
use DomainCart\Domain\Entity\Cliente;
use DomainCart\Domain\Entity\Item;
use DomainCart\Domain\Entity\Produto;
use DomainCart\Domain\Policy\AvaliadorDePoliticasDeVenda;
use DomainCart\Domain\Policy\PoliticaDeVendaIdadeMinima;
use DomainCart\Domain\Policy\PoliticaDeVendaIntoleranciaALactose;
use DomainCart\Domain\ValueObject\DataDeNascimento;
use DomainCart\Domain\ValueObject\Email;
use DomainException;
use PHPUnit\Framework\TestCase;

final class AdicionarProdutoAoCarrinhoTest extends TestCase
{
    public function testAdicionaItemQuandoTodasAsPoliticasPermitem(): void
    {
        $cliente = $this->criarCliente();
        $produto = $this->criarProduto();

        $carrinho = new Carrinho('carrinho-1');
        $item = new Item('item-1', $produto, 2, $produto->valor());

        $useCase = $this->criarUseCase();

        $useCase->executar($cliente, $carrinho, $item);

        $this->assertCount(1, $carrinho->itens());
        $this->assertSame($item, $carrinho->itens()[0]);
        $this->assertSame(3000, $carrinho->valorTotal());
    }

    public function testNaoAdicionaItemQuandoClienteNaoTemIdadeMinima(): void
    {
        $cliente = $this->criarCliente(
            dataDeNascimento: '2020-01-01'
        );

        $produto = $this->criarProduto(
            idadeMinima: 18
        );

        $this->verificarVendaNaoPermitida($cliente, $produto);
    }

    public function testNaoAdicionaItemQuandoClienteTemIntoleranciaALactose(): void
    {
        $cliente = $this->criarCliente(
            intoleranciaLactose: true
        );

        $produto = $this->criarProduto(
            contemLactose: true
        );

        $this->verificarVendaNaoPermitida($cliente, $produto);
    }

    private function verificarVendaNaoPermitida(
        Cliente $cliente,
        Produto $produto
    ): void {
        $carrinho = new Carrinho('carrinho-1');
        $item = new Item('item-1', $produto, 2, $produto->valor());

        $useCase = $this->criarUseCase();

        try {
            $useCase->executar($cliente, $carrinho, $item);

            $this->fail('Era esperada uma exceção.');
        } catch (DomainException $exception) {
            $this->assertSame(
                'O produto não atende às políticas de venda.',
                $exception->getMessage()
            );
        }

        $this->assertSame([], $carrinho->itens());
        $this->assertSame(0, $carrinho->valorTotal());
    }

    private function criarUseCase(): AdicionarProdutoAoCarrinho
    {
        return new AdicionarProdutoAoCarrinho(
            new AvaliadorDePoliticasDeVenda(
                new PoliticaDeVendaIdadeMinima(),
                new PoliticaDeVendaIntoleranciaALactose()
            )
        );
    }

    private function criarCliente(
        string $nome = 'Cliente Teste',
        string $email = 'cliente@teste.com',
        string $dataDeNascimento = '1995-01-01',
        bool $intoleranciaLactose = false
    ): Cliente {
        return new Cliente(
            $nome,
            new Email($email),
            new DataDeNascimento($dataDeNascimento),
            $intoleranciaLactose
        );
    }

    private function criarProduto(
        string $nome = 'Produto Teste',
        string $descricao = 'Descrição do produto',
        int $quantidadeEmEstoque = 10,
        int $valor = 1500,
        int $idadeMinima = 0,
        bool $contemLactose = false
    ): Produto {
        return new Produto(
            $nome,
            $descricao,
            $quantidadeEmEstoque,
            $valor,
            $idadeMinima,
            $contemLactose
        );
    }
}
