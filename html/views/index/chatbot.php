<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AI Travel Chat Bubble</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    /* Chat Bubble Button */
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
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    .chat-bubble-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 12px 32px rgba(18, 154, 99, 0.5);
    }

    .chat-bubble-btn.active {
      animation: none;
    }

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
      background: #ff4757;
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
      animation: bounce 0.5s ease infinite alternate;
    }

    @keyframes bounce {
      from { transform: translateY(0); }
      to { transform: translateY(-4px); }
    }

    .notification-badge.hidden { display: none; }

    /* Chat Window */
    .chat-window {
      position: absolute;
      bottom: 84px;
      right: 0;
      width: 380px;
      max-width: calc(100vw - 48px);
      height: 580px;
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

    /* Header */
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

    /* Messages Area */
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
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
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

    /* Typing Indicator */
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

    /* Quick Suggestions */
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

    /* Input Area */
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

    /* Mobile Responsive */
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

  <!-- Chat Bubble -->
  <div class="chat-bubble-wrapper">
    <button class="chat-bubble-btn" id="chatBubbleBtn">
      <svg class="chat-icon" xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 24 24"
     fill="none"
     stroke="white"
     stroke-width="2"
     stroke-linecap="round"
     stroke-linejoin="round">
  <!-- Head -->
  <rect x="4" y="6" width="16" height="12" rx="4"/>
  <!-- Antenna -->
  <line x1="12" y1="2" x2="12" y2="6"/>
  <circle cx="12" cy="2" r="1.5"/>
  <!-- Eyes -->
  <circle cx="9" cy="12" r="2"/>
  <circle cx="15" cy="12" r="2"/>
  <!-- Smile -->
  <path d="M9 16c1.5 1 4.5 1 6 0"/>
</svg>

      <svg class="close-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
      <span class="notification-badge" id="notificationBadge">1</span>
    </button>

    <!-- Chat Window -->
    <div class="chat-window" id="chatWindow">
      <!-- Header -->
      <div class="chat-header">
        <div class="header-info">
          <div class="header-avatar">✈️</div>
          <div class="header-text">
            <h3>AI Travel Assistant</h3>
            <p>🤖 Trợ lý du lịch thông minh</p>
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

      <!-- Messages Area -->
      <div class="messages-area" id="messagesArea">
        <div class="message bot">
          <div class="msg-avatar">🤖</div>
          <div class="msg-bubble">
            <div class="msg-text">Xin chào! 👋 Tôi là AI Travel Assistant được trang bị trí tuệ nhân tạo.

Tôi có thể giúp bạn:
🗺️ Tìm kiếm tour du lịch
🏨 Gợi ý khách sạn
📅 Lập lịch trình chi tiết
🌤️ Tra cứu thời tiết
💡 Tư vấn địa điểm, ẩm thực

Bạn muốn khám phá điều gì?</div>
            <div class="msg-time" id="welcomeTime"></div>
          </div>
        </div>
      </div>

      <!-- Quick Suggestions -->
      <div class="quick-suggestions" id="quickSuggestions">
        <div class="quick-title">💡 Gợi ý nhanh</div>
        <div class="suggestions-grid">
          <button class="suggestion-btn">Tour Đà Lạt 3N2Đ</button>
          <button class="suggestion-btn">Lịch trình miền Bắc</button>
          <button class="suggestion-btn">Resort Phú Quốc</button>
          <button class="suggestion-btn">Chi phí Sapa</button>
          <button class="suggestion-btn">Thời tiết Nha Trang</button>
          <button class="suggestion-btn">Ẩm thực Đà Nẵng</button>
        </div>
      </div>

      <!-- Input Area -->
      <div class="input-area">
        <div class="input-wrapper">
          <div class="input-field">
            <textarea 
              id="chatInput" 
              placeholder="Nhập câu hỏi..."
              rows="1"
            ></textarea>
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
    // Initialize
    const chatBubbleBtn = document.getElementById('chatBubbleBtn');
    const chatWindow = document.getElementById('chatWindow');
    const messagesArea = document.getElementById('messagesArea');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const notificationBadge = document.getElementById('notificationBadge');
    const minimizeBtn = document.getElementById('minimizeBtn');
    const clearBtn = document.getElementById('clearBtn');
    const quickSuggestions = document.getElementById('quickSuggestions');

    let conversationHistory = [];
    let isTyping = false;

    // Set welcome time
    document.getElementById('welcomeTime').textContent = getCurrentTime();

    // Toggle chat window
    chatBubbleBtn.addEventListener('click', () => {
      chatWindow.classList.toggle('active');
      chatBubbleBtn.classList.toggle('active');
      if (chatWindow.classList.contains('active')) {
        notificationBadge.classList.add('hidden');
        chatInput.focus();
      }
    });

    // Minimize
    minimizeBtn.addEventListener('click', () => {
      chatWindow.classList.remove('active');
      chatBubbleBtn.classList.remove('active');
    });

    // Clear chat
    clearBtn.addEventListener('click', () => {
      if (confirm('Bạn có chắc muốn xóa toàn bộ cuộc trò chuyện?')) {
        messagesArea.innerHTML = '';
        conversationHistory = [];
        addMessage('bot', 'Đã xóa lịch sử chat. Tôi sẵn sàng giúp bạn! 🌏');
        quickSuggestions.style.display = 'flex';
      }
    });

    // Quick suggestions
    document.querySelectorAll('.suggestion-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        chatInput.value = btn.textContent;
        chatInput.focus();
      });
    });

    // Auto-resize textarea
    chatInput.addEventListener('input', () => {
      chatInput.style.height = 'auto';
      chatInput.style.height = Math.min(chatInput.scrollHeight, 100) + 'px';
    });

    // Send message
    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });

    async function sendMessage() {
      const text = chatInput.value.trim();
      if (!text || isTyping) return;

      // Hide quick suggestions after first message
      quickSuggestions.style.display = 'none';

      // Add user message
      addMessage('user', text);
      chatInput.value = '';
      chatInput.style.height = 'auto';

      // Show typing indicator
      showTypingIndicator();

      // Get AI response
      const response = await getAIResponse(text);

      // Remove typing indicator and add response
      removeTypingIndicator();
      addMessage('bot', response);
    }

    function addMessage(sender, text) {
      const messageDiv = document.createElement('div');
      messageDiv.className = `message ${sender}`;
      
      const time = getCurrentTime();
      
      messageDiv.innerHTML = `
        <div class="msg-avatar">${sender === 'bot' ? '🤖' : '👤'}</div>
        <div class="msg-bubble">
          <div class="msg-text">${text}</div>
          <div class="msg-time">${time}</div>
        </div>
      `;
      
      messagesArea.appendChild(messageDiv);
      messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function showTypingIndicator() {
      isTyping = true;
      sendBtn.disabled = true;
      
      const typingDiv = document.createElement('div');
      typingDiv.className = 'message bot';
      typingDiv.id = 'typingIndicator';
      typingDiv.innerHTML = `
        <div class="msg-avatar">🤖</div>
        <div class="msg-bubble">
          <div class="typing-indicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
          </div>
        </div>
      `;
      
      messagesArea.appendChild(typingDiv);
      messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function removeTypingIndicator() {
      const indicator = document.getElementById('typingIndicator');
      if (indicator) indicator.remove();
      isTyping = false;
      sendBtn.disabled = false;
    }

    async function getAIResponse(userMessage) {
      try {
        const systemPrompt = `Bạn là trợ lý du lịch AI chuyên nghiệp tên "AI Travel Assistant", chuyên về du lịch Việt Nam và quốc tế.

Nhiệm vụ của bạn:
- Tư vấn tour du lịch, địa điểm tham quan
- Đề xuất khách sạn, resort theo ngân sách
- Lập lịch trình chi tiết theo số ngày
- Cung cấp thông tin về thời tiết, văn hóa, ẩm thực
- Tư vấn giá cả, chi phí ước tính
- Gợi ý hoạt động phù hợp với từng loại du khách

Phong cách:
- Thân thiện, nhiệt tình, ngắn gọn
- Trả lời chi tiết nhưng súc tích (tối đa 150 từ)
- Sử dụng emoji phù hợp
- Đưa ra lời khuyên thực tế
- Luôn hỏi lại nếu cần thêm thông tin

Trả lời bằng tiếng Việt.`;

        const newHistory = [
          ...conversationHistory,
          { role: "user", content: userMessage }
        ];

        const response = await fetch("https://api.anthropic.com/v1/messages", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            model: "claude-sonnet-4-20250514",
            max_tokens: 1000,
            system: systemPrompt,
            messages: newHistory
          })
        });

        if (!response.ok) {
          throw new Error('API request failed');
        }

        const data = await response.json();
        const aiReply = data.content
          .filter(item => item.type === "text")
          .map(item => item.text)
          .join("\n");

        const updatedHistory = [
          ...newHistory,
          { role: "assistant", content: aiReply }
        ];
        conversationHistory = updatedHistory;

        return aiReply;
      } catch (error) {
        console.error('AI Error:', error);
        return "Xin lỗi, tôi đang gặp chút vấn đề kỹ thuật. Bạn có thể thử lại không? 🙏";
      }
    }

    function getCurrentTime() {
      return new Date().toLocaleTimeString('vi-VN', { 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    }
  </script>

</body>
</html>