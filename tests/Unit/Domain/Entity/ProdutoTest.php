<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\Entity;

use DomainCart\Domain\Entity\Produto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ProdutoTest extends TestCase
{
    public function testDeveCriarProdutoComDadosValidos(): void
    {
        $produto = $this->criarProduto();

        $this->assertInstanceOf(Produto::class, $produto);
        $this->assertSame(10, $produto->quantidadeEmEstoque());
        $this->assertSame(2500, $produto->valor());
        $this->assertSame(0, $produto->idadeMinima());
        $this->assertFalse($produto->contemLactose());
    }

    public function testNaoDevePermitirNomeVazio(): void
    {
        $nomesInvalidos = ['', ' ', '   '];

        foreach ($nomesInvalidos as $nome) {
            try {
                $this->criarProduto(nome: $nome);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'O nome é obrigatório.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                'Um produto com nome vazio deveria ser rejeitado.'
            );
        }
    }

    public function testNaoDevePermitirNomeComMenosDeTresCaracteres(): void
    {
        $nomesInvalidos = ['A', 'Ab', '  Ab  '];

        foreach ($nomesInvalidos as $nome) {
            try {
                $this->criarProduto(nome: $nome);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'O nome deve conter pelo menos três caracteres.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "O nome '{$nome}' deveria ser rejeitado."
            );
        }
    }

    public function testNaoDevePermitirDescricaoVazia(): void
    {
        $descricoesInvalidas = ['', ' ', '   '];

        foreach ($descricoesInvalidas as $descricao) {
            try {
                $this->criarProduto(descricao: $descricao);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A descrição é obrigatória.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                'Um produto com descrição vazia deveria ser rejeitado.'
            );
        }
    }

    public function testNaoDevePermitirEstoqueNegativo(): void
    {
        $estoquesInvalidos = [-1, -10, -100];

        foreach ($estoquesInvalidos as $estoque) {
            try {
                $this->criarProduto(
                    quantidadeEmEstoque: $estoque
                );
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A quantidade em estoque não pode ser negativa.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "O estoque '{$estoque}' deveria ser rejeitado."
            );
        }
    }

    public function testNaoDevePermitirValorMenorOuIgualAZero(): void
    {
        $valoresInvalidos = [0, -1, -100];

        foreach ($valoresInvalidos as $valor) {
            try {
                $this->criarProduto(valor: $valor);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'O valor deve ser maior que zero.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "O valor '{$valor}' deveria ser rejeitado."
            );
        }
    }

    public function testNaoDevePermitirIdadeMinimaNegativa(): void
    {
        $idadesInvalidas = [-1, -2, -10];

        foreach ($idadesInvalidas as $idadeMinima) {
            try {
                $this->criarProduto(
                    idadeMinima: $idadeMinima
                );
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'A idade mínima deve ser maior ou igual a zero.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "A idade mínima '{$idadeMinima}' deveria ser rejeitada."
            );
        }
    }

    private function criarProduto(
        string $nome = 'Leite Integral',
        string $descricao = 'Leite integral de 1 litro.',
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
