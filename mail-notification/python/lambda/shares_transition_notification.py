# fetch shares data in Rakuten Shoken using API to send notification mail.
import requests
import investment_shares as investment_shares

def index(event, context):
    shares = event["shares"]
    timeschedule = event["time"]
    mail_address = event["mail_address"]
    
    get_shares_money_data(shares, timeschedule, mail_address)


def get_shares_money_data(shares, timeschedule, mail_address):

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
    
    try:
        response = requests.get(shares, headers={"Authorization": f"Bearer {mail_address}"})
        response.raise_for_status()  # Raise an error for HTTP errors
        return response.json()
    except requests.RequestException as e:
        print(f"Error fetching shares data: {e}")
        return {"error": str(e)}


