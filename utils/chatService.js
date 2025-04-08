import axios from 'axios';
import { getEnvVar, ENV_KEYS } from './env';

// Configuration
const TIMEOUT_MS = 10000;
const RETRY_DELAY_MS = 500;
const MAX_RETRIES = 2;

// Supported providers
export const ChatProvider = {
    Mistral: 'mistral',
    LocalLLM: 'local'
};

/**
 * Custom error type for chat errors.
 */
export class ChatError extends Error {
    /**
     * @param {string} message - Error message
     * @param {string} provider - The provider responsible for the error
     * @param {any} [originalError] - The original error
     */
    constructor(message, provider, originalError) {
        super(message);
        this.name = 'ChatError';
        this.provider = provider;
        this.originalError = originalError;
    }
}

/**
 * System prompt template.
 */
const SYSTEM_PROMPT =
    "Je bent de professionele AI-assistent van ApotheCare. Je communiceert in een zakelijke en vriendelijke toon in het Nederlands. Je helpt klanten met vragen over medicijnen, gezondheidsadvies en onze online apotheekdiensten. Je gebruikt formele 'u' in plaats van informele 'je'. Je geeft duidelijke en accurate informatie, maar herinnert gebruikers er altijd aan om voor medisch advies een zorgprofessional te raadplegen. Je houdt je antwoorden beknopt en professioneel.";

/**
 * Call the local LLM API.
 *
 * @param {Object} request
 * @param {string} request.message
 * @param {string} [request.context]
 * @param {number} [request.timeout]
 * @returns {Promise<string>}
 */
const callLocalLLM = async (request) => {
    try {
        const apiUrl = getEnvVar('VITE_API_URL');
        if (!apiUrl) {
            throw new ChatError('API URL is not configured', ChatProvider.LocalLLM);
        }

        const response = await axios.post(
            `${apiUrl}/chat`,
            {
                message: request.message,
                context: request.context || SYSTEM_PROMPT,
            },
            {
                timeout: request.timeout || TIMEOUT_MS,
            }
        );

        if (!response.data || !response.data.response) {
            throw new ChatError('Invalid response from local LLM', ChatProvider.LocalLLM);
        }

        return response.data.response;
    } catch (error) {
        if (axios.isAxiosError(error)) {
            throw new ChatError(
                `Local LLM API error: ${error.response?.data?.error || error.message}`,
                ChatProvider.LocalLLM,
                error
            );
        }
        throw new ChatError('Unknown error from local LLM', ChatProvider.LocalLLM, error);
    }
};

/**
 * Get API key from environment variables.
 *
 * @returns {string|null}
 */
const getAPIKey = () => {
    const mistralApiKey = getEnvVar(ENV_KEYS.MISTRAL_API_KEY);
    if (
        mistralApiKey &&
        !mistralApiKey.includes('YOUR_') &&
        !mistralApiKey.includes('your_')
    ) {
        return mistralApiKey;
    }
    return null;
};

/**
 * Call the Mistral AI API.
 *
 * @param {Object} request
 * @param {string} request.message
 * @param {string} [request.context]
 * @param {Array<{ role: string, content: string }>} [request.history]
 * @param {number} [request.timeout]
 * @returns {Promise<string>}
 */
const callMistralAPI = async (request) => {
    try {
        const mistralApiKey = getAPIKey();

        if (!mistralApiKey) {
            throw new ChatError('Mistral API key is missing', ChatProvider.Mistral);
        }

        const response = await axios.post(
            'https://api.mistral.ai/v1/chat/completions',
            {
                model: 'mistral-tiny',
                messages: [
                    {
                        role: 'system',
                        content: request.context || SYSTEM_PROMPT,
                    },
                    ...(request.history || []),
                    {
                        role: 'user',
                        content: request.message,
                    },
                ],
                temperature: 0.7,
                max_tokens: 500,
            },
            {
                headers: {
                    Authorization: `Bearer ${mistralApiKey}`,
                    'Content-Type': 'application/json',
                },
                timeout: request.timeout || TIMEOUT_MS,
            }
        );

        const content = response.data?.choices?.[0]?.message?.content;
        if (!content) {
            throw new ChatError('Invalid response from Mistral API', ChatProvider.Mistral);
        }

        return content;
    } catch (error) {
        if (axios.isAxiosError(error)) {
            throw new ChatError(
                `Mistral API error: ${error.response?.data?.error || error.message}`,
                ChatProvider.Mistral,
                error
            );
        }
        throw new ChatError('Unknown error from Mistral API', ChatProvider.Mistral, error);
    }
};

/**
 * Try to get a response with retry logic.
 *
 * @param {Object} request
 * @param {number} [retryCount=0]
 * @returns {Promise<{ response: string, provider: string }>}
 */
const tryWithRetry = async (request, retryCount = 0) => {
    try {
        const response = await callMistralAPI(request);
        return { response, provider: ChatProvider.Mistral };
    } catch (error) {
        if (retryCount < MAX_RETRIES) {
            await new Promise((resolve) => setTimeout(resolve, RETRY_DELAY_MS));
            return tryWithRetry(request, retryCount + 1);
        }

        try {
            const localResponse = await callLocalLLM(request);
            return { response: localResponse, provider: ChatProvider.LocalLLM };
        } catch (localError) {
            throw localError;
        }
    }
};

/**
 * Main chat service function.
 */
export const chatService = {
    /**
     * Send a message using the chat service.
     *
     * @param {Object} request
     * @param {string} request.message - User's message
     * @param {string} [request.context] - Optional context or system prompt override
     * @param {Array<{ role: string, content: string }>} [request.history] - Chat history, if any
     * @param {number} [request.timeout] - Request timeout in milliseconds
     * @returns {Promise<{ response: string, provider: string }>}
     */
    async sendMessage(request) {
        try {
            return await tryWithRetry(request);
        } catch (error) {
            return {
                response:
                    "Excuses, er is een probleem met onze AI-diensten. Probeer het later opnieuw of neem contact op met onze klantenservice.",
                provider: ChatProvider.LocalLLM,
            };
        }
    },
};
