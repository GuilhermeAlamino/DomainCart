# Decisões técnicas

[← Voltar ao README](../README.md)

## Visão arquitetural

O DomainCart é um módulo de domínio de carrinho de compras desenvolvido em PHP 8.2, com foco na representação de regras de negócio, responsabilidades e comportamentos.

Sua organização é orientada ao domínio, incorporando conceitos de Domain-Driven Design (DDD), orientação a objetos e princípios SOLID quando aplicáveis ao problema.

A modelagem considera entidades com comportamentos próprios, Value Objects para encapsulamento de valores e políticas comerciais independentes, utilizando o padrão Strategy.

A prioridade é manter as regras de negócio explícitas e organizadas, sem introduzir dependências de infraestrutura que não são necessárias ao escopo atual.

**O DomainCart não se apresenta como uma implementação completa de Clean Architecture.** O projeto concentra-se no domínio, sem contemplar uma aplicação completa com camadas de orquestração, persistência e adaptadores externos.

Isso não impede que sua organização adote princípios compatíveis com essa abordagem, como o isolamento das regras de negócio e a definição clara de responsabilidades.

As decisões a seguir descrevem a modelagem adotada. Sua correspondência integral com a implementação deverá ser verificada no código e nos testes.

---

## 1. Delimitação do domínio

### Contexto

Uma loja virtual envolve diferentes responsabilidades: cadastro de clientes, catálogo de produtos, autenticação, estoque, pagamentos e gerenciamento de pedidos.

Entretanto, o objetivo do DomainCart não é implementar todo esse ecossistema.

### Decisão

Concentrar o projeto nas operações e regras de negócio relacionadas ao carrinho de compras, incluindo a representação dos produtos selecionados e a avaliação de políticas comerciais.

### Justificativa

Essa delimitação permite desenvolver o domínio sem depender antecipadamente de mecanismos de persistência, interfaces HTTP ou serviços externos.

Também reduz a necessidade de criar abstrações para problemas que ainda não foram identificados nos requisitos.

### Consequências

- O domínio pode ser estudado e testado isoladamente.
- Responsabilidades externas ao carrinho permanecem fora do escopo atual.
- Novas integrações deverão ser avaliadas conforme necessidades reais surgirem.

Essa escolha não determina antecipadamente a arquitetura de uma futura aplicação. Ela estabelece apenas o recorte necessário para o problema estudado.

## 2. Separação entre Produto e Item

### Problema

Um Produto representa aquilo que está disponível para comercialização. Entretanto, quando selecionado para uma compra, precisamos representar informações que pertencem àquela seleção, como quantidade e valor unitário.

Concentrar essas responsabilidades no Produto misturaria informações do catálogo com informações específicas de um carrinho.

### Decisão

Separar os conceitos em dois objetos:

- `Produto`: representa as características comerciais do produto.
- `Item`: representa um produto selecionado no carrinho, com quantidade, valor unitário e valor total calculado.

### Justificativa

Um mesmo Produto pode participar de diferentes carrinhos, com quantidades e valores considerados em contextos distintos.

O Item permite representar essas informações sem alterar o Produto para cada seleção.

Essa separação também mantém os comportamentos relacionados à quantidade próximos ao objeto responsável por representá-la.

### Consequências

O Carrinho passa a gerenciar uma coleção de itens, enquanto cada Item mantém uma referência ao Produto.

Existe uma relação entre esses objetos, mas suas responsabilidades permanecem distintas.

Essa decisão também permite tratar regras de preço específicas do carrinho, sem necessariamente modificar o preço de venda do Produto.

## 3. Valor unitário registrado no Item

### Problema

O preço de venda de um produto pode mudar depois que ele é adicionado ao carrinho.

Nesse cenário, surge uma questão de negócio:

> Qual preço deve ser considerado para um produto que já está no carrinho?

A resposta depende das condições comerciais estabelecidas.

### Decisão

Manter o valor unitário no `Item`, separadamente do valor atual de venda do `Produto`.

### Justificativa

Essa representação permite distinguir o preço atual do catálogo daquele considerado para uma seleção específica.

Também oferece uma estrutura para comportamentos como manutenção temporária do preço ou atualização antes da finalização da compra.

### Consequências

O Item utiliza seu valor unitário e sua quantidade para calcular o valor total correspondente.

Entretanto, registrar um preço no Item não estabelece automaticamente uma garantia comercial.

Permanecem dependentes de especificação questões como:

- Quando o preço deve ser atualizado.
- Por quanto tempo um valor pode permanecer válido.
- Como alterações de preço devem afetar carrinhos existentes.

A modelagem permite representar essas possibilidades, mas não define sozinha qual política comercial será aplicada.

## 4. Políticas comerciais independentes

### Problema

Durante a análise do domínio, foram identificadas restrições comerciais com critérios diferentes, como idade mínima para compra e restrições relacionadas à intolerância à lactose.

A implementação dessas regras em uma única estrutura condicional poderia concentrar responsabilidades distintas e aumentar o impacto de futuras alterações.

### Decisão

Utilizar o padrão Strategy para encapsular cada política comercial em uma implementação independente, compartilhando um contrato comum.

A modelagem considera:

- `PoliticaDeVenda`: contrato comum para avaliação.
- `PoliticaDeVendaIdadeMinima`: avaliação da restrição de idade.
- `PoliticaDeVendaIntoleranciaALactose`: avaliação da restrição relacionada à lactose.
- `AvaliadorDePoliticasDeVenda`: composição e coordenação das avaliações.

### Justificativa

As políticas possuem um objetivo comum, mas regras próprias.

A abstração permite representar essas diferenças sem concentrar todas as condições em um único componente.

O avaliador assume a responsabilidade de coordenar as políticas consideradas na operação, enquanto cada implementação se concentra em sua regra específica.

A escolha de Strategy surgiu dessa necessidade de separar comportamentos, e não da intenção prévia de aplicar um padrão de projeto.

### Consequências

A estrutura permite adicionar ou substituir implementações de políticas sem precisar reunir todas as regras comerciais em uma única classe.

Entretanto, a utilização de Strategy também introduz contratos e componentes adicionais. Essa complexidade se justifica enquanto houver a necessidade de manter políticas independentes e combináveis.

Outro ponto importante é que **a existência das políticas não garante sua execução**.

O fluxo responsável pela inclusão ou validação de itens precisa acionar o avaliador e tratar seus resultados de acordo com as regras do domínio.

Essa integração deve ser confirmada por testes que representem os comportamentos esperados.

## 5. Responsabilidades e ciclo de vida do Carrinho

### Problema

O Carrinho precisa reunir os itens selecionados e oferecer operações sobre essa coleção.

Também podem existir necessidades relacionadas ao acompanhamento de seu ciclo de vida, como identificar quando foi iniciado ou finalizado.

Entretanto, o registro dessas informações não equivale à implementação de mecanismos de expiração ou monitoramento.

### Decisão

Concentrar no `Carrinho` as responsabilidades relacionadas à seleção e ao gerenciamento dos itens.

A modelagem considera:

- Identificador do carrinho.
- Coleção de itens.
- Cálculo do valor total.
- Operações para adicionar e remover itens.
- Limpeza e finalização.
- Datas de início e finalização.

### Justificativa

O Carrinho representa a unidade responsável por organizar os itens de uma possível compra.

Manter essas operações no domínio permite concentrar comportamentos relacionados à seleção, em vez de distribuí-los entre componentes externos.

### Consequências

O Carrinho passa a representar informações e comportamentos relacionados à seleção dos produtos.

A existência de uma operação de finalização também não significa que o módulo implemente pagamentos ou criação de pedidos.

Essas responsabilidades devem permanecer separadas enquanto não forem necessárias ao escopo.

## 6. Encapsulamento e proteção do estado

### Problema

Alterações diretas nos atributos das entidades podem permitir estados inválidos e contornar regras de negócio.

### Diretriz adotada

Manter os atributos privados e permitir alterações por meio de métodos que respeitem as regras do domínio.

### Justificativa

O encapsulamento permite que cada entidade controle as modificações em seu próprio estado.

No carrinho, por exemplo, a inclusão e a remoção de itens são realizadas por métodos que verificam as condições necessárias antes de executar a operação.

### Consequências

As alterações passam pelos comportamentos disponibilizados pelas entidades, reduzindo o risco de modificações que desrespeitem suas regras.

Essa abordagem exige definir quais operações devem ser expostas publicamente, evitando métodos de alteração sem uma necessidade de negócio.

## 7. Testes como verificação de comportamento

### Contexto

A documentação de requisitos, disponível em [Documento de Requisitos](documento-de-requisitos.pdf),  define comportamentos esperados, mas sua existência não demonstra que esses comportamentos foram implementados corretamente.

É necessário estabelecer uma relação verificável entre requisitos, regras de negócio e testes.

### Decisão

Utilizar testes automatizados com PHPUnit 11 para exercitar os comportamentos do domínio.

### Justificativa

Como o projeto está concentrado nas regras de negócio, os testes podem verificar operações e restrições do domínio sem depender de uma aplicação web completa.

A intenção é que os testes ajudem a demonstrar como os comportamentos especificados foram representados na implementação.

### Consequências

Os testes constituem uma evidência verificável das regras exercitadas.

Entretanto, cobertura de código não deve ser confundida com atendimento integral dos requisitos ou ausência de defeitos.

As métricas publicadas precisam identificar o tipo de cobertura, o escopo analisado e a forma de reprodução dos resultados.

Sempre que possível, os cenários deverão manter referências aos requisitos que procuram verificar.

---

## Princípios e convenções

A organização do DomainCart considera práticas de engenharia de software que contribuem para manter o código compreensível e coerente com o domínio.

| Princípio ou prática | Aplicação no projeto |
|---|---|
| Encapsulamento | Concentração de estado, comportamentos e validações nos objetos responsáveis |
| Responsabilidade única (SRP) | Separação entre responsabilidades de Produto, Item, Carrinho e políticas comerciais |
| Aberto/fechado (OCP) | Possibilidade de introduzir novas políticas por meio de implementações do contrato existente |
| Inversão de dependência (DIP) | Utilização de um contrato para desacoplar a avaliação das implementações concretas de políticas, conforme as dependências do código |
| Composição | Coordenação de políticas independentes por um avaliador |
| DDD | Representação dos conceitos e das regras de negócio na modelagem do domínio |

Essas relações descrevem intenções e decisões de organização, não uma certificação de conformidade integral aos princípios SOLID ou ao DDD.

A avaliação de cada princípio depende das dependências, dos comportamentos e das restrições realmente implementadas.

Convenções PHP, como as recomendações PSR aplicáveis, também deverão ser identificadas a partir da configuração e da organização real do repositório.

## Tecnologias e ferramentas

O projeto utiliza as seguintes tecnologias:

| Tecnologia | Responsabilidade |
|---|---|
| PHP 8.2 | Linguagem utilizada na implementação do domínio |
| Composer | Gerenciamento de dependências e suporte ao carregamento automático de classes |
| PHPUnit 11 | Framework de testes automatizados |

As instruções de instalação e execução ficam concentradas no [README](../README.md), evitando sua duplicação neste documento.

A conteinerização com Docker poderá ser documentada quando houver uma configuração funcional incorporada ao projeto.

## Limites e evolução arquitetural

O DomainCart não implementa uma aplicação completa de comércio eletrônico.

A concentração atual na camada de domínio é uma escolha de escopo, não uma declaração de que toda a arquitetura de uma aplicação já foi definida.

Caso novas necessidades exijam persistência, interfaces externas, casos de uso ou integrações, será necessário avaliar a introdução de outras responsabilidades e estabelecer os limites entre os componentes.

Essa evolução pode aproveitar princípios de Clean Architecture, mas não precisa ser determinada antecipadamente.

O mesmo cuidado se aplica à adoção de novos padrões de projeto: sua utilização deverá responder a necessidades concretas, considerando os benefícios e os custos de manutenção.

As decisões deste documento devem evoluir junto dos requisitos, do código e dos testes.

**O objetivo não é demonstrar a quantidade de padrões ou princípios utilizados, mas tornar compreensível por que cada decisão foi necessária e quais consequências ela trouxe.**

---

## Documentação relacionada

- [Requisitos e critérios de aceitação](requisitos.md) — necessidades, comportamentos esperados e limites de escopo.
- [Modelagem do domínio](modelagem-do-dominio.md) — investigação, identificação dos conceitos e justificativas da modelagem.
- [README](../README.md) — visão geral, execução e testes do projeto.