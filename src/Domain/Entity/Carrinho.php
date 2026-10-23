<?php

declare(strict_types=1);

namespace DomainCart\Domain\Entity;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;

final class Carrinho
{
    private string $id;
    /** @var array<string, Item> */
    private array $itens = [];
    private ?DateTimeImmutable $dataDeInicioDoCarrinho = null;
    private ?DateTimeImmutable $dataDeFimDoCarrinho = null;
    private bool $expirado = false;
    private bool $finalizado = false;

    public function __construct(
        string $id
    ) {
        $idNormalizado = trim($id);

        $this->id = $idNormalizado;

        if ($this->id === '') {
            throw new InvalidArgumentException(
                'O identificador do carrinho é obrigatório.'
            );
        }
    }

    public function dataDeInicio(): ?DateTimeImmutable
    {
        return $this->dataDeInicioDoCarrinho;
    }

    public function dataDeFim(): ?DateTimeImmutable
    {
        return $this->dataDeFimDoCarrinho;
    }

    public function adicionarNoCarrinho(Item $item): void
    {
        if ($this->finalizado) {
            throw new DomainException(
                'Não é possível adicionar itens a um carrinho finalizado.'
            );
        }

        $this->reconhecerExpiracao();

        if ($this->expirado) {
            throw new DomainException(
                'Não é possível adicionar itens a um carrinho expirado.'
            );
        }

        $idDoItem = $item->id();

        if (isset($this->itens[$idDoItem])) {
            throw new DomainException(
                'O item já está presente no carrinho.'
            );
        }

        if ($this->itens === []) {
            $this->dataDeInicioDoCarrinho = new DateTimeImmutable();
        }

        $this->itens[$idDoItem] = $item;
    }

    public function removerDoCarrinho(Item $item): void
    {
        if ($this->finalizado) {
            throw new DomainException(
                'Não é possível remover itens de um carrinho finalizado.'
            );
        }

        $this->reconhecerExpiracao();

        if ($this->expirado) {
            throw new DomainException(
                'Não é possível remover itens de um carrinho expirado.'
            );
        }

        $idDoItem = $item->id();

        if (!isset($this->itens[$idDoItem])) {
            throw new DomainException(
                'O item não está presente no carrinho.'
            );
        }

        unset($this->itens[$idDoItem]);

        if ($this->itens === []) {
            $this->dataDeInicioDoCarrinho = null;
        }
    }

    public function limparCarrinho(): void
    {
        if ($this->finalizado) {
            throw new DomainException(
                'Não é possível limpar um carrinho finalizado.'
            );
        }

        $this->reconhecerExpiracao();

        if ($this->expirado) {
            throw new DomainException(
                'Não é possível limpar um carrinho expirado.'
            );
        }

        $this->itens = [];
        $this->dataDeInicioDoCarrinho = null;
    }

    public function finalizar(): void
    {
        if ($this->finalizado) {
            throw new DomainException(
                'O carrinho já foi finalizado.'
            );
        }

        $this->reconhecerExpiracao();

        if ($this->expirado) {
            throw new DomainException(
                'Não é possível finalizar um carrinho expirado.'
            );
        }

        if ($this->itens === []) {
            throw new DomainException(
                'Não é possível finalizar um carrinho vazio.'
            );
        }

        $this->finalizado = true;

        $this->dataDeInicioDoCarrinho = null;
        $this->dataDeFimDoCarrinho = null;
    }

    public function reconhecerExpiracao(
        ?DateTimeImmutable $agora = null
    ): bool {
        if ($this->finalizado === true) {
            return false;
        }

        if ($this->expirado === true) {
            return false;
        }

        if ($this->itens === []) {
            return false;
        }

        if ($this->dataDeInicioDoCarrinho === null) {
            return false;
        }

        if ($agora === null) {
            $agora = new DateTimeImmutable();
        }

        $limite = $this->dataDeInicioDoCarrinho->modify(
            '+48 hours'
        );

        if ($agora < $limite) {
            return false;
        }

        $this->dataDeFimDoCarrinho = $agora;
        $this->expirado = true;

        return true;
    }

    public function valorTotal(): int
    {
        $total = 0;

        foreach ($this->itens as $item) {
            $total += $item->valorTotal();
        }

        return $total;
    }

    /**
     * @return list<Item>
     */
    public function itens(): array
    {
        return array_values($this->itens);
    }

    public function finalizado(): bool
    {
        return $this->finalizado;
    }

    public function expirado(): bool
    {
        return $this->expirado;
    }

    public function adicionarQuantidadeAoItem(
        string $idDoItem,
        int $quantidade
    ): void {
        if ($this->finalizado) {
            throw new DomainException(
                'Não é possível alterar um carrinho finalizado.'
            );
        }

        $this->reconhecerExpiracao();

        if ($this->expirado) {
            throw new DomainException(
                'Não é possível alterar um carrinho expirado.'
            );
        }

        if ($quantidade <= 0) {
            throw new InvalidArgumentException(
                'A quantidade adicionada deve ser maior que zero.'
            );
        }

        if (!isset($this->itens[$idDoItem])) {
            throw new DomainException(
                'O item não está presente no carrinho.'
            );
        }

        $item = $this->itens[$idDoItem];

        $item->adicionarQuantidade($quantidade);
    }

    public function removerQuantidadeDoItem(
        string $idDoItem,
        int $quantidade
    ): void {
        if ($this->finalizado) {
            throw new DomainException(
                'Não é possível alterar um carrinho finalizado.'
            );
        }

        $this->reconhecerExpiracao();

        if ($this->expirado) {
            throw new DomainException(
                'Não é possível alterar um carrinho expirado.'
            );
        }

        if ($quantidade <= 0) {
            throw new InvalidArgumentException(
                'A quantidade removida deve ser maior que zero.'
            );
        }

        if (isset($this->itens[$idDoItem]) === false) {
            throw new DomainException(
                'O item não está presente no carrinho.'
            );
        }

        $item = $this->itens[$idDoItem];

        if ($quantidade > $item->quantidade()) {
            throw new InvalidArgumentException(
                'A quantidade removida não pode superar a quantidade do item.'
            );
        }

        $quantidadeRestante = $item->removerQuantidade(
            $quantidade
        );

        if ($quantidadeRestante === 0) {
            unset($this->itens[$idDoItem]);
        }

        if ($this->itens === []) {
            $this->dataDeInicioDoCarrinho = null;
        }
    }
}
