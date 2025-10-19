<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// Include the Composer autoloader
require 'vendor/autoload.php';

use phpseclib3\Crypt\PublicKeyLoader;

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the conversation ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: messages.php");
    exit();
}
$other_user_id = intval($_GET['id']);

// Fetch the other user's details
$stmt = $pdo->prepare("SELECT id, username, public_key, profile_picture FROM users WHERE id = ?");
$stmt->execute([$other_user_id]);
$other_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$other_user) {
    $_SESSION['error'] = "User not found.";
    header("Location: messages.php");
    exit();
}
?>


<!-- Display Messages -->
<div id="conversation">
    <!-- Messages will be loaded dynamically here -->
</div>

<!-- Message Input Form -->
<form id="messageForm" class="mt-4">
    <input type="hidden" name="receiver_id" value="<?php echo $other_user_id; ?>">
    <div class="input-group">
        <textarea name="message" id="messageInput" class="form-control" placeholder="Type your message..."></textarea>
        <button type="button" id="emojiButton" class="btn btn-secondary">
            <i class="bi bi-emoji-smile"></i>
        </button>
        <button type="button" id="recordButton" class="btn btn-secondary">
            <i class="bi bi-mic"></i>
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-send"></i>
        </button>
    </div>
</form>

<!-- Emoji Picker (Hidden by Default) -->
<div id="emojiPicker"
    style="display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; z-index: 1000;">
    <!-- Add your emoji picker here -->
    <span class="emoji">😀</span>
    <span class="emoji">😃</span>
    <span class="emoji">😄</span>
    <span class="emoji">😁</span>
    <span class="emoji">😆</span>
    <span class="emoji">😅</span>
    <span class="emoji">😂</span>
    <span class="emoji">🤣</span>
    <span class="emoji">😊</span>
    <span class="emoji">😇</span>
    <!-- Add more emojis as needed -->
</div>

<!-- Call Buttons -->
<div class="call-buttons mt-3">
    <button id="startVoiceCall" class="btn btn-success me-2">
        <i class="bi bi-telephone"></i>
    </button>
    <button id="startVideoCall" class="btn btn-primary">
        <i class="bi bi-camera-video"></i>
    </button>
</div>

<!-- Calling Screen (Hidden by Default) -->
<div id="callingScreen" style="display: none;">
    <div class="calling-screen-content">
        <h3>Calling <?php echo htmlspecialchars($other_user['username']); ?></h3>
        <div class="call-buttons">
            <button id="endCall" class="btn btn-danger">
                <i class="bi bi-telephone-x"></i>
            </button>
            <button id="toggleVideo" class="btn btn-secondary">
                <i class="bi bi-camera-video"></i>
            </button>
            <button id="toggleMute" class="btn btn-secondary">
                <i class="bi bi-mic-mute"></i>
            </button>
        </div>
        <div class="video-container">
            <video id="localVideo" autoplay muted></video>
            <video id="remoteVideo" autoplay></video>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script>
    // WebSocket connection for signaling
    const signalingSocket = new WebSocket('ws://localhost:8080');

    signalingSocket.onopen = () => {
        console.log('Connected to the signaling server');
        // Register the user with the signaling server
        signalingSocket.send(JSON.stringify({
            type: 'register',
            userId: <?php echo $_SESSION['user_id']; ?>
        }));
    };

    signalingSocket.onerror = (error) => {
        console.error('WebSocket error:', error);
    };

    signalingSocket.onclose = () => {
        console.log('WebSocket connection closed');
    };

    signalingSocket.onmessage = (event) => {
        if (event.data instanceof Blob) {
            // Handle binary data (e.g., audio or video streams)
            const blob = event.data;
            console.log('Received binary data:', blob);

            // Example: Create a URL for the Blob and use it in an <audio> or <video> element
            const url = URL.createObjectURL(blob);
            const audioElement = new Audio(url);
            audioElement.play();
        } else {
            // Handle text data (e.g., JSON messages)
            try {
                const signal = JSON.parse(event.data);
                console.log('Received signal:', signal);
                handleSignal(signal); // Handle incoming signals
            } catch (error) {
                console.error('Error parsing WebSocket message:', error);
            }
        }
    };

    // Function to send signals
    function sendSignal(signal) {
        signal.targetUserId = <?php echo $other_user_id; ?>;
        signalingSocket.send(JSON.stringify(signal));
    }

    // Load messages dynamically
    function loadMessages() {
        $.ajax({
            url: 'fetch_messages.php',
            method: 'GET',
            data: { receiver_id: <?php echo $other_user_id; ?> },
            success: function (response) {
                $('#conversation').html(response);
                // Scroll to the bottom of the conversation
                $('#conversation').scrollTop($('#conversation')[0].scrollHeight);
            },
            error: function (xhr, status, error) {
                console.error('Error loading messages:', error);
            }
        });
    }

    // Load messages every 2 seconds
    setInterval(loadMessages, 10000);

    // Prevent form submission and send message via AJAX
    $('#messageForm').on('submit', function (e) {
        e.preventDefault(); // Prevent page reload

        const message = $('#messageInput').val().trim();
        if (!message) return; // Do not send empty messages

        const receiverId = $('input[name="receiver_id"]').val();

        // Send message via AJAX
        $.ajax({
            url: 'send_message.php',
            method: 'POST',
            data: { receiver_id: receiverId, message: message },
            success: function (response) {
                console.log('Message sent:', response);
                $('#messageInput').val(''); // Clear the input field
                loadMessages(); // Reload messages
            },
            error: function (xhr, status, error) {
                console.error('Error sending message:', error);
            }
        });
    });

    // Toggle emoji picker
    $('#emojiButton').on('click', function () {
        $('#emojiPicker').toggle();
    });

    // Insert emoji into the message input
    $('.emoji').on('click', function () {
        const emoji = $(this).text();
        const messageInput = $('#messageInput');
        messageInput.val(messageInput.val() + emoji);
    });

    // WebRTC variables and functions (same as before)
    let localStream;
    let remoteStream;
    let peerConnection;
    const configuration = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' } // Google's public STUN server
        ]
    };

    // Start a call (voice or video)
    async function startCall(video = true) {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ video, audio: true });
            document.getElementById('localVideo').srcObject = localStream;

            // Show the calling screen
            document.getElementById('callingScreen').style.display = 'block';

            peerConnection = new RTCPeerConnection(configuration);
            localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

            peerConnection.ontrack = (event) => {
                remoteStream = event.streams[0];
                document.getElementById('remoteVideo').srcObject = remoteStream;
            };

            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    sendSignal({ type: 'candidate', candidate: event.candidate });
                }
            };

            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            sendSignal({ type: 'offer', offer });
        } catch (error) {
            console.error('Error starting call:', error);
            alert('Failed to start call. Please ensure your camera and microphone are accessible.');
        }
    }

    // Handle incoming signals (offers, answers, candidates)
    async function handleSignal(signal) {
        if (!peerConnection) {
            peerConnection = new RTCPeerConnection(configuration);

            if (localStream) {
                localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
            }

            peerConnection.ontrack = (event) => {
                remoteStream = event.streams[0];
                document.getElementById('remoteVideo').srcObject = remoteStream;
            };

            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    sendSignal({ type: 'candidate', candidate: event.candidate });
                }
            };
        }

        if (signal.type === 'offer') {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(signal.offer));
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);
            sendSignal({ type: 'answer', answer });
        } else if (signal.type === 'answer') {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(signal.answer));
        } else if (signal.type === 'candidate') {
            await peerConnection.addIceCandidate(new RTCIceCandidate(signal.candidate));
        }
    }

    // End the call
    function endCall() {
        if (peerConnection) {
            peerConnection.close();
            peerConnection = null;
        }
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
        }
        if (remoteStream) {
            remoteStream.getTracks().forEach(track => track.stop());
            remoteStream = null;
        }
        document.getElementById('callingScreen').style.display = 'none';
    }

    // Toggle video on/off
    function toggleVideo() {
        const videoTrack = localStream.getVideoTracks()[0];
        if (videoTrack) {
            videoTrack.enabled = !videoTrack.enabled;
            document.getElementById('toggleVideo').innerHTML = videoTrack.enabled ?
                '<i class="bi bi-camera-video"></i> Toggle Video' :
                '<i class="bi bi-camera-video-off"></i> Toggle Video';
        }
    }

    // Toggle mute on/off
    function toggleMute() {
        const audioTrack = localStream.getAudioTracks()[0];
        if (audioTrack) {
            audioTrack.enabled = !audioTrack.enabled;
            document.getElementById('toggleMute').innerHTML = audioTrack.enabled ?
                '<i class="bi bi-mic"></i> Toggle Mute' :
                '<i class="bi bi-mic-mute"></i> Toggle Mute';
        }
    }

    // Event listeners for call buttons
    document.getElementById('startVoiceCall').addEventListener('click', () => startCall(false));
    document.getElementById('startVideoCall').addEventListener('click', () => startCall(true));
    document.getElementById('endCall').addEventListener('click', endCall);
    document.getElementById('toggleVideo').addEventListener('click', toggleVideo);
    document.getElementById('toggleMute').addEventListener('click', toggleMute);
</script>