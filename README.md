

# Sistema de Controle de Estacionamento

Este projeto implementa um sistema simples para registrar a entrada e saída de veículos e calcular o valor a ser pago conforme o tempo de permanência.  
Também foi desenvolvido com o objetivo de praticar princípios como SOLID, DRY, KISS, boas práticas de Clean Code e alguns conceitos de Object Calisthenics, utilizando PHP 8+, Composer e SQLite em uma estrutura modular organizada nas pastas `Application`, `Domain` e `Infra`.  
O sistema ainda apresenta um relatório geral contendo o total de veículos atendidos e o faturamento por tipo.

## Participantes
Vinicius da Silva Gomes RA 2010424

Guilherme Dalanora Dos Santos RA 1991839

João Pedro Pereira Guerra RA 2006484

## Requisitos

- PHP 8 ou superior  
- SQLite  
- Composer  

## Instalação

1. Acesse a pasta do projeto pelo terminal.  
2. Execute o comando:

composer install

Isso inicializa o autoload do Composer.

## Banco de Dados

O sistema utiliza um banco SQLite.  
A tabela necessária é criada a partir do arquivo `migrate.sql`, localizado em `Infra/Database/`.

Para gerar o banco:

sqlite3 parking.db < Infra/Database/migrate.sql

O arquivo `parking.db` deve permanecer na pasta `Infra/Database`.

## Execução

Para iniciar o sistema, execute no terminal:

php -S localhost:8080

Em seguida, acesse no navegador:

http://localhost:8080/index.php

## Utilização

### Entrada de veículo
- Informe a placa.  
- Selecione o tipo de veículo.  
- Confirme o registro.  

### Saída de veículo
- Informe a placa cadastrada.  
- O sistema apresenta o valor calculado automaticamente.  

### Relatório
- Exibe uma tabela com:
  - Tipo de veículo  
  - Total de registros finalizados  
  - Faturamento correspondente  

## Estrutura do Projeto

- `index.php` — Interface principal e roteamento básico.  
- `Application/` — Classes de controle e serviços de aplicação.  
- `Domain/` — Entidades, enums e interfaces de repositório.  
- `Infra/` — Implementação do repositório e arquivos de banco de dados.  
- `vendor/` — Dependências geradas pelo Composer.  

## Observações

O projeto é desenvolvido em PHP puro, com foco na organização das camadas e na separação das responsabilidades principais do sistema.
