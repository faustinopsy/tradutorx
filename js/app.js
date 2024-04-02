class AvatarCompleto {
    constructor(transcriptionId,translationId) {
        this.DURACAO = 300;
        this.linguaDestino = "en";
         // "en" "ja-JP" "it-IT" "fr-FR" "es-ES"  "en-GB" "zh-HK" "zh-CN"
        this.interval = null;
        this.api='backend/index.php';
        this.useGPT= false;
        this.voices = [];
        this.recebe_audio = null;
        this.transcricao_audio = '';
        this.esta_gravando = false;
        this.transcriptionElem = document.getElementById(transcriptionId);
        this.translationElem = document.getElementById(translationId);
        window.speechSynthesis.onvoiceschanged = this.loadVoices.bind(this);
        this.setupAudio();
    }

    async loadVoices() {
        this.voices = window.speechSynthesis.getVoices();
        const voiceSelector = document.getElementById('vozSelector');
        this.voices.forEach(voice => {
            let option = document.createElement('option');
            option.value = voice.name;
            option.textContent = `${voice.name} (${voice.lang})`;
            voiceSelector.appendChild(option);
        });
        const destino = document.getElementById('destino');
            let gpt = document.createElement('option');
            let google = document.createElement('option');
            gpt.value = 1;
            gpt.textContent = `GPT`;
            google.value = 2;
            google.textContent = `Google`;
            destino.appendChild(google);
            destino.appendChild(gpt);
            
            destino.addEventListener('change', ()=> this.selectDestino(destino.value));
        const videoElement = document.getElementById('video');
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
                videoElement.srcObject = stream;
            } catch (error) {
                console.error("Erro ao acessar a câmera:", error);
            }
        }
    }
    selectDestino(valor) {
        this.useGPT = valor==1 ? true : false; 

    }
    setupAudio() {
        if (window.SpeechRecognition || window.webkitSpeechRecognition) {
            const speech_api = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recebe_audio = new speech_api();
            this.recebe_audio.continuous = true;
            this.recebe_audio.interimResults = true;
            this.recebe_audio.lang = "pt-BR";
            this.recebe_audio.onresult = this.handleResult.bind(this);
            this.recebe_audio.onend = () => {
                if (this.esta_gravando) {
                    this.recebe_audio.start();
                }
            };
        } else {
            console.log('Navegador não suporta a Web Speech API');
        }
    }

    handleResult(event) {
        for (let i = event.resultIndex; i < event.results.length; i++) {
            if (event.results[i].isFinal) {
                this.transcricao_audio = event.results[i][0].transcript;
                this.transcriptionElem.innerHTML = this.transcricao_audio;
                this.translateText(this.transcricao_audio, this.linguaDestino);
            }
        }
    }
    onEndSpeaking(event) {
        clearInterval(this.interval);
    }
    toggleRecording() {
        if (this.esta_gravando) {
            this.recebe_audio.stop();
            this.esta_gravando = false;
        } else {
            this.recebe_audio.start();
            this.esta_gravando = true;
        }
    }

    translateText(texto, langdestino) {
        const data = {
            text: texto,
            langDestino: langdestino,
            source: 'pt',
            useGPT:this.useGPT
        };
       
        fetch(`${this.api}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            let chatbotResponse;
            if(this.useGPT){
                chatbotResponse = data.response;
                document.getElementById("custo").textContent = `Custo: Prompt-${data.prompt_tokens} Total: ${data.total_token}`
            }else{
                chatbotResponse = data.response.data.translations[0].translatedText;
            }
        this.translationElem.innerHTML = chatbotResponse;
        this.speak(chatbotResponse);
})
.catch(error => console.error('Erro na comunicação com o chatbot:', error));
    }

    speak(text) {
        const selectedVoiceName = document.getElementById('vozSelector').value;
        const msg = new SpeechSynthesisUtterance(text);
        const selectedVoice = this.voices.find(voice => voice.name == selectedVoiceName);
        msg.voice = selectedVoice;
        msg.lang = this.linguaDestino;
        msg.pitch = 0.9;
        msg.rate = 0.9;
        msg.onend = this.onEndSpeaking.bind(this);
        window.speechSynthesis.speak(msg);
       
    }

   

    
}

const avatarCompleto = new AvatarCompleto('transcription', 'translatedText');
function selectLanguage(buttonElem) {
    const selectedLang = buttonElem.getAttribute('data-lang');
    const buttons = document.querySelectorAll('.langButton');
    buttons.forEach(btn => {
        btn.classList.remove('selecao');
    });
    buttonElem.classList.add("selecao"); 
    avatarCompleto.linguaDestino = selectedLang;
}

document.getElementById('falar').addEventListener('click', function() {
        const statusElem = document.getElementById('statusGravacao');
        if (statusElem.style.display === 'none') {
            statusElem.style.display = 'block';
            this.style.backgroundColor = 'red';
            this.textContent = '⏹ Parar Gravação';
        } else {
            statusElem.style.display = 'none';
            this.textContent = '🎙 Iniciar Gravação';
            this.style.backgroundColor = 'chartreuse';
        }
    });
document.getElementById('falar').addEventListener('click', () => avatarCompleto.toggleRecording());
