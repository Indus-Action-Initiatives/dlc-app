<template>
    <div>
        <!-- Floating Chatbot Button -->
        <v-btn class="chatbot-btn" color="primary" icon size="large" @click="toggleChat">
            <v-icon>mdi-chat</v-icon>
        </v-btn>

        <!-- Chatbot Dialog -->
        <v-dialog v-model="chatOpen" persistent width="380">
            <v-card class="pa-3">
                <v-card-title class="d-flex justify-space-between align-center">
                    <div class="text-h6">💬 Labour Chowk Chatbot</div>
                    <v-btn icon small @click="chatOpen = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>

                <v-divider class="mb-2"></v-divider>

                <!-- Chat Messages -->
                <div class="chat-area">
                    <div v-for="(msg, i) in messages" :key="i" class="chat-bubble" :class="msg.sender">
                        <strong v-if="msg.sender === 'bot'">🤖</strong>
                        <span>{{ msg.text }}</span>
                    </div>
                </div>

                <!-- FAQ Quick Buttons -->
                <v-divider class="my-2"></v-divider>
                <div class="faq-buttons">
                    <v-btn v-for="(faq, i) in faqs" :key="i" variant="tonal" size="small" class="my-1 text-truncate"
                        style="width: 100%;" @click="askQuestion(faq.question)">
                        {{ faq.question }}
                    </v-btn>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "ChatBot",
    data() {
        return {
            chatOpen: false,
            messages: [],
            faqs: [
                {
                    question: "What is the Digital Labour Chowk app?",
                    answer:
                        "It’s a government platform that connects workers and employers. You can find or post jobs, update your profile, and access welfare schemes — all in one place.",
                },
                {
                    question: "How can I register as a worker?",
                    answer:
                        "Go to the “I am a Worker” tab → tap “Register” → enter your mobile number → verify with OTP → fill skill and location details → submit.",
                },
                {
                    question: "How can I post a job as an employer?",
                    answer:
                        "Tap “I am an Employer” → choose “Post a Job” → enter job type, number of workers needed, and location → submit. Workers nearby will get notified instantly.",
                },
                {
                    question: "How do I know if my job or profile is visible?",
                    answer:
                        "Once your registration or job post is submitted, you’ll see a green “Available” status. You can edit or deactivate anytime from your dashboard.",
                },
                {
                    question: "How can I access welfare schemes or benefits?",
                    answer:
                        "Tap the “Welfare & Schemes” icon on the profile screen → view all eligible schemes → tap “Know More” to visit the official site.",
                },
            ],
        };
    },
    mounted() {
        this.resetChat();
    },
    methods: {
        toggleChat() {
            this.chatOpen = !this.chatOpen;
            if (this.chatOpen) {
                this.resetChat(); // 🔹 Reset every time user opens the chat
            }
        },
        resetChat() {
            this.messages = [
                {
                    text: "👋 Hi! I'm your Labour Chowk assistant. How can I help you today?",
                    sender: "bot",
                },
            ];
        },
        askQuestion(question) {
            const faq = this.faqs.find((f) => f.question === question);
            if (!faq) return;

            this.messages.push({ text: question, sender: "user" });
            setTimeout(() => {
                this.messages.push({ text: faq.answer, sender: "bot" });
            }, 400);
        },
    },
};
</script>

<style scoped>
.chatbot-btn {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1000;
}

.chat-area {
    max-height: 250px;
    overflow-y: auto;
    padding: 8px;
    background-color: #f8f9fa;
    border-radius: 6px;
    font-size: 0.9rem;
}

.chat-bubble {
    margin: 6px 0;
    padding: 8px 10px;
    border-radius: 8px;
    max-width: 90%;
    line-height: 1.3;
}

.chat-bubble.bot {
    background-color: #eceff1;
    align-self: flex-start;
}

.chat-bubble.user {
    background-color: #ffe0b2;
    align-self: flex-end;
    text-align: right;
    margin-left: auto;
}

.faq-buttons {
    display: flex;
    flex-direction: column;
    max-height: 150px;
    overflow-y: auto;
}

.faq-buttons .v-btn {
    text-align: left;
    font-size: 9px;
    color: black;
}
</style>
