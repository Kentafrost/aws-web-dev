# fetch shares data in Rakuten Shoken using API to send notification mail.
import requests
import investment_shares as investment_shares

def fetch_shares_data(api_url, headers):
    try:
        response = requests.get(api_url, headers=headers)
        response.raise_for_status()  # Raise an error for HTTP errors
        return response.json()
    except requests.RequestException as e:
        print(f"Error fetching shares data: {e}")
        return {"error": str(e)}


target_urls = [
    "https://api.rakuten.co.jp/shoken/v1/shares/[0]",
    "https://api.rakuten.co.jp/shoken/v1/shares/[1]",
    "https://api.rakuten.co.jp/shoken/v1/shares/[2]"
]

headers = {
    "Authorization": "Bearer YOUR_ACCESS_TOKEN",  # Required for OAuth2-based APIs
    "Accept": "application/json",                 # Ensures response is in JSON
    "Content-Type": "application/json"            # Needed for POST/PUT requests
}

for target_url in target_urls:
    data = fetch_shares_data(target_url, headers)
    print(data)