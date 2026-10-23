<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\Entity;

use DomainCart\Domain\Entity\Carrinho;
use DomainCart\Domain\Entity\Item;
use DomainCart\Domain\Entity\Produto;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CarrinhoTest extends TestCase
{
    public function testNaoDeveCriarCarrinhoComIdVazio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'O identificador do carrinho é obrigatório.'
        );

        new Carrinho('');
    }

    public function testNaoDeveAdicionarItemDuplicado(): void
    {
        $carrinho = new Carrinho('carrinho-1');
        $item = $this->criarItem();

        $carrinho->adicionarNoCarrinho($item);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'O item já está presente no carrinho.'
        );

        $carrinho->adicionarNoCarrinho($item);
    }

    public function testNaoDeveRemoverItemInexistente(): void
    {
        $carrinho = new Carrinho('carrinho-1');
        $item = $this->criarItem();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'O item não está presente no carrinho.'
        );

        $carrinho->removerDoCarrinho($item);
    }

    public function testNaoDeveFinalizarCarrinhoVazio(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível finalizar um carrinho vazio.'
        );

        $carrinho->finalizar();
    }

    public function testNaoDeveFinalizarCarrinhoDuasVezes(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());
        $carrinho->finalizar();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'O carrinho já foi finalizado.'
        );

        $carrinho->finalizar();
    }

    public function testNaoDeveAdicionarItemEmCarrinhoFinalizado(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());
        $carrinho->finalizar();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível adicionar itens a um carrinho finalizado.'
        );

        $carrinho->adicionarNoCarrinho(
            $this->criarItem('item-2')
        );
    }

    public function testNaoDeveRemoverItemDeCarrinhoFinalizado(): void
    {
        $carrinho = new Carrinho('carrinho-1');
        $item = $this->criarItem();

        $carrinho->adicionarNoCarrinho($item);
        $carrinho->finalizar();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível remover itens de um carrinho finalizado.'
        );

        $carrinho->removerDoCarrinho($item);
    }

    public function testNaoDeveLimparCarrinhoFinalizado(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());
        $carrinho->finalizar();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível limpar um carrinho finalizado.'
        );

        $carrinho->limparCarrinho();
    }

    public function testNaoDeveAdicionarItemEmCarrinhoExpirado(): void
    {
        $carrinho = $this->criarCarrinhoExpirado();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível adicionar itens a um carrinho expirado.'
        );

        $carrinho->adicionarNoCarrinho(
            $this->criarItem('item-2')
        );
    }

    public function testNaoDeveRemoverItemDeCarrinhoExpirado(): void
    {
        $carrinho = $this->criarCarrinhoExpirado();
        $item = $carrinho->itens()[0];

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível remover itens de um carrinho expirado.'
        );

        $carrinho->removerDoCarrinho($item);
    }

    public function testNaoDeveLimparCarrinhoExpirado(): void
    {
        $carrinho = $this->criarCarrinhoExpirado();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível limpar um carrinho expirado.'
        );

        $carrinho->limparCarrinho();
    }

    public function testNaoDeveFinalizarCarrinhoExpirado(): void
    {
        $carrinho = $this->criarCarrinhoExpirado();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível finalizar um carrinho expirado.'
        );

        $carrinho->finalizar();
    }

    public function testNaoDeveAdicionarQuantidadeZero(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'A quantidade adicionada deve ser maior que zero.'
        );

        $carrinho->adicionarQuantidadeAoItem('item-1', 0);
    }

    public function testNaoDeveRemoverQuantidadeZero(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'A quantidade removida deve ser maior que zero.'
        );

        $carrinho->removerQuantidadeDoItem('item-1', 0);
    }

    public function testNaoDeveRemoverQuantidadeSuperiorAoDisponivel(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'A quantidade removida não pode superar a quantidade do item.'
        );

        $carrinho->removerQuantidadeDoItem('item-1', 3);
    }

    public function testNaoDeveAdicionarQuantidadeAItemInexistente(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'O item não está presente no carrinho.'
        );

        $carrinho->adicionarQuantidadeAoItem('item-99', 1);
    }

    public function testNaoDeveRemoverQuantidadeDeItemInexistente(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'O item não está presente no carrinho.'
        );

        $carrinho->removerQuantidadeDoItem('item-99', 1);
    }

    public function testNaoDeveAdicionarQuantidadeEmCarrinhoFinalizado(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());
        $carrinho->finalizar();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível alterar um carrinho finalizado.'
        );

        $carrinho->adicionarQuantidadeAoItem('item-1', 1);
    }

    public function testNaoDeveRemoverQuantidadeEmCarrinhoFinalizado(): void
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());
        $carrinho->finalizar();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível alterar um carrinho finalizado.'
        );

        $carrinho->removerQuantidadeDoItem('item-1', 1);
    }

    public function testNaoDeveAdicionarQuantidadeEmCarrinhoExpirado(): void
    {
        $carrinho = $this->criarCarrinhoExpirado();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível alterar um carrinho expirado.'
        );

        $carrinho->adicionarQuantidadeAoItem('item-1', 1);
    }

    public function testNaoDeveRemoverQuantidadeEmCarrinhoExpirado(): void
    {
        $carrinho = $this->criarCarrinhoExpirado();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Não é possível alterar um carrinho expirado.'
        );

        $carrinho->removerQuantidadeDoItem('item-1', 1);
    }

    private function criarCarrinhoExpirado(): Carrinho
    {
        $carrinho = new Carrinho('carrinho-1');

        $carrinho->adicionarNoCarrinho($this->criarItem());

        $dataDeInicio = $carrinho->dataDeInicio();

        $this->assertNotNull($dataDeInicio);

        $dataDeExpiracao = $dataDeInicio->modify('+48 hours');

        $carrinho->reconhecerExpiracao($dataDeExpiracao);

        return $carrinho;
    }

    private function criarItem(string $id = 'item-1'): Item
    {
        $produto = new Produto(
            nome: 'Leite Integral',
            descricao: 'Leite integral de 1 litro.',
            quantidadeEmEstoque: 10,
            valor: 2500,
            idadeMinima: 0,
            contemLactose: true
        );

        return new Item(
            id: $id,
            produto: $produto,
            quantidade: 2,
            valorUnitario: 2500
        );
    }
}
