<div class="bg-white shadow-md rounded-lg p-8 w-full mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4 text-center text-gray-800">Data MQTT</h1>

    <div id="statusIndicator" class="mb-4 text-sm text-gray-600 italic text-center">Menghubungkan...</div>



    <div class="mb-6 text-center">
        <h2 class="text-xl font-semibold mb-3 text-gray-800">Kontrol Kamera</h2>

        <button id="captureButton" onclick="sendCommand('Capture')"
            class="w-64 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Kirim Perintah Capture
        </button>

        <div id="audioEnableContainer" class="mt-4">
            <button id="enableAudioButton" class="w-64 bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Aktifkan Notifikasi Suara
            </button>
        </div>

        <div class="mt-4">
            <input type="text" id="messageInput" placeholder="Pesan custom, misal: Ping"
                class="p-2 border border-gray-300 rounded w-full mb-2">
            <button onclick="sendCustomMessage()"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                Kirim Pesan
            </button>
        </div>
    </div>

    <div id="mqttDataContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto pr-1"></div>
</div>

<audio id="alertSound" src="{{ asset('sounds/1000 Hz.mp3') }}" preload="auto"></audio>

<script src="https://cdn.jsdelivr.net/npm/mqtt@4.3.7/dist/mqtt.min.js"></script>
<script>
    const brokerUrl = "{{ env('MQTT_BROKER_URL') }}";
    const topicPubs = "{{ env('MQTT_TOPIC_PUBS') }}";
    const topicSubs = "{{ env('MQTT_TOPIC_SUBS') }}";

    const client = mqtt.connect(brokerUrl, {
        username: "{{ env('MQTT_AUTH_USERNAME') }}",
        password: "{{ env('MQTT_AUTH_PASSWORD') }}",
        reconnectPeriod: 1000,
        keepalive: 60
    });

    const mqttDataContainer = document.getElementById('mqttDataContainer');
    const statusIndicator = document.getElementById('statusIndicator');
    const alertSound = document.getElementById('alertSound');
    const enableAudioButton = document.getElementById('enableAudioButton');
    const audioEnableContainer = document.getElementById('audioEnableContainer');

    let soundTimeout;
    let audioUnlocked = false; // Flag untuk melacak status aktivasi audio

    function unlockAudio() {
        if (!audioUnlocked) {
            alertSound.play().then(() => {
                alertSound.pause();
                alertSound.currentTime = 0;
                audioUnlocked = true;
                console.log('Audio unlocked!');
                if (audioEnableContainer) {
                    audioEnableContainer.style.display = 'none';
                }
            }).catch(e => {
                console.error("Failed to unlock audio immediately:", e);
            });
        }
    }

    if (enableAudioButton) {
        enableAudioButton.addEventListener('click', unlockAudio);
    }


    client.on('connect', function () {
        console.log('Terhubung ke broker MQTT');
        statusIndicator.textContent = 'Connected to MQTT Broker';
        client.subscribe(topicSubs, function (err) {
            if (err) {
                console.error(`Gagal berlangganan ke topik: ${topicSubs}`, err);
                statusIndicator.textContent = `Error subscribing: ${err.message}`;
            } else {
                console.log(`Berlangganan ke topik: ${topicSubs}`);
            }
        });
    });

    client.on('message', function (receivedTopic, message) {
        if (receivedTopic === topicSubs) {
            try {
                const data = JSON.parse(message.toString());
                console.log('Data MQTT diterima:', data);
                displayMqttData(data);

                // Cek pesan untuk memutar suara
                if (data.message && (data.message === "Gerakan terdeteksi!" || data.message === "-")) {
                    console.log("Gerakan terdeteksi! Memutar file audio selama 5 detik.");
                    if (alertSound && audioUnlocked) { // Pastikan audio sudah di-unlock
                        // Hentikan suara jika sedang bermain
                        alertSound.pause();
                        alertSound.currentTime = 0; // Mengatur ulang audio ke awal

                        // Mulai putar suara
                        alertSound.play().catch(e => {
                            console.error("Error playing sound after unlock:", e);
                            // Jika masih ada error, mungkin ada masalah lain atau unlock gagal
                            // Anda bisa tampilkan kembali tombol unlock di sini
                            if (audioEnableContainer) {
                                audioEnableContainer.style.display = 'block';
                            }
                        });

                        // Atur timeout untuk menghentikan suara setelah 5 detik (5000 milidetik)
                        if (soundTimeout) {
                            clearTimeout(soundTimeout);
                        }
                        soundTimeout = setTimeout(() => {
                            alertSound.pause();
                            alertSound.currentTime = 0;
                            console.log("Suara dihentikan setelah 5 detik.");
                        }, 5000); // 5000 milidetik = 5 detik
                    } else if (!audioUnlocked) {
                        console.warn("Audio not unlocked yet. Please click the 'Aktifkan Notifikasi Suara' button.");
                        // Pastikan tombol unlock terlihat jika audio belum diaktifkan
                        if (audioEnableContainer) {
                            audioEnableContainer.style.display = 'block';
                        }
                    }
                }

            } catch (error) {
                console.warn('Pesan bukan JSON, hanya teks:', message.toString());
                if (message.toString() === "Gerakan terdeteksi!") {
                    console.log("Gerakan terdeteksi! Memutar file audio selama 5 detik.");
                    if (alertSound && audioUnlocked) {
                        alertSound.pause();
                        alertSound.currentTime = 0;

                        alertSound.play().catch(e => {
                            console.error("Error playing sound after unlock:", e);
                            if (audioEnableContainer) {
                                audioEnableContainer.style.display = 'block';
                            }
                        });

                        if (soundTimeout) {
                            clearTimeout(soundTimeout);
                        }
                        soundTimeout = setTimeout(() => {
                            alertSound.pause();
                            alertSound.currentTime = 0;
                            console.log("Suara dihentikan setelah 5 detik.");
                        }, 5000);
                    } else if (!audioUnlocked) {
                        console.warn("Audio not unlocked yet. Please click the 'Aktifkan Notifikasi Suara' button.");
                        if (audioEnableContainer) {
                            audioEnableContainer.style.display = 'block';
                        }
                    }
                }
            }
        }
    });

    function displayMqttData(data) {
        const dataDiv = document.createElement('div');
        dataDiv.className = 'bg-white rounded-md shadow-sm p-4 border border-gray-200';

        const idParagraph = document.createElement('p');
        idParagraph.className = 'text-gray-700 mb-2';
        idParagraph.textContent = `ID: ${data.id_device}`;

        const imageElement = document.createElement('img');
        imageElement.className = 'rounded-md mb-2 w-full h-auto';
        imageElement.src = `data:image/jpeg;base64,${data.image}`;
        imageElement.alt = `Gambar dengan ID ${data.id_device}`;

        const messageParagraph = document.createElement('p');
        messageParagraph.className = 'text-gray-800';
        messageParagraph.textContent = `Pesan: ${data.message}`;

        dataDiv.appendChild(idParagraph);
        dataDiv.appendChild(imageElement);
        dataDiv.appendChild(messageParagraph);

        mqttDataContainer.prepend(dataDiv);
    }

    function sendCommand(command) {
        client.publish(topicPubs, command, { qos: 0 }, (err) => {
            if (err) {
                alert('Gagal mengirim perintah Capture.');
                console.error(err);
            } else {
                console.log(`Perintah "${command}" berhasil dikirim.`);
            }
        });
    }

    function sendCustomMessage() {
        const input = document.getElementById('messageInput');
        const msg = input.value.trim();
        if (msg === '') {
            alert('Pesan tidak boleh kosong!');
            return;
        }
        client.publish(topicPubs, msg, { qos: 0 }, (err) => {
            if (err) {
                alert('Gagal mengirim pesan custom.');
                console.error(err);
            } else {
                console.log(`Pesan custom "${msg}" berhasil dikirim.`);
                input.value = '';
            }
        });
    }

    client.on('offline', () => statusIndicator.textContent = 'Disconnected from MQTT Broker');
    client.on('error', (error) => {
        console.error('MQTT Error:', error);
        statusIndicator.textContent = `MQTT Error: ${error.message}`;
    });
    client.on('reconnect', () => statusIndicator.textContent = 'Reconnecting to MQTT Broker...');
</script>