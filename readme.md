# Documentação do Sistema de Tradução Avatar Falante
> [!NOTE]
> ## Visão Geral
> O sistema consiste em uma aplicação de tradução de voz com interface web que interage com um backend PHP. 
> O frontend captura áudio, o backend realiza traduções utilizando APIs de terceiros e retorna o texto traduzido, que é então falado pelo avatar na interface.
[!IMPORTANT]
> >vídeo exemplo:
> https://vimeo.com/930678738/b237078579?share=copy
# Frontend
> [!TIP]
> ## Estrutura HTML
> O arquivo index.html define a estrutura da página web. Contém botões para iniciar a captura de áudio, selecionar o idioma e exibir o avatar falante.

## JavaScript
> [!IMPORTANT]
> ### Classe AvatarTradutor:
> Responsável pela captura de áudio, seleção de idioma, comunicação com o backend para tradução e animação do avatar.
> Usa a Web Speech API para reconhecimento de voz e síntese de fala.
> Interage com o backend para obter traduções.
> ## Funcionalidades
> Captura de áudio e exibição de transcrição.
> Seleção de idioma de tradução e serviço de tradução.
> Comunicação com o backend para obter traduções e armazenar em cache resultados para otimizar buscas iguais no mesmo idioma destino.
# Backend (PHP)
> [!TIP]
> ## Classes e Padrões de Design
> CacheHandler:
> 
> Gerencia o cache de traduções para evitar chamadas repetidas às APIs.
> - Padrão: Repositório.
> CurlHandler:
> 
> Realiza as chamadas cURL para as APIs de tradução.
> - Padrão: Fachada.
> GoogleTranslator e ChatGPT:
> 
> Realizam a tradução utilizando APIs específicas.
> - Padrão: Estratégia.
> TranslationRouter:
> 
> Encaminha a solicitação de tradução para a API apropriada.
> - Padrão: Fachada.

> [!IMPORTANT]
> ## Funcionalidades
> Tradução de texto usando Google Translate ou GPT-3.
> Armazenamento de traduções em cache para melhor desempenho.
> Flexibilidade para alterar a fonte da tradução.
> Arquivo de Entrada (backend/index.php)
> Ponto de entrada para solicitações de tradução do frontend.
> Processa solicitações POST, extrai dados e utiliza TranslationRouter para obter traduções.
> [!TIP]
> ## Como Executar o Sistema
> Carregue o index.html em um navegador para acessar a interface do usuário.
> Fale no microfone para capturar áudio e obter a tradução.
> A tradução será falada pelo avatar na interface.
> [!IMPORTANT]
> ## Considerações Adicionais
> O sistema requer um servidor PHP >8 para o backend.
> O sistema requer composer e psr-4.
> As APIs de tradução podem necessitar de chaves de API configuradas no config.php.
> ## criar arquivo backend\config\config.php
> adicionar a chave de api
```
<?php
  define('API', 'AIza********************'); //google tradutor
  define('OPENAI_API_KEY', 'sk-*************************');//api openai
```
Instalar com o composer e no terminal dentro da raiz do projeto
```
composer install
```
