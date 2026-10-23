# Requisitos e critérios de aceitação

[← Voltar ao README](../README.md)

## Uma solicitação, muitas possibilidades

Imagine uma situação comum no desenvolvimento de software: um cliente procura um desenvolvedor ou uma equipe de tecnologia e apresenta uma necessidade.

> Preciso de uma loja virtual com carrinho de compras.

À primeira vista, a solicitação parece suficientemente clara. Afinal, sabemos o que é um carrinho de compras e provavelmente já utilizamos vários deles.

Com experiência em programação, podemos começar imediatamente a imaginar as telas, as tabelas do banco de dados, as classes e as tecnologias necessárias para construir a solução.

Eu mesmo já trabalhei dessa maneira.

Durante parte da minha trajetória, tive mais experiência com linguagens, tecnologias e implementação do que com entrevistas, levantamento e refinamento de requisitos.

Com o tempo, passei a compreender algo importante: **saber como implementar uma solução não significa necessariamente ter compreendido o problema que precisa ser resolvido.**

Uma solicitação aparentemente simples pode esconder necessidades, restrições e decisões de negócio que ainda não foram esclarecidas.

## Antes de implementar, precisamos investigar

Quando um cliente solicita uma loja virtual, algumas perguntas começam a surgir:

- A loja venderá para pessoas físicas, jurídicas ou ambas?
- Os produtos terão restrições ou políticas comerciais específicas?
- Quais formas de pagamento serão aceitas?
- O cliente precisa estar autenticado para realizar uma compra?
- Existe a necessidade de acompanhar os carrinhos dos clientes?
- Os carrinhos podem expirar após determinado período?
- O preço de um produto deve permanecer o mesmo depois que ele é adicionado ao carrinho?

E essas são apenas algumas das perguntas possíveis.

Dependendo das respostas, podemos identificar comportamentos que não estavam explícitos na solicitação inicial, descobrir novas responsabilidades ou perceber que determinadas funcionalidades não são necessárias.

Isso não significa necessariamente que estamos identificando novas funcionalidades ou solicitações de evolução. Muitas vezes, estamos apenas esclarecendo aquilo que ainda não foi suficientemente especificado.

É nesse momento que o refinamento de requisitos começa a influenciar a solução.

**Antes de decidir como construir, precisamos compreender o que deve ser construído e quais comportamentos o negócio espera.**

## O recorte do DomainCart

O DomainCart parte desse cenário, mas não tem como objetivo implementar uma loja virtual completa.

O projeto concentra-se na modelagem de um domínio de carrinho de compras, explorando responsabilidades, regras de negócio e políticas comerciais.

A investigação inicial ajuda a identificar conceitos como:

- **Cliente:** quem está envolvido na operação de compra.
- **Produto:** aquilo que pode ser comercializado.
- **Item:** a representação de um produto selecionado, com quantidade e valor considerados no carrinho.
- **Carrinho:** responsável por reunir e gerenciar os itens de uma compra.
- **Políticas de venda:** regras utilizadas para avaliar determinadas condições comerciais.

Esses conceitos não surgem simplesmente porque precisamos criar classes. Eles representam responsabilidades identificadas a partir do contexto investigado.

A explicação sobre como chegamos a essas representações está disponível em [Modelagem do domínio](modelagem-do-dominio.md).

## Histórias de usuário e critérios de aceitação

As histórias de usuário devem representar necessidades e comportamentos esperados, enquanto os critérios de aceitação estabelecem condições verificáveis para considerar esses comportamentos atendidos.

Esta seção será preenchida a partir do documento original de requisitos do DomainCart, preservando o conteúdo efetivamente definido e distinguindo requisitos implementados de hipóteses e possibilidades de evolução.

## Cenários de comportamento

Os cenários permitem representar situações específicas do domínio, incluindo as condições necessárias, as ações realizadas e os resultados esperados.

Sempre que possível, eles serão relacionados aos testes automatizados correspondentes.

Essa relação contribui para a rastreabilidade entre:

**Requisito → Comportamento esperado → Implementação → Teste**

A existência de um teste não garante, por si só, que todos os aspectos de um requisito foram atendidos. Entretanto, permite verificar quais comportamentos foram especificados e exercitados.

Os cenários originais serão incorporados nesta seção após a conferência com os requisitos e testes do projeto.

## Limites de escopo

Embora a solicitação inicial envolva uma loja virtual, o DomainCart concentra-se no domínio do carrinho de compras.

A existência de conceitos como Cliente e Produto não implica a implementação de módulos completos de cadastro, autenticação, administração de produtos ou processamento de pagamentos.

Da mesma maneira, considerar informações relacionadas ao ciclo de vida do carrinho não significa que mecanismos de monitoramento, abandono ou expiração estejam automaticamente implementados.

Essas responsabilidades precisam ser especificadas e avaliadas separadamente.

O propósito deste recorte é permitir que as regras de negócio do carrinho sejam compreendidas, modeladas e verificadas sem depender da construção de todo um e-commerce.