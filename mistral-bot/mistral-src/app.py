from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import requests
from dotenv import load_dotenv

# Laad environment variables
load_dotenv()

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

# Systeem prompt voor de AI assistent
SYSTEM_PROMPT = """Je bent een professionele en vriendelijke AI assistent voor ApotheCare, een online apotheek. 
Je rol is om klanten te helpen met hun vragen over gezondheid, medicijnen en apotheekproducten.

Belangrijke richtlijnen:
1. Blijf altijd professioneel en respectvol
2. Geef geen medisch advies - verwijs hiervoor naar een arts
3. Focus op productinformatie, bestellingen en algemene vragen
4. Als je iets niet weet, verwijs naar de klantenservice
5. Houd antwoorden beknopt en duidelijk
6. Gebruik een vriendelijke maar professionele toon
7. Stel vervolgvragen om de klant beter te kunnen helpen

Voorbeelden van goede antwoorden:
- "Ik kan je helpen met informatie over onze producten. Wat specifiek wil je weten?"
- "Voor medisch advies raad ik je aan contact op te nemen met je huisarts."
- "Ik kan je helpen met je bestelling. Heb je een bestelnummer?"

Ongepaste vragen:
- Geen medisch advies geven
- Geen persoonlijke gezondheidsinformatie delen
- Geen illegale of onethische activiteiten ondersteunen"""

def get_ai_response(messages):
    """Haal een response op van de Mistral AI API"""
    try:
        api_key = os.getenv('MISTRAL_API_KEY')
        if not api_key:
            raise ValueError("MISTRAL_API_KEY is niet geconfigureerd in .env bestand")

        headers = {
            "Authorization": f"Bearer {api_key}",
            "Content-Type": "application/json"
        }

        # Voeg systeem prompt toe aan de messages
        messages_with_system = [
            {"role": "system", "content": SYSTEM_PROMPT},
            *messages
        ]

        data = {
            "model": "mistral-tiny",
            "messages": messages_with_system,
            "temperature": 0.7,
            "max_tokens": 500
        }

        response = requests.post(
            "https://api.mistral.ai/v1/chat/completions",
            headers=headers,
            json=data
        )
        
        if response.status_code != 200:
            print(f"API Error: {response.status_code} - {response.text}")
            return "Er is een probleem opgetreden bij het verwerken van je vraag. Probeer het later opnieuw."

        return response.json()["choices"][0]["message"]["content"]
    except Exception as e:
        print(f"Error in get_ai_response: {str(e)}")
        return "Er is een probleem opgetreden bij het verwerken van je vraag. Probeer het later opnieuw."

@app.route('/v1/chat/completions', methods=['POST', 'OPTIONS'])
def chat_completions():
    if request.method == 'OPTIONS':
        return '', 200
        
    try:
        data = request.json
        print("Received request:", data)  # Debug log
        
        # Haal de messages op uit de request
        messages = data.get('messages', [])
        if not messages:
            return jsonify({"error": "No messages provided"}), 400

        # Haal AI response op
        response_content = get_ai_response(messages)
        
        response = {
            "choices": [
                {
                    "message": {
                        "role": "assistant",
                        "content": response_content
                    }
                }
            ]
        }
        
        print("Sending response:", response)  # Debug log
        return jsonify(response)
        
    except Exception as e:
        print("Error:", str(e))  # Debug log
        return jsonify({"error": str(e)}), 400

if __name__ == '__main__':
    print("Starting ApotheCare Customer Service Bot on port 7860...")  # Debug log
    app.run(host='0.0.0.0', port=7860, debug=True) 