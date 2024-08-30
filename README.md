# Envio de Emails com Laravel

Este projeto permite enviar emails através de uma API RESTful construída com Laravel. Siga as instruções abaixo para enviar emails usando a aplicação.

## Requisitos

- PHP >= 7.4
- Laravel >= 8.x
- Servidor web configurado com o projeto Laravel
- Cliente HTTP (como Postman, Insomnia ou cURL)

## Enviar Email

### Passo 1: Obter Token CSRF

Antes de enviar um email, você precisa obter um token CSRF para garantir a segurança da requisição.

1. Envie uma requisição `GET` para:

   ```http
   GET http://127.0.0.1:8000/csrf-token
   ```

2. A resposta será um token CSRF, por exemplo:

   ```json
   "5taQ7bcYbR3Jqs6nQZhXOsNEcaGrrEmvg24avYIb"
   ```

### Passo 2: Enviar Requisição de Email

Com o token CSRF em mãos, você pode enviar a requisição para enviar um email.

1. Envie uma requisição `POST` para:

   ```http
   POST http://127.0.0.1:8000/mailo/enviaremail
   ```

2. Adicione um cabeçalho (`Header`) com o token CSRF:

   - **Chave (Key)**: `X-CSRF-TOKEN`
   - **Valor (Value)**: `5taQ7bcYbR3Jqs6nQZhXOsNEcaGrrEmvg24avYIb` (o token CSRF obtido no Passo 1)

3. No corpo da requisição (`Body`), envie um JSON com o seguinte formato:

   ```json
   {
     "to": "bravo18br@gmail.com",
     "subject": "Assunto do Email",
     "body": "Conteúdo do email.",
     "from_nome": "Nome do APP",
     "from_email": "email_do_app@mailo.com"
   }
   ```

### Exemplo de Requisição com cURL

Aqui está um exemplo de como enviar a requisição usando cURL:

```bash
curl -X POST http://127.0.0.1:8000/mailo/enviaremail \
-H "X-CSRF-TOKEN: 5taQ7bcYbR3Jqs6nQZhXOsNEcaGrrEmvg24avYIb" \
-H "Content-Type: application/json" \
-d '{
  "to": "bravo18br@gmail.com",
  "subject": "Assunto do Email",
  "body": "Conteúdo do email.",
  "from_nome": "Nome do APP",
  "from_email": "email_do_app@mailo.com"
}'
```

## Erros Comuns

- **The from field is required**: Certifique-se de que o campo `from_nome` e `from_email` estão incluídos corretamente no JSON enviado.
- **CSRF Token Mismatch**: Verifique se o token CSRF está correto e se foi incluído no cabeçalho da requisição.

## Configuração do Ambiente

- Certifique-se de que o arquivo `.env` está corretamente configurado para o envio de emails, especialmente os campos `MAIL_FROM_ADDRESS` e `MAIL_FROM_NAME`.

```env
MAIL_FROM_ADDRESS=default@example.com
MAIL_FROM_NAME="Default Name"
```

## Funcionalidade de Monitoramento de Emails e Disparo de Webhook

Este projeto inclui uma funcionalidade para monitorar a caixa de entrada de emails e disparar webhooks automaticamente com base em regras definidas.

### Descrição Geral

A aplicação verifica periodicamente a caixa de emails configurada, buscando por mensagens com subjects específicos. Quando uma mensagem correspondente é encontrada, a aplicação dispara uma requisição HTTP para um endpoint configurado (webhook). Após processar a mensagem, ela é excluída do servidor de emails.

### Funcionalidade Implementada

1. **Busca de Mensagem na Caixa de Entrada**
   - O método `buscaSubject()` conecta-se ao servidor de emails, percorre todas as pastas e busca pela primeira mensagem disponível.
   - Quando uma mensagem é encontrada, seu subject é recuperado e a mensagem é excluída.
   - Logs são gerados para registrar o sucesso ou falha em encontrar e/ou excluir a mensagem.

2. **Execução de Tarefas com Disparo de Webhook**
   - O método `executaTarefas()` chama o método `buscaSubject()` para buscar a próxima mensagem.
   - Se uma mensagem for encontrada e corresponder a uma regra ativada no sistema (com o mesmo subject), uma requisição HTTP GET é enviada ao webhook configurado na regra.
   - A verificação SSL é desabilitada para o ambiente de desenvolvimento usando `Http::withoutVerifying()->get(...)`.
   - Logs são criados para registrar o sucesso ou falha ao disparar o webhook e para informar se nenhuma regra ativa foi encontrada para o subject correspondente.

### Exemplo de Uso

Para usar essa funcionalidade, configure sua aplicação com as seguintes etapas:

1. **Definição das Regras**
   - As regras devem ser definidas no banco de dados da aplicação, incluindo o campo `subject` para identificar o assunto do email e `webhook` para especificar o endpoint que será chamado.

2. **Configuração de Email**
   - Configure sua aplicação com as credenciais de email necessárias para se conectar ao servidor de emails via IMAP.
   - Verifique as configurações SSL, especialmente em ambientes de desenvolvimento, onde pode ser necessário desabilitar a verificação SSL.

### Requisitos

- **GuzzleHTTP**: Para enviar requisições HTTP ao webhook.
- **Extensão IMAP**: Para conectividade com a caixa de emails.

### Exemplo de Código

**Buscar o Subject de uma Mensagem:**

```php
public function buscaSubject()
{
    // Lógica para buscar a mensagem e retornar seu subject
}
```

**Executar Tarefas com Disparo de Webhook:**

```php
public function executaTarefas()
{
    // Lógica para executar tarefas com base nas regras configuradas
}
```

### Logs

Os logs gerados durante o processo de monitoramento e execução são armazenados no banco de dados para auditoria e depuração. Os seguintes eventos são registrados:

- Mensagem encontrada e excluída.
- Erro ao excluir a mensagem.
- Nenhuma mensagem encontrada.
- Sucesso ou erro ao chamar o webhook.

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.

## Licença

Este projeto é licenciado sob a licença MIT.
