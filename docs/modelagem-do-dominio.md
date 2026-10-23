# Modelagem do domínio

[← Voltar ao README](../README.md)

## Da necessidade à modelagem

No documento de [Requisitos e critérios de aceitação](requisitos.md), partimos de uma solicitação aparentemente simples:

> Preciso de uma loja virtual com carrinho de compras.

Vimos que essa necessidade pode provocar diversas perguntas antes de qualquer decisão técnica.

Agora, quero aprofundar um pouco mais esse raciocínio e mostrar como podemos identificar os conceitos e as responsabilidades que farão parte da solução.

A ideia não é começar escolhendo classes, interfaces ou padrões de projeto.

Quero primeiro observar situações do mundo real, compreender os elementos envolvidos e descobrir quais características e comportamentos fazem sentido para o domínio que estamos modelando.

É importante lembrar que **não estamos tentando reproduzir toda a realidade dentro do software**. Estamos representando apenas aquilo que é relevante para o problema.

## Identificando as entidades e responsabilidades

### Cliente

Vamos começar pelo Cliente.

Na vida real, encontramos esse conceito em diversos contextos. Somos clientes quando utilizamos serviços bancários, contratamos uma empresa ou entramos em uma loja para realizar uma compra.

Dependendo do contexto, um cliente pode possuir informações como nome, e-mail, data de nascimento, endereço e muitas outras.

Mas será que precisamos de todas elas?

No DomainCart, consideramos as seguintes características:

- Nome.
- E-mail.
- Data de nascimento.
- Indicação de intolerância à lactose.

A data de nascimento permite determinar a idade do cliente, informação necessária para avaliar determinadas restrições comerciais.

Já a intolerância à lactose foi incluída para explorar outro tipo de política de venda.

Vale observar que representar a intolerância por meio de um valor booleano é uma simplificação adotada para este domínio. Na realidade, a condição pode envolver diferentes níveis de tolerância e outras particularidades.

Outra pergunta importante surge durante a modelagem:

**Ter as informações de um cliente significa necessariamente que ele possui uma conta cadastrada ou está autenticado?**

Não.

Podemos precisar das informações de uma pessoa para avaliar uma operação sem que isso implique a existência de um módulo de autenticação.

Essa distinção ajuda a manter o foco nas responsabilidades que realmente pertencem ao domínio do carrinho.

### Produto

Agora vamos pensar no Produto.

Encontramos produtos em supermercados, lojas de eletrônicos e diversos outros estabelecimentos comerciais.

Em uma loja, um produto normalmente possui características como nome, descrição e preço de venda.

Mas precisamos considerar o contexto.

Um produto não possui necessariamente uma quantidade em estoque como característica intrínseca. Entretanto, quando estamos tratando de uma operação comercial, precisamos saber se existem unidades disponíveis para venda.

No DomainCart, consideramos:

- Nome.
- Descrição.
- Quantidade em estoque.
- Valor de venda.
- Idade mínima para compra.
- Indicação de presença de lactose.

As duas últimas características permitem avaliar determinadas restrições comerciais, que serão discutidas mais adiante.

Durante essa análise, podemos identificar outra necessidade: alguém precisará cadastrar os produtos, atualizar suas informações e manter os valores de venda.

Isso significa que precisamos desenvolver um módulo administrativo?

Não necessariamente.

Reconhecer uma responsabilidade não significa que precisamos implementá-la imediatamente.

Neste momento, estamos concentrados no domínio do carrinho de compras. A administração do catálogo de produtos pode ser tratada separadamente.

### Carrinho

O conceito de Carrinho é bastante intuitivo.

Imagine que você entra em um supermercado, pega um carrinho e começa a percorrer os corredores.

Você seleciona produtos, coloca alguns dentro do carrinho e eventualmente devolve outros às prateleiras.

Ao terminar, dirige-se ao caixa para concluir a compra.

Essa situação nos ajuda a identificar alguns comportamentos:

- Adicionar produtos.
- Remover produtos.
- Consultar os produtos selecionados.
- Calcular o valor total.
- Finalizar o carrinho.

Mas, ao transportar esse conceito para um sistema digital, precisamos considerar outras informações.

Por exemplo, como identificar um carrinho específico?

Podemos utilizar um identificador.

E se precisarmos saber quando o carrinho foi iniciado ou finalizado?

Podemos representar essas informações por meio de datas.

Assim, o modelo considerado para o DomainCart contempla:

- Identificador do carrinho.
- Coleção de itens.
- Valor total calculado.
- Data de início.
- Data de finalização.
- Operações relacionadas ao gerenciamento dos itens e à finalização.

Observe que começamos com uma situação simples do mundo real e, conforme aprofundamos a análise, novas informações e responsabilidades aparecem.

Entretanto, existe um detalhe importante.

**Registrar a data de início e a data de finalização não significa que implementamos automaticamente um mecanismo de abandono ou expiração de carrinhos.**

Essas informações podem apoiar necessidades futuras, mas identificar um carrinho abandonado exige definir o que caracteriza o abandono.

Da mesma maneira, possuir uma operação de finalização não significa que o módulo seja responsável pelo processamento de pagamentos.

As responsabilidades precisam respeitar o escopo definido.

### Item

Agora vamos voltar ao supermercado.

Imagine que colocamos três caixas de leite dentro do carrinho.

Quando chegamos ao caixa, a operadora registra os produtos para calcular o valor da compra.

No contexto do sistema, precisamos decidir como representar essas três unidades.

Poderíamos armazenar três ocorrências independentes do mesmo produto, mas também podemos representar a seleção por meio de um único Item com quantidade igual a três.

Essa segunda representação permite distinguir dois conceitos:

- **Produto:** aquilo que está disponível para comercialização.
- **Item:** a seleção de determinado produto dentro de um carrinho, considerando uma quantidade e um valor unitário.

No DomainCart, o Item considera:

- Referência ao Produto.
- Quantidade selecionada.
- Valor unitário.
- Valor total calculado.

Também podemos identificar comportamentos relacionados ao aumento e à redução da quantidade.

Essa separação nos leva a outra decisão importante.

#### Por que o Item possui um valor unitário próprio?

Imagine que um produto custe R$ 10,00 no momento em que o cliente o adiciona ao carrinho.

Posteriormente, o estabelecimento altera o preço de venda para R$ 12,00.

Qual valor deverá ser considerado para o produto que já está no carrinho?

Essa resposta depende das regras de negócio.

O estabelecimento pode decidir preservar o preço por determinado período, atualizar o valor antes da finalização ou estabelecer outras condições.

Ao manter o valor unitário no Item, conseguimos representar o preço considerado no momento da seleção, sem depender exclusivamente do preço atual do Produto.

Isso não significa que o preço permanecerá válido indefinidamente.

**A separação entre Produto e Item permite representar diferentes políticas de preço, mas não define sozinha qual delas deve ser aplicada.**

Essa regra precisa ser especificada pelo negócio.

Perceba como uma decisão de modelagem pode surgir a partir de uma pergunta sobre um comportamento esperado, e não simplesmente de uma preferência por determinada estrutura de código.

## Políticas de venda

Até aqui identificamos Cliente, Produto, Carrinho e Item.

Mas vamos imaginar outras situações do mundo real.

Em determinados estabelecimentos, alguns produtos possuem restrições de comercialização.

Uma bebida alcoólica, por exemplo, pode exigir que o comprador tenha uma idade mínima.

Em outro contexto, podemos considerar restrições relacionadas a características de um produto e condições informadas pelo cliente.

No DomainCart, utilizamos dois exemplos de políticas comerciais:

- Restrição por idade mínima.
- Restrição relacionada à intolerância à lactose.

São regras diferentes, com critérios próprios.

E aqui surge uma questão interessante:

**Como representar diferentes políticas comerciais sem concentrar todas as condições em uma única implementação?**

Poderíamos criar uma estrutura contendo diversas condições, verificando cada tipo de restrição.

Entretanto, conforme novas políticas fossem identificadas, essa estrutura tenderia a acumular responsabilidades e exigir alterações frequentes.

Uma alternativa é representar cada política de forma independente, mantendo um contrato comum de avaliação.

Foi essa necessidade que motivou a utilização do padrão Strategy.

### Quando o padrão surge do problema

No DomainCart, a interface `PoliticaDeVenda` estabelece o contrato utilizado pelas diferentes políticas.

As classes `PoliticaDeIdadeMinima` e `PoliticaDeIntoleranciaALactose` representam avaliações específicas.

Já o `AvaliadorDePoliticasDeVenda` possui a responsabilidade de combinar essas avaliações.

Essa organização permite separar regras comerciais diferentes e trabalhar com suas implementações sem concentrar todas as condições em um único componente.

É importante observar que `PoliticaDeVenda` não precisa ser tratada como uma entidade. Nesse modelo, ela representa uma abstração de comportamento do domínio.

O mais interessante dessa decisão é perceber como chegamos até ela.

Não começamos dizendo que precisaríamos utilizar Strategy.

Primeiro identificamos as políticas comerciais. Depois percebemos que elas possuíam comportamentos independentes, mas precisavam ser avaliadas por meio de um contrato comum.

Foi essa necessidade que justificou o padrão.

**Não precisamos forçar a utilização de padrões de projeto. Eles podem surgir naturalmente quando compreendemos o problema e identificamos uma necessidade real de organização.**

## Responsabilidades e limites da modelagem

Ao longo dessa investigação, identificamos conceitos, atributos, comportamentos e possíveis regras de negócio.

Entretanto, a modelagem não deve ser considerada definitiva.

Novos requisitos podem revelar responsabilidades que ainda não conhecemos. Outras decisões podem precisar ser revisadas conforme o comportamento esperado se torna mais claro.

Também precisamos distinguir o que foi representado no modelo daquilo que foi efetivamente implementado.

Por exemplo:

- A existência de um Cliente não exige um módulo de autenticação.
- A existência de um Produto não exige um sistema administrativo completo.
- A presença de datas no Carrinho não implementa automaticamente mecanismos de expiração.
- O valor unitário armazenado no Item não determina sozinho uma política de validade de preços.
- A existência de políticas comerciais não significa que todas as possíveis restrições de venda foram contempladas.

Essas distinções ajudam a evitar que o modelo assuma responsabilidades que não foram especificadas.

## Da modelagem à implementação

A modelagem do DomainCart procura estabelecer uma relação coerente entre aquilo que foi investigado e aquilo que será representado no código.

Cada entidade deve possuir responsabilidades compatíveis com o domínio. Cada regra precisa ter um comportamento compreensível e, quando possível, verificável por meio de testes automatizados.

Isso não significa que todas as decisões tomadas inicialmente serão mantidas.

Durante a implementação, podemos descobrir novas necessidades, identificar inconsistências ou perceber que determinada responsabilidade foi atribuída ao componente errado.

Essas descobertas fazem parte do desenvolvimento de software e podem exigir que retornemos aos requisitos e à modelagem.

O importante é preservar a coerência entre o problema, as decisões e o comportamento implementado.

**A intenção não é construir classes para depois encontrar uma justificativa para elas. É compreender o problema e permitir que as responsabilidades e as soluções técnicas surjam das necessidades identificadas.**

As justificativas mais específicas sobre a organização do código, os Value Objects e a aplicação dos padrões utilizados podem ser consultadas em [Decisões técnicas](decisoes-tecnicas.md).