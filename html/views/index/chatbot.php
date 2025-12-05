<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AI Travel Chat Bubble - Database</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    .chat-bubble-wrapper {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 9999;
    }

    .chat-bubble-btn {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      background: linear-gradient(135deg, #20ab7aff 0%, #15a04fff 100%);
      border: none;
      cursor: pointer;
      box-shadow: 0 8px 24px rgba(18, 183, 37, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); box-shadow: 0 8px 24px rgba(18, 183, 37, 0.4); }
      50% { transform: scale(1.05); box-shadow: 0 12px 32px rgba(18, 183, 37, 0.6); }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px) rotate(-5deg); }
      75% { transform: translateX(5px) rotate(5deg); }
    }

    .chat-bubble-btn:hover {
      transform: scale(1.1) rotate(5deg);
      box-shadow: 0 12px 32px rgba(18, 154, 99, 0.5);
      animation: shake 0.5s ease;
    }

    .chat-bubble-btn.active { animation: none; }

    .chat-icon, .close-icon {
      width: 35px;
      height: 32px;
      color: white;
      transition: all 0.3s;
    }

    .close-icon { display: none; }
    .chat-bubble-btn.active .chat-icon { display: none; }
    .chat-bubble-btn.active .close-icon { display: block; }

    .notification-badge {
      position: absolute;
      top: -4px;
      right: -4px;
      background: linear-gradient(135deg, #ff4757, #ff6b81);
      color: white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: bold;
      border: 3px solid white;
      animation: bounce 0.5s ease infinite alternate, pulse-ring 2s infinite;
    }

    @keyframes bounce {
      from { transform: translateY(0); }
      to { transform: translateY(-4px); }
    }

    @keyframes pulse-ring {
      0% { box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7); }
      100% { box-shadow: 0 0 0 10px rgba(255, 71, 87, 0); }
    }

    .notification-badge.hidden { display: none; }

    .chat-window {
      position: absolute;
      bottom: 84px;
      right: 0;
      width: 400px;
      max-width: calc(100vw - 48px);
      height: 600px;
      max-height: calc(100vh - 140px);
      background: white;
      border-radius: 20px;
      box-shadow: 0 12px 48px rgba(0, 0, 0, 0.2);
      display: none;
      flex-direction: column;
      overflow: hidden;
      animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .chat-window.active { display: flex; }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .chat-header {
      background: linear-gradient(135deg, #29a842ff 0%, #319348ff 100%);
      color: white;
      padding: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .header-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .header-avatar {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: rgba(73, 176, 61, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      backdrop-filter: blur(10px);
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-5px); }
    }

    .header-text h3 {
      margin: 0;
      font-size: 16px;
      font-weight: 600;
    }

    .header-text p {
      margin: 2px 0 0;
      font-size: 12px;
      opacity: 0.9;
    }

    .header-actions {
      display: flex;
      gap: 8px;
    }

    .header-btn {
      background: rgba(27, 196, 86, 0.2);
      border: none;
      color: white;
      width: 32px;
      height: 32px;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      backdrop-filter: blur(10px);
    }

    .header-btn:hover { background: rgba(255, 255, 255, 0.3); }
    .header-btn svg { width: 18px; height: 18px; }

    .messages-area {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      background: #f8f9fa;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .messages-area::-webkit-scrollbar { width: 6px; }
    .messages-area::-webkit-scrollbar-track { background: transparent; }
    .messages-area::-webkit-scrollbar-thumb { 
      background: #cbd5e0; 
      border-radius: 3px; 
    }

    .message {
      display: flex;
      gap: 8px;
      animation: messageIn 0.3s ease-out;
    }

    @keyframes messageIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .message.user { flex-direction: row-reverse; }

    .msg-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 16px;
    }

    .message.bot .msg-avatar { 
      background: linear-gradient(135deg, #0fb96aff, #12c5b3ff); 
    }

    .message.user .msg-avatar { 
      background: linear-gradient(135deg, #219e6cff, #10b66eff); 
    }

    .msg-bubble {
      max-width: 75%;
      padding: 10px 14px;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      position: relative;
    }

    .message.bot .msg-bubble {
      background: white;
      color: #2d3748;
      border-bottom-left-radius: 4px;
    }

    .message.user .msg-bubble {
      background: linear-gradient(135deg, #38b15cff, #359662ff);
      color: white;
      border-bottom-right-radius: 4px;
    }

    .msg-text {
      font-size: 14px;
      line-height: 1.5;
      white-space: pre-wrap;
      word-wrap: break-word;
      margin-bottom: 4px;
    }

    .msg-time {
      font-size: 10px;
      opacity: 0.6;
      text-align: right;
    }

    .prediction-card {
      background: linear-gradient(135deg, #fff7e6 0%, #fff 100%);
      border: 1px solid #ffd666;
      border-radius: 12px;
      padding: 12px;
      margin-top: 8px;
      animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-10px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .prediction-header {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 600;
      color: #fa8c16;
      margin-bottom: 8px;
      font-size: 13px;
    }

    .prediction-content {
      font-size: 13px;
      color: #595959;
      line-height: 1.5;
    }

    .tour-card {
      background: linear-gradient(135deg, #f6f9fc 0%, #ffffff 100%);
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px;
      margin-top: 8px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .tour-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-color: #20ab7aff;
    }

    .tour-card-header {
      display: flex;
      gap: 10px;
      margin-bottom: 8px;
    }

    .tour-card-img {
      width: 80px;
      height: 60px;
      border-radius: 8px;
      object-fit: cover;
      flex-shrink: 0;
    }

    .tour-card-info {
      flex: 1;
    }

    .tour-card-title {
      font-weight: 600;
      font-size: 13px;
      color: #2d3748;
      margin-bottom: 4px;
      line-height: 1.3;
    }

    .tour-card-meta {
      display: flex;
      gap: 10px;
      font-size: 11px;
      color: #718096;
    }

    .tour-card-meta span {
      display: flex;
      align-items: center;
      gap: 3px;
    }

    .tour-card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 8px;
      border-top: 1px solid #e2e8f0;
    }

    .tour-card-price {
      font-size: 15px;
      font-weight: 700;
      color: #20ab7aff;
    }

    .tour-card-btn {
      background: linear-gradient(135deg, #20ab7aff, #15a04fff);
      color: white;
      border: none;
      padding: 6px 14px;
      border-radius: 8px;
      font-size: 12px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .tour-card-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 8px rgba(32, 171, 122, 0.3);
    }

    .typing-indicator {
      display: flex;
      gap: 4px;
      padding: 10px;
    }

    .typing-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #667eea;
      animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
      0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
      30% { transform: translateY(-8px); opacity: 1; }
    }

    .quick-suggestions {
      padding: 12px 16px;
      background: white;
      border-top: 1px solid #e2e8f0;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .quick-title {
      font-size: 11px;
      color: #718096;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .suggestions-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .suggestion-btn {
      background: #edf2f7;
      border: none;
      padding: 6px 12px;
      border-radius: 12px;
      font-size: 12px;
      cursor: pointer;
      transition: all 0.2s;
      color: #4a5568;
    }

    .suggestion-btn:hover {
      background: linear-gradient(135deg, #29b26bff, #2eb670ff);
      color: white;
      transform: translateY(-1px);
    }

    .input-area {
      padding: 16px;
      background: white;
      border-top: 1px solid #e2e8f0;
    }

    .input-wrapper {
      display: flex;
      gap: 8px;
      align-items: flex-end;
    }

    .input-field {
      flex: 1;
      position: relative;
    }

    textarea {
      width: 100%;
      padding: 10px 12px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 14px;
      font-family: inherit;
      resize: none;
      transition: all 0.2s;
      max-height: 100px;
    }

    textarea:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .send-btn {
      background: linear-gradient(135deg, #30a168ff, #25ac49ff);
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      flex-shrink: 0;
    }

    .send-btn:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }

    .send-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .send-btn svg {
      width: 20px;
      height: 20px;
      color: white;
    }

    @media (max-width: 480px) {
      .chat-window {
        width: calc(100vw - 32px);
        height: calc(100vh - 120px);
        bottom: 90px;
      }

      .chat-bubble-wrapper {
        bottom: 16px;
        right: 16px;
      }
    }
  </style>
</head>
<body>

  <div class="chat-bubble-wrapper">
    <button class="chat-bubble-btn" id="chatBubbleBtn">
      <svg class="chat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="6" width="16" height="12" rx="4"/>
        <line x1="12" y1="2" x2="12" y2="6"/>
        <circle cx="12" cy="2" r="1.5"/>
        <circle cx="9" cy="12" r="2"/>
        <circle cx="15" cy="12" r="2"/>
        <path d="M9 16c1.5 1 4.5 1 6 0"/>
      </svg>
      <svg class="close-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
      <span class="notification-badge" id="notificationBadge">1</span>
    </button>

    <div class="chat-window" id="chatWindow">
      <div class="chat-header">
        <div class="header-info">
          <div class="header-avatar">✈️</div>
          <div class="header-text">
            <h3>AI Travel Assistant</h3>
            <p>🤖 Tìm Các địa điểm ở khu vực</p>
          </div>
        </div>
        <div class="header-actions">
          <button class="header-btn" id="clearBtn" title="Xóa chat">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
          <button class="header-btn" id="minimizeBtn" title="Thu nhỏ">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </div>
      </div>

      <div class="messages-area" id="messagesArea">
        <div class="message bot">
          <div class="msg-avatar">🤖</div>
          <div class="msg-bubble">
            <div class="msg-text">Xin chào! 👋 Tôi có thể giúp gì cho bạn hay không.
</div>
            <div class="msg-time" id="welcomeTime"></div>
          </div>
        </div>
      </div>

      <div class="input-area">
        <div class="input-wrapper">
          <div class="input-field">
            <textarea id="chatInput" placeholder="Tìm tour du lịch..." rows="1"></textarea>
          </div>
          <button class="send-btn" id="sendBtn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>

const chatBubbleBtn = document.getElementById("chatBubbleBtn");
const chatWindow = document.getElementById("chatWindow");
const messagesArea = document.getElementById("messagesArea");
const chatInput = document.getElementById("chatInput");
const sendBtn = document.getElementById("sendBtn");

// ==== Bật / tắt cửa sổ ====
chatBubbleBtn.addEventListener("click", () => {
  chatBubbleBtn.classList.toggle("active");
  chatWindow.classList.toggle("active");
});

// ==== Gửi tin nhắn ====
sendBtn.addEventListener("click", () => sendMessage());
chatInput.addEventListener("keydown", (e) => {
  if (e.key === "Enter" && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

function sendMessage() {
  const text = chatInput.value.trim();
  if (!text) return;

  // ==== Thêm tin nhắn user lên giao diện ====
  appendMessage("user", text);
  chatInput.value = "";

  // ==== Gửi lên API PHP ====
  fetch("travel_ai.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ message: text })
  })
  .then(res => res.json())
  .then(data => {
    appendMessage("bot", data.reply);
  })
  .catch(() => {
    appendMessage("bot", "❌ Lỗi kết nối API!");
  });
}

// ==== Hàm thêm tin nhắn ====
function appendMessage(sender, text) {
  const msg = document.createElement("div");
  msg.className = "message " + sender;

  msg.innerHTML = `
    <div class="msg-avatar">${sender === "bot" ? "🤖" : "🧑"}</div>
    <div class="msg-bubble">
      <div class="msg-text">${text}</div>
    </div>
  `;

  messagesArea.appendChild(msg);
  messagesArea.scrollTop = messagesArea.scrollHeight;
}


  </script>

</body>
</html>