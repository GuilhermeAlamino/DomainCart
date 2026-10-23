<?php

declare(strict_types=1);

namespace DomainCart\Tests\Unit\Domain\ValueObject;

use DomainCart\Domain\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testDeveCriarEmailValido(): void
    {
        $emailsValidos = [
            'cliente@email.com',
            'nome.sobrenome@email.com.br',
            'usuario+teste@email.com',
            '  cliente@email.com  ',
        ];

        foreach ($emailsValidos as $email) {
            $objeto = new Email($email);

            $this->assertInstanceOf(Email::class, $objeto);
            $this->assertSame(trim($email), $objeto->email());
        }
    }

    public function testNaoDevePermitirEmailVazio(): void
    {
        $emailsVazios = [
            '',
            ' ',
            '   ',
        ];

        foreach ($emailsVazios as $email) {
            try {
                new Email($email);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'O endereço de e-mail é obrigatório.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                'Um endereço de e-mail vazio deveria ser rejeitado.'
            );
        }
    }

    public function testNaoDevePermitirEmailComFormatoInvalido(): void
    {
        $emailsInvalidos = [
            'email-invalido',
            'clienteemail.com',
            'cliente@',
            '@email.com',
            'cliente@dominio',
            'cliente teste@email.com',
            'cliente@@email.com',
        ];

        foreach ($emailsInvalidos as $email) {
            try {
                new Email($email);
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'O endereço de e-mail informado é inválido.',
                    $exception->getMessage()
                );

                continue;
            }

            $this->fail(
                "O e-mail '{$email}' deveria ser rejeitado."
            );
        }
    }
}
