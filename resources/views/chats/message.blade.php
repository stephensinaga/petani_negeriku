<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <style>
        #chat-messages {
            height: 300px;
            overflow-y: scroll;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }
        .text-start {
            text-align: left;
        }
        .text-end {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
        }
        .bg-primary {
            background-color: #007bff;
        }
        .bg-secondary {
            background-color: #6c757d;
        }
    </style>
</head>
<body>
    <div>
        <h1>Chat</h1>
        <!-- Daftar Pesan -->
        <div id="chat-messages">
            <!-- Pesan akan ditampilkan di sini -->
        </div>

        <!-- Form Kirim Pesan -->
        <form id="chat-form">
            <textarea id="message" placeholder="Ketik pesan..." style="width: 100%; height: 50px;"></textarea>
            <button type="submit" style="margin-top: 10px;">Kirim</button>
        </form>
    </div>

    <script>
        const receiverId = {{ $receiverId }}; // ID penerima chat
        const chatMessages = document.getElementById('chat-messages');

        // Ambil Pesan Lama
        axios.get(`/messages/${receiverId}`)
            .then(response => {
                response.data.forEach(message => {
                    addMessageToUI(message, message.sender_id === {{ auth()->id() }});
                });
            })
            .catch(error => console.error('Error fetching messages:', error));

        // Kirim Pesan
        document.getElementById('chat-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const message = document.getElementById('message').value;

            axios.post('/messages', {
                receiver_id: receiverId,
                message: message
            }, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                addMessageToUI(response.data.chat, true);
                document.getElementById('message').value = ''; // Kosongkan input pesan
            })
            .catch(error => console.error('Error sending message:', error));
        });

        // Tambahkan Pesan ke UI
        function addMessageToUI(message, isSender) {
            const div = document.createElement('div');
            div.className = isSender ? 'text-end mb-2' : 'text-start mb-2';
            div.innerHTML = `
                <span class="badge ${isSender ? 'bg-primary' : 'bg-secondary'}">
                    ${message.message}
                </span>
            `;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll ke bawah
        }

        // Real-Time Listener menggunakan Pusher
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            encrypted: true
        });

        const channel = pusher.subscribe(`private-chat.${receiverId}`);
        channel.bind('MessageSent', function (data) {
            addMessageToUI(data.chat, false);
        });
    </script>
</body>
</html>
