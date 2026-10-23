<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\Entity;

use DomainCart\Domain\Entity\Item;
use DomainCart\Domain\Entity\Produto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    public function testDeveCriarItemComDadosValidos(): void
    {
        $item = $this->criarItem();

        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame('item-1', $item->id());
        $this->assertSame(2, $item->quantidade());
        $this->assertSame(2500, $item->valorUnitario());
        $this->assertInstanceOf(Produto::class, $item->produto());
    }

    public function testNaoDevePermitirIdentificadorVazio(): void
    {
        $identificadoresInvalidos = ['', ' ', '   '];

        foreach ($identificadoresInvalidos as $id) {
            try {
                $this->criarItem(id: $id);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'O identificador do item é obrigatório.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                'Um item sem identificador deveria ser rejeitado.'
            );
        }
    }

    public function testNaoDevePermitirQuantidadeMenorOuIgualAZero(): void
    {
        $quantidadesInvalidas = [0, -1, -10];

        foreach ($quantidadesInvalidas as $quantidade) {
            try {
                $this->criarItem(quantidade: $quantidade);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A quantidade deve ser maior que zero.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "A quantidade '{$quantidade}' deveria ser rejeitada."
            );
        }
    }

    public function testNaoDevePermitirValorUnitarioMenorOuIgualAZero(): void
    {
        $valoresInvalidos = [0, -1, -100];

        foreach ($valoresInvalidos as $valor) {
            try {
                $this->criarItem(valorUnitario: $valor);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'O valor unitário deve ser maior que zero.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "O valor unitário '{$valor}' deveria ser rejeitado."
            );
        }
    }

    public function testDeveCalcularValorTotalDoItem(): void
    {
        $quantidades = [1, 2, 5, 10];

        foreach ($quantidades as $quantidade) {
            $item = $this->criarItem(
                quantidade: $quantidade,
                valorUnitario: 2500
            );

            $this->assertSame(
                $quantidade * 2500,
                $item->valorTotal()
            );
        }
    }

    public function testDeveAdicionarQuantidadeAoItem(): void
    {
        $item = $this->criarItem(quantidade: 2);

        $quantidadeAtual = $item->adicionarQuantidade(3);

        $this->assertSame(5, $quantidadeAtual);
        $this->assertSame(5, $item->quantidade());
        $this->assertSame(12500, $item->valorTotal());
    }

    public function testNaoDeveAdicionarQuantidadeInvalida(): void
    {
        $quantidadesInvalidas = [0, -1, -10];

        foreach ($quantidadesInvalidas as $quantidade) {
            $item = $this->criarItem();

            try {
                $item->adicionarQuantidade($quantidade);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A quantidade adicionada deve ser maior que zero.',
                    $exception->getMessage()
                );

                $this->assertSame(2, $item->quantidade());

                continue;
            }

            $this->fail(
                "A quantidade '{$quantidade}' deveria ser rejeitada."
            );
        }
    }

    public function testDeveRemoverQuantidadeDoItem(): void
    {
        $item = $this->criarItem(quantidade: 5);

        $quantidadeAtual = $item->removerQuantidade(2);

        $this->assertSame(3, $quantidadeAtual);
        $this->assertSame(3, $item->quantidade());
        $this->assertSame(7500, $item->valorTotal());
    }

    public function testDevePermitirRemoverTodaQuantidadeDoItem(): void
    {
        $item = $this->criarItem(quantidade: 2);

        $quantidadeAtual = $item->removerQuantidade(2);

        $this->assertSame(0, $quantidadeAtual);
        $this->assertSame(0, $item->quantidade());
        $this->assertSame(0, $item->valorTotal());
    }

    public function testNaoDeveRemoverQuantidadeInvalida(): void
    {
        $quantidadesInvalidas = [0, -1, -10];

        foreach ($quantidadesInvalidas as $quantidade) {
            $item = $this->criarItem();

            try {
                $item->removerQuantidade($quantidade);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A quantidade removida deve ser maior que zero.',
                    $exception->getMessage()
                );

                $this->assertSame(2, $item->quantidade());

                continue;
            }

            $this->fail(
                "A quantidade '{$quantidade}' deveria ser rejeitada."
            );
        }
    }

    public function testNaoDeveRemoverQuantidadeMaiorQueDisponivel(): void
    {
        $item = $this->criarItem(quantidade: 2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'A quantidade removida não pode superar a quantidade do item.'
        );

        $item->removerQuantidade(3);
    }

    private function criarItem(
        string $id = 'item-1',
        ?Produto $produto = null,
        int $quantidade = 2,
        int $valorUnitario = 2500
    ): Item {
        return new Item(
            id: $id,
            produto: $produto ?? new Produto(
                nome: 'Leite Integral',
                descricao: 'Leite integral de 1 litro.',
                quantidadeEmEstoque: 10,
                valor: 2500,
                idadeMinima: 0,
                contemLactose: true
            ),
            quantidade: $quantidade,
            valorUnitario: $valorUnitario
        );
    }
}
